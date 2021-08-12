<?php

namespace FooDBar\Recipe;

use \FooDBar\Users\LoginController as LoginController;

$GLOBALS['Boot']->loadDBExt("Join");
$GLOBALS['Boot']->loadDBExt("Fields");
$GLOBALS['Boot']->loadDBExt("Order");

use \Frame\Join 	as Join;
use \Frame\Fields	as Fields;
use \Frame\Condition 	as Condition;
use \Frame\Order 	as Order;

$GLOBALS['Boot']->loadModel("RecipeModel");
$GLOBALS['Boot']->loadModel("StorageModel");
$GLOBALS['Boot']->loadModel("StorageConsumptionModel");
$GLOBALS['Boot']->loadModel("ProductsModel");
$GLOBALS['Boot']->loadModel("RecipeConsumptionGroupAllergiesModel");
$GLOBALS['Boot']->loadModel("RecipeConsumptionGroupAggModel");
$GLOBALS['Boot']->loadModel("RecipeConsumptionGroupModel");
$GLOBALS['Boot']->loadModel("RecipeRequestDailyPresetModel");

use \FooDBar\RecipeModel 				as RecipeModel;
use \FooDBar\StorageModel 				as StorageModel;
use \FooDBar\StorageConsumptionModel           		as StorageConsumptionModel;
use \FooDBar\ProductsModel 				as ProductsModel;
use \FooDBar\RecipeConsumptionGroupAllergiesModel     	as RecipeConsumptionGroupAllergiesModel;
use \FooDBar\RecipeConsumptionGroupAggModel		as RecipeConsumptionGroupAggModel;
use \FooDBar\RecipeConsumptionGroupModel 		as RecipeConsumptionGroupModel;
use \FooDBar\RecipeRequestDailyPresetModel		as RecipeRequestDailyPresetModel;

use \FooDBar\Allergies\AllergyController	as AllergyController;
use \FooDBar\Storage 				as Storage;

class IndexController {
    private $DefaultController = true;
    private $DefaultAction = "index";

    public function getdailyuserpresetsAction() {
	$user = LoginController::requireAuth();

	$condition = new Condition("[c1]", array(
			"[c1]" => [
				[RecipeRequestDailyPresetModel::class, RecipeRequestDailyPresetModel::FIELD_USERS_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $user->getId()]
			]
	));

	$rrdp = new RecipeRequestDailyPresetModel();
	$rrdp->find($condition);

	$result["status"] = true;
	$result["recipe_request_daily_preset"] = new \stdClass();
	while ($rrdp->next()) {
		$result["recipe_request_daily_preset"]->{$rrdp->getId()} = $rrdp->toArray();
	}

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function insertdailyuserpresetAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'recipe_request_daily_preset'};

	$rrdp = new RecipeRequestDailyPresetModel();
	$rrdp->setUsersId($user->getId());
	$rrdp->setPresetName($data->{RecipeRequestDailyPresetModel::FIELD_PRESET_NAME});
	$rrdp->setPreset($data->{RecipeRequestDailyPresetModel::FIELD_PRESET});
	$rrdp->insert();

	$result["status"] = true;
	$result["Day"] = $data->{'Day'};
	$result["recipe_request_daily_preset_item"] = $rrdp->toArray();;

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function deletedailyuserpresetAction() {
	$user = LoginController::requireAuth();

        $data = $GLOBALS['POST']->{'recipe_request_daily_preset_id'};

	$condition = new Condition("[c1] AND [c2]", array(
                        "[c1]" => [
                                [RecipeRequestDailyPresetModel::class, RecipeRequestDailyPresetModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $user->getId()]
                        ],
			"[c2]" => [
				[RecipeRequestDailyPresetModel::class, RecipeRequestDailyPresetModel::FIELD_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $data]
			]
        ));

	$rrdp = new RecipeRequestDailyPresetModel();
	$rrdp->find($condition);

	$result["status"] = true;
	if ($rrdp->next()) {
		$rrdp->delete();
	} else {
		$resutl["status"] = false;
	}

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function requestAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'recipes_request'};

	$demand = $data->{'demand'};
	$days = $data->{'days'};
	$parts = $data->{'parts'};
	$n_dist = $data->{'n_dist'};
	$threshold = 2;


	$rcg_agg_ids_with_found_products = array();
        $rcg_agg_by_found_products_in_storage = array();


	/* USER ALLERGIES CONDITION */
	$GLOBALS['Boot']->loadModule("allergies", "Allergy");
        $user_allergies = AllergyController::getAllergyValues($user);
        unset($user_allergies->{'has_unset_allergies'});

        $conds_a_ct = 0;
        $conds_a_str = "";
        $conds_a = array();
        foreach ($user_allergies as $field_name_camel => $value) {
                if ($value > 0) {
                        $desc = "[a" . $conds_a_ct . "]";

                        if ($conds_a_ct != 0) {
                                $conds_a_str .= " AND ";
                        }
                        $conds_a_str .= $desc;

                        $conds_a[$desc] = [
                                [RecipeConsumptionGroupAllergiesModel::class, $field_name_camel],
                                Condition::COMPARISON_LESS,
                                [Condition::CONDITION_CONST, 1]
                        ];
                        $conds_a_ct++;
                }
        }

	/* RCG_AGG WITH PRODUCTS IN STORAGE */
	$GLOBALS['Boot']->loadModule("storage", "Index");
        $storage_r = Storage\IndexController::getStoragesContent($user);
        $products_ids_to_amount = array();
        foreach ($storage_r as $s_id => $s_item) {
                $products_ids_to_amount[$s_item[StorageModel::FIELD_PRODUCTS_ID]] = $s_item[StorageModel::FIELD_AMOUNT];
        }
	$products_ids_in_storage = array_keys($products_ids_to_amount);

	$conds_0_str = "";
	$conds_0 = array();
	if (count($products_ids_in_storage) > 0) {
	        $sub_cond = new Condition("[c1]", array(
        	        "[c1]" => [
	                        [StorageModel::class, StorageModel::FIELD_PRODUCTS_ID],
                	        Condition::COMPARISON_IN,
        	                [Condition::CONDITION_CONST_ARRAY, $products_ids_in_storage]
	                ]
        	));

	        $sub_join = new Join(new StorageModel(), "[j1]", array(
        	        "[j1]" => [
	                        [StorageConsumptionModel::class, StorageConsumptionModel::FIELD_STORAGE_ID],
                        	Condition::COMPARISON_EQUALS,
                	        [StorageModel::class, StorageModel::FIELD_ID]
        	        ]
	        ));

	        $sub_field = new Fields(array());
	        $sub_field->addField(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_DATETIME);

        	$sub_storage_consumption = new StorageConsumptionModel();
	        $sub_query = $sub_storage_consumption->find($sub_cond, array($sub_join), null, null, $sub_field, null, false);

		$conds_0 = array(
			"[c0]" => [
                        	[RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_DATETIME],
	                        Condition::COMPARISON_IN,
        	                [Condition::CONDITION_QUERY, $sub_query]
	                ]
		);

		$f_str = "[c0]";
		if ($conds_a_ct > 0) {
			$f_str .= " AND " . $conds_a_str;
			$conds_0 = array_merge($conds_0, $conds_a);
		}

		$rcg_cond = new Condition($f_str, $conds_0);

		$rcg_join = new Join(new RecipeConsumptionGroupModel(), "[j0]", array(
                		"[j0]" => [
		                        [RecipeConsumptionGroupAggModel::class, RecipeConsumptionGroupAggModel::FIELD_RECIPE_CONSUMPTION_GROUP_ID],
	        	                Condition::COMPARISON_EQUALS,
                	        	[RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_ID]
		       	        ]
		        ));

	        $rcg_allergies = new Join(new RecipeConsumptionGroupAllergiesModel(), "[j1]", array(
        		        "[j1]" => [
	                	        [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_RECIPE_CONSUMPTION_GROUP_ALLERGIES_ID],
                        		Condition::COMPARISON_EQUALS,
	                	        [RecipeConsumptionGroupAllergiesModel::class, RecipeConsumptionGroupAllergiesModel::FIELD_ID]
        		        ]
		        ));

		$rcg_agg = new RecipeConsumptionGroupAggModel();
	        $rcg_agg->find($rcg_cond, array($rcg_join, $rcg_allergies));

		while ($rcg_agg->next()) {
                	$recipe_consumption_group = $rcg_agg->joinedModelByClass(RecipeConsumptionGroupModel::class);

	                $products_arr = explode(";", $recipe_consumption_group->getProductsIds());
        	        $found_products = 0;
                	foreach ($products_arr as $p_id) {
	                        if (in_array($p_id, $products_ids_in_storage)) {
        	                        $found_products++;
                	        }
	                }
        	        if ($found_products == 0) continue;

	                if (!isset($rcg_agg_by_found_products_in_storage[$found_products])) $rcg_agg_by_found_products_in_storage[$found_products] = array();

        	        $rcg_agg_by_found_products_in_storage[$found_products][] = $rcg_agg->toArray(true);
                	$rcg_agg_ids_with_found_products[] = $rcg_agg->getId();
	        }

	}

	$conds_1_str = "";
	$conds_1 = array();

	$rcg_cond = null;
	if (count($rcg_agg_ids_with_found_products) > 0) {
		$conds_1_str = "[c1]";
		$conds_1["[c1]"] = [
					[RecipeConsumptionGroupAggModel::class, RecipeConsumptionGroupAggModel::FIELD_ID],
					Condition::COMPARISON_NOT_IN,
					[Condition::CONDITION_CONST_ARRAY, $rcg_agg_ids_with_found_products]
				];
	}

	if ($conds_a_ct > 0) {
        	if (count($conds_1) > 0) $conds_1_str .= " AND ";
		$conds_1_str .= $conds_a_str;
		$conds_1 = array_merge($conds_1, $conds_a);
        }

	if (count($conds_1) > 0) {
		$rcg_cond = new Condition($conds_1_str, $conds_1);
	}

	$rcg_join = new Join(new RecipeConsumptionGroupModel(), "[j0]", array(
        		"[j0]" => [
                        	[RecipeConsumptionGroupAggModel::class, RecipeConsumptionGroupAggModel::FIELD_RECIPE_CONSUMPTION_GROUP_ID],
                                Condition::COMPARISON_EQUALS,
                                [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_ID]
                        ]
		));

	$rcg_allergies = new Join(new RecipeConsumptionGroupAllergiesModel(), "[j1]", array(
        	"[j1]" => [
                                        [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_RECIPE_CONSUMPTION_GROUP_ALLERGIES_ID],
                                        Condition::COMPARISON_EQUALS,
                                        [RecipeConsumptionGroupAllergiesModel::class, RecipeConsumptionGroupAllergiesModel::FIELD_ID]
                                ]
                        ));

        $rcg_agg = new RecipeConsumptionGroupAggModel();
        $rcg_agg->find($rcg_cond, array($rcg_join, $rcg_allergies));

	$rcg_agg_by_found_products_in_storage[0] = array();
	while ($rcg_agg->next()) {
		$rcg_agg_by_found_products_in_storage[0][] = $rcg_agg->toArray(true);
	}
	krsort($rcg_agg_by_found_products_in_storage);

	$used_rcg_agg_ids = array();

	$result["status"] = true;
	$result["recipes"] = new \stdClass();
	$result["recipes_mj_min_total"] = 0;
	$result["recipes_mj_max_total"] = 0;

	for ($d = 0; $d < $days; $d++) {
		for ($p = 0; $p < count($parts[$d]); $p++) {
			$found_one = false;
			foreach ($rcg_agg_by_found_products_in_storage as $products_used => $rcg_agg_deep_arrs) {
				foreach ($rcg_agg_deep_arrs as $idx => $rcg_agg_deep_arr) {
					$rcg_agg_arr = $rcg_agg_deep_arr[RecipeConsumptionGroupAggModel::class];

					if (in_array($rcg_agg_arr[RecipeConsumptionGroupAggModel::FIELD_ID], $used_rcg_agg_ids)) continue;
					if ($rcg_agg_arr[RecipeConsumptionGroupAggModel::FIELD_MJ_MAX] > $parts[$d][$p] - $threshold && $rcg_agg_arr[RecipeConsumptionGroupAggModel::FIELD_MJ_MIN] < $parts[$d][$p] + $threshold) {
/*
						 $n_dist_fit = array(
								$rcg_agg_arr[RecipeConsumptionGroupAggModel::FIELD_N_FAT_PERCENT_AVG] 	- $n_dist[$d][0],
								$rcg_agg_arr[RecipeConsumptionGroupAggModel::FIELD_N_CARBS_PERCENT_AVG] - $n_dist[$d][1],
								$rcg_agg_arr[RecipeConsumptionGroupAggModel::FIELD_N_CARBS_PERCENT_AVG] - $n_dist[$d][2]
							);
*/
						 $amount_multiplier = $parts[$d][$p] / $rcg_agg_arr[RecipeConsumptionGroupAggModel::FIELD_MJ_AVG];


						 $used_rcg_agg_ids[] = $rcg_agg_arr[RecipeConsumptionGroupAggModel::FIELD_ID];

 						 $rcg = $rcg_agg_deep_arr[Join::class][RecipeConsumptionGroupModel::class];
                                        	 $result["recipes"]->{$d . "_" . $p} = $rcg_agg_arr;
                                	         $result["recipes"]->{$d . "_" . $p}["ProductsIds"] = $rcg[RecipeConsumptionGroupModel::FIELD_PRODUCTS_IDS];
						 $result["recipes"]->{$d . "_" . $p}["amount_multiplier"] = $amount_multiplier;
                        	                 $result["recipes_mj_min_total"] += $rcg_agg_arr[RecipeConsumptionGroupAggModel::FIELD_MJ_MIN];
                	                         $result["recipes_mj_max_total"] += $rcg_agg_arr[RecipeConsumptionGroupAggModel::FIELD_MJ_MAX];
						 $found_one = true;
        	                                 break;
					}
				}
				if ($found_one) break;
			}
		}
	}

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function indexAction() {
/*
	$recipe = new RecipeModel();
	$recipe->find();
*/
	/* USER ALLERGIES */
/*	$user_id = @$_GET["user_id"];
	$user = null;

	$allergy_ct = 0;
	$allergy_conds_desc = "";
	$allergy_conds = array();
	if (!is_null($user_id)) {
		$user_cond = new Condition("[c1]", array(
			"[c1]" => [
					[UsersModel::class, UsersModel::FIELD_ID],
					Condition::COMPARISON_EQUALS,
					[Condition::CONDITION_CONST, $user_id]
				]
			));

		$user = new UsersModel();
		$user->find($user_cond);
		if ($user->next()) {
		        $allergy_conds_desc = "";
		        $allergy_conds = array();

			foreach ($user->fields() as $field_name_camel => $field) {
				$field_name = $field["Field"];

				$starts_with_a_ = strpos($field_name, "a_");
				if ($starts_with_a_ !== false && $starts_with_a_ === 0) {
					$getter = "get" . $field_name_camel;
					$value = $user->$getter();
					if (!is_null($value) && $value == 1) {
						if ($allergy_ct > 0) {
							$allergy_conds_desc += " AND ";
						}
						$a_desc = "[a{$allergy_ct}]";
						$allergy_conds_desc .= $a_desc;
						$allergy_conds[$a_desc] = array(
							[ProductsModel::class, $field_name_camel],
							Condition::COMPARISON_LESS,
							[Condition::CONDITION_CONST, 1]
						);
						$allergy_ct++;
					}
				}
			}
		}
	} */
	/* END USER ALLERGIES */
/*
	$result = array();

	$available = @$_GET["available"];
	if (is_null($available)) {
		$available = false;
	} else {
		$available = $available === "true";
	}

	$partially = @$_GET["partially"];
	if (is_null($partially)) {
		$partially = true;
	} else {
		$partially = $partially === "true";
	}

	$ignore_missing = @$_GET["ignore_missing"];
	if (is_null($ignore_missing)) {
		$ignore_missing = false;
	} else {
		$ignore_missing = $ignore_missing === "true";
	}

	$t_products = new \stdClass();

	while ($recipe->next()) {
		$ingredients_blob = $recipe->getIngredientsList();
		$ingredients_splt = explode(";", $ingredients_blob);

		$r_available = true;
		$r_partially = false;

		$r_meta = array(
			"kj_low" => 0,
			"kj_high" => 0,
			"price_low" => 0,
			"price_high" => 0,
			"price_available_low" => 0
		);

		$r_kj_low_sum = 0;
		$r_kj_high_sum = 0;
		$r_price_low_sum = 0;
		$r_price_high_sum = 0;
		$r_products = array();
		$r_missing_ingredient = false;

		$ingredients = array();
		for ($i = 0; $i < count($ingredients_splt); $i++) {
			$ingredient_splt = explode(":", $ingredients_splt[$i]);

			$ingredient_name = $ingredient_splt[0];
			$ingredient_amount = $ingredient_splt[1];
			$ingredient_amount_type_id = $ingredient_splt[2];

			$ingredients[$ingredient_name] = array(
				"meta"		=> array(
					"available" 		=> false,
					"completly"		=> false,
					"without_composition" 	=> false,
					"kj_low"		=> null,
					"kj_high"		=> null,
					"price_low"		=> null,
					"price_high"		=> null,
					"price_available_low"	=> null
				),
				"products" 	=> array()
			);

			$product_condition_desc = "[c1] AND [c2]";
			$product_condition_cond = array(
				"[c1]" => [
                	                        [ProductsModel::class, ProductsModel::FIELD_NAME],
        	                                Condition::COMPARISON_LIKE,
	                                        [Condition::CONDITION_CONST, $ingredient_name]
                                        ],
                                "[c2]" => [
                        	                [ProductsModel::class, ProductsModel::FIELD_AMOUNT_TYPE_ID],
                                	        Condition::COMPARISON_EQUALS,
                                        	[Condition::CONDITION_CONST, $ingredient_amount_type_id]
                                        ]

			);
			if ($allergy_ct > 0) {
				$product_condition_desc .= " AND " . $allergy_conds_desc;
				$product_condition_cond = array_merge($product_condition_cond, $allergy_conds);
			}

			$product_condition = new Condition($product_condition_desc, $product_condition_cond);

			$product_storage_join = new Join(new StorageModel(), "[j1]", array(
				"[j1]" => [
					[ProductsModel::class, ProductsModel::FIELD_ID],
					Condition::COMPARISON_EQUALS,
					[StorageModel::class, StorageModel::FIELD_PRODUCTS_ID]
				]
			), Join::JOIN_LEFT);

			$products = new ProductsModel();
			$products->find($product_condition, array($product_storage_join));

			$amount_sum = 0;

			while ($products->next()) {
				$p_id = intval($products->getId());
				$r_products[] = $products->toArray();

				$storage = $products->joinedModelByClass(StorageModel::class);

				$p_am = floatval($storage->getAmount());
				$p_kj = floatval($products->getKj());

				if (is_null($ingredients[$ingredient_name]["meta"]["kj_low"]) || $ingredients[$ingredient_name]["meta"]["kj_low"] > $p_kj) {
					$ingredients[$ingredient_name]["meta"]["kj_low"] = $p_kj * $ingredient_amount;
				}
                                if (is_null($ingredients[$ingredient_name]["meta"]["kj_high"]) || $ingredients[$ingredient_name]["meta"]["kj_high"] < $p_kj) {
                                        $ingredients[$ingredient_name]["meta"]["kj_high"] = $p_kj * $ingredient_amount;
                                }

				$p_app = $products->getPrice()/$products->getAmount();
				$p_ap = $p_app * $ingredient_amount;
				if (is_null($ingredients[$ingredient_name]["meta"]["price_low"]) || $ingredients[$ingredient_name]["meta"]["price_low"] > $p_ap) {
					$ingredients[$ingredient_name]["meta"]["price_low"] = $p_ap;
				}
                                if (is_null($ingredients[$ingredient_name]["meta"]["price_high"]) || $ingredients[$ingredient_name]["meta"]["price_high"] < $p_ap) {
                                        $ingredients[$ingredient_name]["meta"]["price_high"] = $p_ap;
                                }
				if (is_null($ingredients[$ingredient_name]["meta"]["price_available_low"]) || $ingredients[$ingredient_name]["meta"]["price_available_low"] > $p_ap) {
					$ingredients[$ingredient_name]["meta"]["price_available_low"] = $p_ap;
				}


				if ($p_am != null) {
					$amount_sum += $p_am;

					$ingredients[$ingredient_name]["meta"]["available"] = true;

					$avail_price_low = $p_ap - ($p_app * $p_am);
					if ($avail_price_low < 0) $avail_price_low = 0.0;
					if (is_null($ingredients[$ingredient_name]["meta"]["price_available_low"]) || $ingredients[$ingredient_name]["meta"]["price_available_low"] > $avail_price_low) {
						$ingredients[$ingredient_name]["meta"]["price_available_low"] = $avail_price_low;
					}

					if ($p_am >= $ingredient_amount) {
						$ingredients[$ingredient_name]["meta"]["without_composition"] = true;
						$ingredients[$ingredient_name]["meta"]["price_available_low"] = 0.0;
					}
					if ($amount_sum >= $ingredient_amount) {
						$ingredients[$ingredient_name]["meta"]["completly"] = true;
					}
				}

				$ingredients[$ingredient_name]["products"][] = array(
					"id" => $p_id,
					"available_amount" => $p_am
				);
			}
			if (count($ingredients[$ingredient_name]["products"]) == 0) {
				$r_missing_ingredient = true;
				if (!$ignore_missing) break;
			}
			if ($available) {
				if (!$ingredients[$ingredient_name]["meta"]["available"]) {
					$r_avail = false;
					if (!$partially) break;
				} else {
					$r_partially = true;
				}
			}

			if (!is_null($ingredients[$ingredient_name]["meta"]["kj_low"])) {
				$r_meta["kj_low"] += $ingredients[$ingredient_name]["meta"]["kj_low"];
			}
			if (!is_null($ingredients[$ingredient_name]["meta"]["kj_high"])) {
				$r_meta["kj_high"] += $ingredients[$ingredient_name]["meta"]["kj_high"];
			}
			if (!is_null($ingredients[$ingredient_name]["meta"]["price_low"])) {
				$r_meta["price_low"] += $ingredients[$ingredient_name]["meta"]["price_low"];
			}
                        if (!is_null($ingredients[$ingredient_name]["meta"]["price_high"])) {
                                $r_meta["price_high"] += $ingredients[$ingredient_name]["meta"]["price_high"];
                        }
			if (!is_null($ingredients[$ingredient_name]["meta"]["price_available_low"])) {
				$r_meta["price_available_low"] += $ingredients[$ingredient_name]["meta"]["price_available_low"];
			}
		}

		$add = false;
		if (!$available) {
			$add = true;
		} else if ($available && $partially) {
			if ($r_partially) $add = true;
		} else if ($available && !$partially) {
			if ($r_avail) $add = true;
		}
		if (!$ignore_missing) {
			if ($r_missing_ingredient) $add = false;
		}
		if ($add) {
			for ($p = 0; $p < count($r_products); $p++) {
				$p_id = $r_products[$p][ProductsModel::FIELD_ID];
				if (!isset($t_products->{$p_id})) {
					$t_products->{$p_id} = $r_products[$p];
				}
			}
                        $result[$recipe->getName()] = array(
                                "description" 	=> $recipe->getDescription(),
				"meta"		=> $r_meta,
                                "ingredients" 	=> $ingredients
                        );
		}
	}
	$result["products"] = $t_products;
	exit(json_encode($result, JSON_PRETTY_PRINT));
*/
    }
}
