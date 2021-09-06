<?php

namespace FooDBar\Jobs;

require __DIR__ . "/../../vendor/reinerlanz/frame/src/Job.php";

use \Frame\Job as Job;

use \Frame\Fields               as Fields;
use \Frame\Join                 as Join;
use \Frame\Condition            as Condition;
use \Frame\Order                as Order;
use \Frame\GroupBy              as GroupBy;
use \Frame\DBFunction           as DBFunction;
use \Frame\DBFunctionExpression as DBFunctionExpression;

use \FooDBar\StorageModel                               as StorageModel;
use \FooDBar\StorageConsumptionModel                    as StorageConsumptionModel;
use \FooDBar\RecipeConsumptionGroupModel                as RecipeConsumptionGroupModel;
use \FooDBar\RecipeConsumptionGroupAllergiesModel       as RecipeConsumptionGroupAllergiesModel;
use \FooDBar\RecipeConsumptionGroupAggModel             as RecipeConsumptionGroupAggModel;
use \FooDBar\ProductsModel                              as ProductsModel;
use \FooDBar\ProductsMatrixModel                        as ProductsMatrixModel;

use \FooDBar\App\StatusController as StatusController;
use \FooDBar\Allergies\AllergyController as AllergyController;
use \FooDBar\Products as Products;
use \FooDBar\Storage as Storage;

class UpdateProcessConsumptionJob extends Job {
	const APP_STATUS_PROCESS_CONSUMPTION = "PROCESS_CONSUMPTION";

	public static function getProductsIds($datefrom, $dateto) {
        	$recipe_user_condition = new Condition("[c1] AND [c2] AND [c3]", array(
	                "[c1]" => [
                                	[StorageConsumptionModel::class, StorageConsumptionModel::FIELD_USERS_ID],
                        	        Condition::COMPARISON_GREATER,
                	                [Condition::CONDITION_CONST, 0]
        	                ],
	                "[c2]" => [
                                	[StorageConsumptionModel::class, StorageConsumptionModel::FIELD_DATETIME],
                        	        Condition::COMPARISON_LESS_EQUALS,
                	                [Condition::CONDITION_CONST, $dateto]
        	                ],
	                "[c3]" => [
                                	[StorageConsumptionModel::class, StorageConsumptionModel::FIELD_DATETIME],
                        	        Condition::COMPARISON_GREATER,
                	                [Condition::CONDITION_CONST, $datefrom]
        	                ]
	        ));

        	$storage_join = new Join(new StorageModel(), "[j1]", array(
	                "[j1]" => [
                                	[StorageConsumptionModel::class, StorageConsumptionModel::FIELD_STORAGE_ID],
                        	        Condition::COMPARISON_EQUALS,
                	                [StorageModel::class, StorageModel::FIELD_ID]
        	                ]
	        ));

        	$fields = new Fields(array());
	        $fields->addField(StorageModel::class, StorageModel::FIELD_PRODUCTS_ID);

	        $group_by_p = new GroupBy(StorageModel::class, StorageModel::FIELD_PRODUCTS_ID);

        	$consumption = new StorageConsumptionModel();
	        $consumption->find($recipe_user_condition, array($storage_join), null, null, $fields, array($group_by_p));

        	$result = array();
	        while ($consumption->next()) {
        	        $result[] = $consumption->joinedModelByClass(StorageModel::class)->getProductsId();
		}

	        return $result;
	}

	public static function getConsumptionGroups($datefrom, $dateto) {
	        $recipe_user_condition = new Condition("[c1] AND [c2] AND [c3]", array(
        	        "[c1]" => [
	                                [StorageConsumptionModel::class, StorageConsumptionModel::FIELD_USERS_ID],
        	                        Condition::COMPARISON_GREATER,
	               	                [Condition::CONDITION_CONST, 0]
        	                ],
	                "[c2]" => [
        	                        [StorageConsumptionModel::class, StorageConsumptionModel::FIELD_DATETIME],
                	                Condition::COMPARISON_LESS_EQUALS,
                        	        [Condition::CONDITION_CONST, $dateto]
	                        ],
        	        "[c3]" => [
                	                [StorageConsumptionModel::class, StorageConsumptionModel::FIELD_DATETIME],
                        	        Condition::COMPARISON_GREATER,
                                	[Condition::CONDITION_CONST, $datefrom]
	                        ]
        	));

	        $storage_join = new Join(new StorageModel(), "[j1]", array(
        	        "[j1]" => [
                	                [StorageConsumptionModel::class, StorageConsumptionModel::FIELD_STORAGE_ID],
                        	        Condition::COMPARISON_EQUALS,
                                	[StorageModel::class, StorageModel::FIELD_ID]
	                        ]
        	));

	        $gc_expr_p = new DBFunctionExpression("[e1]", array(
        	        "[e1]" => [StorageModel::class, StorageModel::FIELD_PRODUCTS_ID]
	        ));
        	$gc_expr_a = new DBFunctionExpression("[e2]", array(
	                "[e2]" => [StorageConsumptionModel::class, StorageModel::FIELD_AMOUNT]
        	));

	        $gc_expr_order = new DBFunctionExpression("[e3]", array(
        	        "[e3]" => [StorageModel::class, StorageModel::FIELD_PRODUCTS_ID]
	        ));

        	$fields = new Fields(array());
	        $fields->addFunctionField("ProductsIds", DBFunction::FUNCTION_GROUP_CONCAT, array($gc_expr_p, $gc_expr_order));
        	$fields->addFunctionField("Amounts", DBFunction::FUNCTION_GROUP_CONCAT, array($gc_expr_a, $gc_expr_order));
	        $fields->addField(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_DATETIME);
        	$fields->addField(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_USERS_ID);

	        $group_by_d = new GroupBy(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_DATETIME);
        	$group_by_u = new GroupBy(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_USERS_ID);

	        $order_by = new Order(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_USERS_ID, Order::ORDER_ASC);

        	$consumption = new StorageConsumptionModel();
	        $consumption->find($recipe_user_condition, array($storage_join), $order_by, null, $fields, array($group_by_d, $group_by_u));

		$result = new \stdClass();
	        while ($consumption->next()) {
        	        $products_ids = $consumption->DBFunctionResult("ProductsIds");
                	$amounts = $consumption->DBFunctionResult("Amounts");

	                $products_ids_arr = explode(";", $products_ids);
        	        $amounts_arr = explode(";", $amounts);
	                for ($p = count($products_ids_arr) - 1; $p >= 1; $p--) {
        	                if ($products_ids_arr[$p] == $products_ids_arr[$p-1]) {
                	                unset($products_ids_arr[$p]);
                        	        $amounts_arr[$p-1] += $amounts_arr[$p];
                                	unset($amounts_arr[$p]);
	                        }
        	        }
	                $products_ids = implode(";", $products_ids_arr);
        	        $amounts = implode(";", $amounts_arr);

	                $h = hash('sha256', $products_ids . "_" . $consumption->getUsersId());

        	        if (!isset($result->{$h})) $result->{$h} = array();

	                $result->{$h}[] = array(
        	                "UsersId"       => $consumption->getUsersId(),
                	        "ProductsIds"   => $products_ids,
                        	"Amounts"       => $amounts,
	                        "Datetime"      => $consumption->getDatetime()
        	        );
	        }
        	return $result;
	}

	public static function calculateNutrition($amount, $product) {
        	$c_a = $amount;
	        $amount_type_id = $product[ProductsModel::FIELD_AMOUNT_TYPE_ID];
        	if ($amount_type_id == 1) { //g
	                $c_a /= 100;
        	} else if ($amount_type_id == 2) { //l
	                $c_a *= 10;
        	}

	        $kj_p           = $product[ProductsModel::FIELD_KJ];
	        $fat_p          = $product[ProductsModel::FIELD_N_FAT];
	        $carbs_p        = $product[ProductsModel::FIELD_N_CARBS];
	        $protein_p      = $product[ProductsModel::FIELD_N_PROTEIN];
	        $salt_p         = $product[ProductsModel::FIELD_N_SALT];
	        $fiber_p        = $product[ProductsModel::FIELD_N_FIBER];

        	$result = array(
	                "Kj"            => is_null($kj_p)       ? 0 : round($c_a * $kj_p, 2),
        	        "Fat"           => is_null($fat_p)      ? 0 : round($c_a * $fat_p, 2),
                	"Carbs"         => is_null($carbs_p)    ? 0 : round($c_a * $carbs_p, 2),
	                "Protein"       => is_null($protein_p)  ? 0 : round($c_a * $protein_p, 2),
	                "Salt"          => is_null($salt_p)     ? 0 : round($c_a * $salt_p, 2),
        	        "Fiber"         => is_null($fiber_p)    ? 0 : round($c_a * $fiber_p, 2)
        	);
	        return $result;
	}

	public static function getNutritionFromConsumptionGroups($consumption_groups, $products) {
        	$result = array();

	        foreach ($consumption_groups as $h => $cg_arr) {
        	        foreach ($cg_arr as $idx => $cg) {
                	        $products_arr = explode(";", $cg["ProductsIds"]);
	                        $amounts_arr = explode(";", $cg["Amounts"]);
        	                $contribution = array();
                	        for ($p = 0; $p < count($products_arr); $p++) {
                        	        $tmp = self::calculateNutrition($amounts_arr[$p], $products->{$products_arr[$p]});
	                                if ($p == 0) {
        	                                $result[$h . "_" . $idx] = $tmp;
                	                } else {
                        	                $result[$h . "_" . $idx]["Kj"] += $tmp["Kj"];
                                	        $result[$h . "_" . $idx]["Fat"] += $tmp["Fat"];
                                        	$result[$h . "_" . $idx]["Carbs"] += $tmp["Carbs"];
	                                        $result[$h . "_" . $idx]["Protein"] += $tmp["Protein"];
        	                                $result[$h . "_" . $idx]["Salt"] += $tmp["Salt"];
                	                        $result[$h . "_" . $idx]["Fiber"] += $tmp["Fiber"];
                        	        }
                                	$contribution[$p] = $tmp;
	                        }
        	                $result[$h . "_" . $idx]["Kj"]          = round($result[$h . "_" . $idx]["Kj"], 2);
                	        $result[$h . "_" . $idx]["Fat"]         = round($result[$h . "_" . $idx]["Fat"], 2);
                        	$result[$h . "_" . $idx]["Carbs"]       = round($result[$h . "_" . $idx]["Carbs"], 2);
	                        $result[$h . "_" . $idx]["Protein"]     = round($result[$h . "_" . $idx]["Protein"], 2);
        	                $result[$h . "_" . $idx]["Salt"]        = round($result[$h . "_" . $idx]["Salt"], 2);
                	        $result[$h . "_" . $idx]["Fiber"]       = round($result[$h . "_" . $idx]["Fiber"], 2);
                        	$result[$h . "_" . $idx]["p_parts"]     = $contribution;
	                }
        	}
	        return $result;
	}

	public static function getConsumptionGroupsAllergies($consumption_groups, $products) {
        	$GLOBALS['Boot']->loadModule("allergies", "Allergy");
	        $allergy_fields = AllergyController::getAllergyFields(new RecipeConsumptionGroupAllergiesModel());

        	$result = array();
	        foreach ($consumption_groups as $h => $cg_arr) {
        	        $idx = 0;
                	$cg = $cg_arr[0];
	                if (isset($result[$cg["ProductsIds"]])) continue;

        	        $result[$cg["ProductsIds"]] = new \stdClass();
                	foreach ($allergy_fields as $a => $field_name_camel) {
	                        $result[$cg["ProductsIds"]]->{$field_name_camel} = 0;
        	        }

	                $products_arr = explode(";", $cg["ProductsIds"]);
        	        for ($p = 0; $p < count($products_arr); $p++) {
                	        foreach ($allergy_fields as $a => $field_name_camel) {
                        	        $a_value = $products->{$products_arr[$p]}[$field_name_camel];
                                	if (!is_null($a_value) && $a_value == 1) {
	                                        $result[$cg["ProductsIds"]]->{$field_name_camel} = 1;
        	                        }
                	        }
	                }
        	}
	        return $result;
	}

	public static function updatePrices($users_ids, $date_from, $date_to) {
        	$result["products_ids"] = self::getProductsIds($date_from, $date_to);

	        $GLOBALS['Boot']->loadModule("products", "Index");
        	$result["products"] = Products\IndexController::getProductsByIdArray($result["products_ids"]);

	        $GLOBALS['Boot']->loadModule("products", "Price");
        	$result["products_minmax_prices"] = Products\PriceController::getMinMaxPriceByProductsIdArray($users_ids, $result["products_ids"], $date_to);

	        $cond = new Condition("[c1] AND [c2]", array(
        	        "[c1]" => [
                	                [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_DATETIME],
                        	        Condition::COMPARISON_LESS_EQUALS,
                                	[Condition::CONDITION_CONST, $date_to]
	                        ],
        	        "[c2]" => [
                	                [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_DATETIME],
                        	        Condition::COMPARISON_GREATER,
                                	[Condition::CONDITION_CONST, $date_from]
	                        ]
        	));

	        $recipe_consumption_group = new RecipeConsumptionGroupModel();
        	$recipe_consumption_group->find($cond);
	        while ($recipe_consumption_group->next()) {
        	        $products_arr = explode(";", $recipe_consumption_group->getProductsIds());
                	$amounts_arr = explode(";", $recipe_consumption_group->getAmounts());

	                $min_price = 0;
        	        $max_price = 0;
                	foreach ($products_arr as $idx => $p_id) {
	                        $min_price += $amounts_arr[$idx] / $result["products"]->{$p_id}[ProductsModel::FIELD_AMOUNT] * $result["products_minmax_prices"]->{$p_id}["MinPrice"];
	                        $max_price += $amounts_arr[$idx] / $result["products"]->{$p_id}[ProductsModel::FIELD_AMOUNT] * $result["products_minmax_prices"]->{$p_id}["MaxPrice"];
        	        }

	                $recipe_consumption_group->setMinPrice(round($min_price, 2));
        	        $recipe_consumption_group->setMaxPrice(round($max_price, 2));
                	$recipe_consumption_group->save();
	        }
	}

	public static function insertConsumptionGroupsWithAllergies($date_from, $date_to) {
		$result["products_ids"] = self::getProductsIds($date_from, $date_to);
	        $result["consumption_groups"] = self::getConsumptionGroups($date_from, $date_to);

	        $GLOBALS['Boot']->loadModule("products", "Index");
	        $result["products"] = Products\IndexController::getProductsByIdArray($result["products_ids"]);

	        $result["consumption_groups_nutrition"] = self::getNutritionFromConsumptionGroups($result["consumption_groups"], $result["products"]);
        	$result["consumption_groups_allergies"] = self::getConsumptionGroupsAllergies($result["consumption_groups"], $result["products"]);

	        $cga_ids = array();
        	foreach ($result["consumption_groups_allergies"] as $productsIds => $cga) {
                	$cga_model = new RecipeConsumptionGroupAllergiesModel();
	                AllergyController::setAllergyValues($cga_model, $cga, false);
        	        $cga_model->setProductsIds($productsIds);

	                $cga_model->insert();

        	        $cga_ids[$productsIds] = $cga_model->getId();
	        }

        	foreach ($result["consumption_groups"] as $h => $cg_arr) {
                	foreach ($cg_arr as $idx => $cg) {
	                        $cgn = $result["consumption_groups_nutrition"][$h . "_" . $idx];

				$cg_model = new RecipeConsumptionGroupModel();
	                        $cg_model->setUsersId($cg["UsersId"]);
        	                $cg_model->setProductsIds($cg["ProductsIds"]);
                	        $cg_model->setAmounts($cg["Amounts"]);
	                        $cg_model->setDatetime($cg["Datetime"]);

        	                $total_nutrition = $cgn["Fat"] + $cgn["Carbs"] + $cgn["Protein"] + $cgn["Fiber"] + $cgn["Salt"];

                	        $cg_model->setMj(round($cgn["Kj"]/1000, 5));
	                        $cg_model->setNFatPercent(round($cgn["Fat"]/$total_nutrition * 100, 2));
        	                $cg_model->setNCarbsPercent(round($cgn["Carbs"]/$total_nutrition * 100, 2));
                	        $cg_model->setNProteinPercent(round($cgn["Protein"]/$total_nutrition * 100, 2));
	                        $cg_model->setNFiberPercent(round($cgn["Fiber"]/$total_nutrition * 100, 2));
        	                $cg_model->setNSaltPercent(round($cgn["Salt"]/$total_nutrition * 100, 2));

                	        $cg_model->setRecipeConsumptionGroupAllergiesId($cga_ids[$cg["ProductsIds"]]);

	                        $cg_model->insert();
        	        }
	        }
    	}

	public static function insertConsumptionGroupAgg($users_id) {
		$cond = new Condition("[c1]", array(
			"[c1]" => [
				[RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_USERS_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $users_id]
			]
		));

	        $field_nut = array(
                        RecipeConsumptionGroupModel::FIELD_MJ,
                        RecipeConsumptionGroupModel::FIELD_N_FAT_PERCENT,
                        RecipeConsumptionGroupModel::FIELD_N_CARBS_PERCENT,
                        RecipeConsumptionGroupModel::FIELD_N_PROTEIN_PERCENT,
                        RecipeConsumptionGroupModel::FIELD_N_FIBER_PERCENT,
                        RecipeConsumptionGroupModel::FIELD_N_SALT_PERCENT
                        );
        	$functions = array(
                        "Min" => DBFunction::FUNCTION_MIN,
                        "Avg" => DBFunction::FUNCTION_AVG,
                        "Max" => DBFunction::FUNCTION_MAX
                        );

	        $fields = new Fields(array());

        	$min_id_expr = new DBFunctionExpression("[e1]", array(
                                "[e1]" => [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_ID]
                  ));
	        $fields->addFunctionField("RecipeConsumptionGroupId_" . DBFunction::FUNCTION_MIN, DBFunction::FUNCTION_MIN, $min_id_expr);
        	$fields->addFunctionField("RecipeConsumptionGroupCount_" . DBFunction::FUNCTION_COUNT, DBFunction::FUNCTION_COUNT, $min_id_expr);


	        $min_price_expr = new DBFunctionExpression("Round([e1]/[e2], 4)", array(
                                "[e1]" => [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_MIN_PRICE],
                                "[e2]" => [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_MJ]
        	));

	        $max_price_expr = new DBFunctionExpression("Round([e1]/[e2], 4)", array(
                                "[e1]" => [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_MAX_PRICE],
                                "[e2]" => [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_MJ]
        	));

	        $avg_price_expr = new DBFunctionExpression("Round(([e1] + 0.5 * ([e2] - [e1]))/[e3], 4)", array(
                                "[e1]" => [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_MIN_PRICE],
                                "[e2]" => [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_MAX_PRICE],
                                "[e3]" => [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_MJ]
        	));


	        $fields->addFunctionField("PricePerMjMin", DBFunction::FUNCTION_MIN, array($min_price_expr));
	        $fields->addFunctionField("PricePerMjMax", DBFunction::FUNCTION_MAX, array($max_price_expr));
        	$fields->addFunctionField("PricePerMjAvg", DBFunction::FUNCTION_AVG, array($avg_price_expr));


        	foreach ($field_nut as $idx => $field) {

                	$expr = new DBFunctionExpression("Round([e1], 2)", array(
                                "[e1]" => [RecipeConsumptionGroupModel::class, $field]
                        ));


	                foreach ($functions as $idy => $f) {
        	                $fields->addFunctionField($field . "_" . $f, $f, array($expr));
	                }
        	}

	        $group_by = new GroupBy(RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_PRODUCTS_IDS);

	        $rcg = new RecipeConsumptionGroupModel();
        	$rcg->find($cond, null, null, null, $fields, $group_by);
	        while ($rcg->next()) {
        	        $rcg_agg = new RecipeConsumptionGroupAggModel();
			$rcg_agg->setUsersId($users_id);
                	$rcg_agg->setRecipeConsumptionGroupId($rcg->DBFunctionResult("RecipeConsumptionGroupId_" . DBFunction::FUNCTION_MIN));
        	        $rcg_agg->setRecipeConsumptionGroupCount($rcg->DBFunctionResult("RecipeConsumptionGroupCount_" . DBFunction::FUNCTION_COUNT));
	                $rcg_agg->setPricePerMjMin($rcg->DBFunctionResult("PricePerMjMin"));
	                $rcg_agg->setPricePerMjAvg($rcg->DBFunctionResult("PricePerMjAvg"));
        	        $rcg_agg->setPricePerMjMax($rcg->DBFunctionResult("PricePerMjMax"));
                	foreach ($field_nut as $idx => $field) {
        	                foreach ($functions as $idy => $f) {
	                                $setter = "set" . $field . $idy;
                	                $rcg_agg->$setter($rcg->DBFunctionResult($field . "_" . $f));
                        	}
	                }
        	        $rcg_agg->insert();
	        }
	}

	public static function updateProductsMatrix($users_id) {
		$users_cond =  new Condition("[c1]", array(
                        "[c1]" => [
                                [RecipeConsumptionGroupAggModel::class, RecipeConsumptionGroupAggModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $users_id]
                        ]
                ));

        	$rcg_join = new Join(new RecipeConsumptionGroupModel(), "[j1]", array(
	                "[j1]" => [
        	                [RecipeConsumptionGroupAggModel::class, RecipeConsumptionGroupAggModel::FIELD_RECIPE_CONSUMPTION_GROUP_ID],
                	        Condition::COMPARISON_EQUALS,
                        	[RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_ID]
	                ]
        	));

	        $rcg_agg = new RecipeConsumptionGroupAggModel();
        	$rcg_agg->find($users_cond, $rcg_join);

	        $combination_matrix_by_p_id = array();
        	while ($rcg_agg->next()) {
	                //$count = $rcg_agg->getRecipeConsumptionGroupCount();

        	        $rcg = $rcg_agg->joinedModelByClass(RecipeConsumptionGroupModel::class);

	                $products_ids = explode(";", $rcg->getProductsIds());

        	        foreach ($products_ids as $p_id) {
                	        foreach ($products_ids as $p_id_i) {
	                                if ($p_id_i == $p_id) continue;
        	                        if (!isset($combination_matrix_by_p_id[$p_id])) {
                	                        $combination_matrix_by_p_id[$p_id] = array();
                        	        }
	                                if (!isset($combination_matrix_by_p_id[$p_id][$p_id_i])) {
        	                                $combination_matrix_by_p_id[$p_id][$p_id_i] = 1;
                	                } else {
                        	                $combination_matrix_by_p_id[$p_id][$p_id_i]++;
	                                }
        	                }
                	}
	        }

	        $pcm_models = array();
        	foreach ($combination_matrix_by_p_id as $p_id => $p_id_i_arr) {
                	$pcm = new ProductsMatrixModel();
			$pcm->setUsersId($users_id);

	                $pcm->setProductsId($p_id);

        	        $total = 0;

	                ksort($p_id_i_arr);
        	        $row = "";
                	foreach ($p_id_i_arr as $p_id_i => $count) {
                        	if (strlen($row) > 0) $row .= ";";
	                        $row .= $p_id_i . ":" . $count;
        	                $total += $count;
        	        }
	                $pcm->setCombinationRow($row);

                	$pcm->setCombinationTotal($total);

	                $pcm_models[$p_id] = $pcm;
        	}

	        foreach ($combination_matrix_by_p_id as $p_id => $p_id_i_arr) {
        	        $similarity = array();

	                foreach ($combination_matrix_by_p_id as $p_id_ => $p_id_i_arr_) {
        	                if ($p_id == $p_id_) continue;

	                        $c = 0;
        	                $p_id__k = array_keys($p_id_i_arr_);
                	        foreach ($p_id_i_arr as $p_id_i => $count) {
                        	        for (; $c < count($p_id__k); $c++) {
	                                        if ($p_id__k[$c] > $p_id_i) break;
        	                                if ($p_id__k[$c] == $p_id_i) {
                        	                        if (!isset($similarity[$p_id_])) {
                	                                        $similarity[$p_id_] = 1;
                                	                } else {
                                        	                $similarity[$p_id_]++;
                                                	}
	                                                $c++;
        	                                        break;
                	                        }
                        	        }
	                        }
        	        }

                	ksort($similarity);
	                $row = "";
        	        foreach ($similarity as $p_id_i => $count) {
                	        if (strlen($row) > 0) $row .= ";";
                        	$row .= $p_id_i . ":" . $count;
	                }
        	        $pcm_models[$p_id]->setCombinationSimilarityRow($row);

                	arsort($similarity);
	                $row = "";
        	        foreach ($similarity as $p_id_i => $count) {
                	        if (strlen($row) > 0) $row .= ";";
                        	$row .= $p_id_i . ":" . $count;
	                }
        	        $pcm_models[$p_id]->setCombinationSimilarityRowTopdown($row);
	        }

	        foreach ($combination_matrix_by_p_id as $p_id => $p_id_i_arr) {
        	        arsort($p_id_i_arr);
                	$row = "";
	                foreach ($p_id_i_arr as $p_id_i => $count) {
        	                if (strlen($row) > 0) $row .= ";";
                	        $row .= $p_id_i . ":" . $count;
	                }
        	        $pcm_models[$p_id]->setCombinationRowTopdown($row);
	                $pcm_models[$p_id]->insert();
        	}
	}

    	public static function deleteAll() {
	        $rcg = new RecipeConsumptionGroupModel();
        	$rcg->truncate();

	        $rcga = new RecipeConsumptionGroupAllergiesModel();
        	$rcga->truncate();

	        $rcg_agg = new RecipeConsumptionGroupAggModel();
        	$rcg_agg->truncate();

	        $products_matrix = new ProductsMatrixModel();
        	$products_matrix->truncate();
	}

	public static function get_context($json) {
	        $context = array(
        	        'http' => array(
                	        'method'         =>     'POST',
                        	'header'         =>     "Content-type: application/json\r\n" .
	                                                "Content-Length: " . strlen($json) . "\r\n",
        	                'content'        =>     $json
                	)
	        );
        	$context = stream_context_create($context);
	        return $context;
	}

	public static function get_post($url, $json) {
	        $context = self::get_context($json);
        	$result = json_decode(file_get_contents($url, false, $context));
	        return $result;
	}

	public static function download_tables() {
		$foreign_base_url = "https://foodbar.api.mur1.de/";
		$local_base_url = "http://10.10.12.33/";

		$login_action = "users/login";

		$foreign_login_json = '{"email": "mur1s.playground@root.de", "password": "secreter"}';
		$local_login_json = '{"email": "mur1s.playground@root.de", "password": "secret"}';

		$foreign_login_result = self::get_post($foreign_base_url . $login_action, $foreign_login_json);
		$local_login_result = self::get_post($local_base_url . $login_action, $local_login_json);

		if ($foreign_login_result->{"status"} == true && $local_login_result->{"status"} == true) {
		        $tables = array(
		                "Storage",
        	        	"StorageConsumption",
                		"Products",
		                "ProductsPrice"
	        	);

		        $truncate_action = "app/backup/truncate";
        		$backup_action = "app/backup";
	        	$insert_action = "app/backup/insert";

	        	foreach ($tables as $table) {
        	        	$local_data_arr = array(
	        	                "login_data" => $local_login_result->{'login_data'},
        	        	        "table" => array(
                	        	                "name"          =>      $table
                        	        	)
			                );
				$local_json = json_encode($local_data_arr, JSON_INVALID_UTF8_SUBSTITUTE);
                		$truncate_result = self::get_post($local_base_url . $truncate_action, $local_json);

		                $foreign_data_arr = array(
        		                "login_data" => $foreign_login_result->{'login_data'},
                		        "table" => array(
                        	                "name"          =>      $table,
                                	        "limit"         =>      10000,
                                        	"offset"        =>      0
	                                )
		                );
        		        $json = json_encode($foreign_data_arr, JSON_INVALID_UTF8_SUBSTITUTE);
                		$table_result = self::get_post($foreign_base_url . $backup_action, $json);

		                $model_full_name = "\\FooDBar\\" . $table . "Model";

        		        $local_data_arr["table"]["rows"] = $table_result->{$model_full_name};
                		$local_json = json_encode($local_data_arr, JSON_INVALID_UTF8_SUBSTITUTE);
	                	$insert_result = self::get_post($local_base_url . $insert_action, $local_json);
			}
		}
		return array("local" => $local_login_result, "foreign" => $foreign_login_result);
	}

	public static function upload_tables($logins) {
		$foreign_base_url = "https://foodbar.api.mur1.de/";
                $local_base_url = "http://10.10.12.33/";

		$local_login_result = $logins["local"];
		$foreign_login_result = $logins["foreign"];

		$truncate_action = "app/backup/truncate";
                $backup_action = "app/backup";
                $insert_action = "app/backup/insert";

		$tables = array(
                                "RecipeConsumptionGroup",
                                "RecipeConsumptionGroupAgg",
                                "RecipeConsumptionGroupAllergies",
                                "ProductsMatrix",
				"Jobs",
				"AppStatus"
                        );

		foreach ($tables as $table) {
                		$foreign_data_arr = array(
                                        "login_data" => $foreign_login_result->{'login_data'},
                                        "table" => array(
                                                        "name"          =>      $table
                                                )
                                        );
                                $foreign_json = json_encode($foreign_data_arr, JSON_INVALID_UTF8_SUBSTITUTE);
                                $truncate_result = self::get_post($foreign_base_url . $truncate_action, $foreign_json);

                                $local_data_arr = array(
                                        "login_data" => $local_login_result->{'login_data'},
                                        "table" => array(
                                                "name"          =>      $table,
                                                "limit"         =>      10000,
                                                "offset"        =>      0
                                        )
                                );
                                $json = json_encode($local_data_arr, JSON_INVALID_UTF8_SUBSTITUTE);
                                $table_result = self::get_post($local_base_url . $backup_action, $json);

                                $model_full_name = "\\FooDBar\\" . $table . "Model";

                                $foreign_data_arr["table"]["rows"] = $table_result->{$model_full_name};
                                $foreign_json = json_encode($foreign_data_arr, JSON_INVALID_UTF8_SUBSTITUTE);
                                $insert_result = self::get_post($foreign_base_url . $insert_action, $foreign_json);
                }
	}

	public function run() {
		$GLOBALS['Boot']->loadDBExt("Fields");
		$GLOBALS['Boot']->loadDBExt("Join");
		$GLOBALS['Boot']->loadDBExt("Order");
		$GLOBALS['Boot']->loadDBExt("GroupBy");
		$GLOBALS['Boot']->loadDBExt("DBFunction");
		$GLOBALS['Boot']->loadDBExt("DBFunctionExpression");

		$GLOBALS['Boot']->loadModel("StorageModel");
                $GLOBALS['Boot']->loadModel("StorageConsumptionModel");
                $GLOBALS['Boot']->loadModel("RecipeConsumptionGroupModel");
                $GLOBALS['Boot']->loadModel("RecipeConsumptionGroupAllergiesModel");
                $GLOBALS['Boot']->loadModel("RecipeConsumptionGroupAggModel");
                $GLOBALS['Boot']->loadModel("ProductsMatrixModel");

		$job_params = parent::getParams();

		if (is_null($job_params)) {
			parent::setJobStatus(parent::JOB_STATUS_ERROR, array("error" => "no params"));
			exit();
		} else if (!isset($job_params->{"date_to"})) {
			parent::setJobStatus(parent::JOB_STATUS_ERROR, array("error" => "missing param date_to"));
			exit();
		}

		$logins = self::download_tables();

		$status_fields = array(
			self::APP_STATUS_PROCESS_CONSUMPTION
		);

		$GLOBALS['Boot']->loadModule("app", "Status");
		$fields = StatusController::getFields($status_fields);

                $date_f = $job_params->{"date_to"};

		$date_from = null;
		if (!isset($fields["app_status"]->{"date_from"})) {
			$date_from = 0;
		} else {
			$date_from = $fields["app_status"]->{"date_from"};
		}

		/* TODO: date_from */
		self::deleteAll();

		self::insertConsumptionGroupsWithAllergies(0, $date_f);

		/* TMP: 1 */
		self::updatePrices(array(1), 0, $date_f);

	        self::insertConsumptionGroupAgg(1);

        	self::updateProductsMatrix(1);

		$fields = array(
			self::APP_STATUS_PROCESS_CONSUMPTION => array( "date_from" => $date_f)
		);

		StatusController::setFields($fields);

		self::upload_tables($logins);

		parent::setJobStatus(parent::JOB_STATUS_FINISHED, array("status" => true));
	}
}

$env = getenv('FRAME_ENVIRONMENT');

if ($env == "development") {
    $cfg = "development";
} else {
    $cfg = "live";
}

$upc_job = new UpdateProcessConsumptionJob("../app/config/app.{$cfg}.json", $argv);
$upc_job->run();
