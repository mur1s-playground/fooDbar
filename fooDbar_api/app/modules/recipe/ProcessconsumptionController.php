<?php

namespace FooDBar;

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "Fields.php";
use \Frame\Fields as Fields;
require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "Join.php";
use \Frame\Join as Join;
use \Frame\Condition as Condition;
require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "Order.php";
use \Frame\Order as Order;
require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "GroupBy.php";
use \Frame\GroupBy as GroupBy;

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "DBFunction.php";
use \Frame\DBFunction as DBFunction;
require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "DBFunctionExpression.php";
use \Frame\DBFunctionExpression as DBFunctionExpression;

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "StorageModel.php";
require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "StorageConsumptionModel.php";
//require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "ProductsModel.php";

class ProcessconsumptionController {
    private $DefaultController = false;
    private $DefaultAction = "get";

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
			"ProductsIds" 	=> $products_ids,
			"Amounts"	=> $amounts,
                	"Datetime" 	=> $consumption->getDatetime()
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

	$kj_p		= $product[ProductsModel::FIELD_KJ];
	$fat_p 	 	= $product[ProductsModel::FIELD_N_FAT];
	$carbs_p	= $product[ProductsModel::FIELD_N_CARBS];
	$protein_p 	= $product[ProductsModel::FIELD_N_PROTEIN];
	$salt_p		= $product[ProductsModel::FIELD_N_SALT];
	$fiber_p	= $product[ProductsModel::FIELD_N_FIBER];

	$result = array(
		"Kj"		=> is_null($kj_p)	? 0 : round($c_a * $kj_p, 2),
		"Fat"		=> is_null($fat_p)	? 0 : round($c_a * $fat_p, 2),
		"Carbs"		=> is_null($carbs_p)	? 0 : round($c_a * $carbs_p, 2),
		"Protein"	=> is_null($protein_p)	? 0 : round($c_a * $protein_p, 2),
		"Salt"		=> is_null($salt_p)	? 0 : round($c_a * $salt_p, 2),
		"Fiber"		=> is_null($fiber_p)	? 0 : round($c_a * $fiber_p, 2)
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
			$result[$h . "_" . $idx]["Kj"] 		= round($result[$h . "_" . $idx]["Kj"], 2);
			$result[$h . "_" . $idx]["Fat"] 	= round($result[$h . "_" . $idx]["Fat"], 2);
        	        $result[$h . "_" . $idx]["Carbs"] 	= round($result[$h . "_" . $idx]["Carbs"], 2);
                	$result[$h . "_" . $idx]["Protein"] 	= round($result[$h . "_" . $idx]["Protein"], 2);
        	        $result[$h . "_" . $idx]["Salt"] 	= round($result[$h . "_" . $idx]["Salt"], 2);
	                $result[$h . "_" . $idx]["Fiber"] 	= round($result[$h . "_" . $idx]["Fiber"], 2);
			$result[$h . "_" . $idx]["p_parts"] 	= $contribution;
		}
	}
	return $result;
    }

    public static function optimizeNutritionDistributionSingle($consumption_groups_nutrition, $kj, $fat_percent, $carbs_percent, $protein_percent, $conditions) {
		$amount_factors = array();
		$weights = array();

		$products_ct = count($consumption_groups_nutrition["p_parts"]);
		$products_ids = array_keys($consumption_groups_nutrition["p_parts"]);
		$total_nutrition = $consumption_groups_nutrition["Fat"] + $consumption_groups_nutrition["Carbs"] + $consumption_groups_nutrition["Protein"] + $consumption_groups_nutrition["Salt"] + $consumption_groups_nutrition["Fiber"];
		$nutrition_current = $total_nutrition;
		$distribution_start = array(
			"Fat" 		=> round($consumption_groups_nutrition["Fat"]/$total_nutrition, 4),
			"Carbs"		=> round($consumption_groups_nutrition["Carbs"]/$total_nutrition, 4),
			"Protein"       => round($consumption_groups_nutrition["Protein"]/$total_nutrition, 4),
			"Salt"         	=> round($consumption_groups_nutrition["Salt"]/$total_nutrition, 4),
			"Fiber"		=> round($consumption_groups_nutrition["Fiber"]/$total_nutrition, 4)
		);
		$distribution_current = $distribution_start;
		$amount_multiplier = round($kj/$consumption_groups_nutrition["Kj"], 2);
		if ($products_ct == 1) {
                        return array(
					"amount_factors" 	=> array($products_ids[0] => $amount_multiplier),
					"distribution" 		=> $distribution_current,
					"nutrition"	 	=> $amount_multiplier * $total_nutrition,
					"distribution_start" 	=> $distribution_start,
					"nutrition_start" 	=> $total_nutrition);
                }

		for ($p = 0; $p < $products_ct; $p++) {
			$amount_factors[$products_ids[$p]] = $amount_multiplier;
			$weights[$p] = $consumption_groups_nutrition["p_parts"][$p]["Fat"] + $consumption_groups_nutrition["p_parts"][$p]["Carbs"] + $consumption_groups_nutrition["p_parts"][$p]["Protein"] + $consumption_groups_nutrition["p_parts"][$p]["Salt"] + $consumption_groups_nutrition["p_parts"][$p]["Fiber"];
			$weights[$p] /= $total_nutrition;
		}

		//TODO
		$optimize = 100;

		return array(
				"amount_factors" 	=> $amount_factors,
				"distribution" 		=> $distribution_current,
				"nutrition" 		=> $nutrition_current,
				"distribution_start" 	=> $distribution_start,
				"nutrition_start" 	=> $total_nutrition);
    }

    public function getAction() {
	$user = LoginController::requireAuth();

	//carbs (45%-65%), protein (10%-35%), and fat (20%-35%)
	//TMP
	$fat_percent = 0.275;
	$protein_percent = 0.225;
	$carbs_percent = 0.5;

	$result = array();
        $result["status"] = true;

	//TMP
	$date_now = date_create();
	$date_f = $date_now->format("Y-m-d H:i:s");

	$result["products_ids"] = self::getProductsIds(0, $date_f);
	$result["consumption_groups"] = self::getConsumptionGroups(0, $date_f);

	$GLOBALS['Boot']->loadModule("products", "Index");
	$result["products"] = IndexController::getProductsByIdArray($result["products_ids"]);

	$result["consumption_groups_nutrition"] = self::getNutritionFromConsumptionGroups($result["consumption_groups"], $result["products"]);
	foreach ($result["consumption_groups_nutrition"] as $h_idx => $cgn) {
		$result["consumption_groups_nutrition"][$h_idx]["optimised"] = self::optimizeNutritionDistributionSingle($cgn, $result["consumption_groups_nutrition"][$h_idx]["Kj"], $fat_percent, $carbs_percent, $protein_percent, null);
	}

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }
}
