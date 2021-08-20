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
$GLOBALS['Boot']->loadModel("RecipeConsumptionGroupAggPlannedModel");

use \FooDBar\RecipeModel 				as RecipeModel;
use \FooDBar\StorageModel 				as StorageModel;
use \FooDBar\StorageConsumptionModel           		as StorageConsumptionModel;
use \FooDBar\ProductsModel 				as ProductsModel;
use \FooDBar\RecipeConsumptionGroupAllergiesModel     	as RecipeConsumptionGroupAllergiesModel;
use \FooDBar\RecipeConsumptionGroupAggModel		as RecipeConsumptionGroupAggModel;
use \FooDBar\RecipeConsumptionGroupModel 		as RecipeConsumptionGroupModel;
use \FooDBar\RecipeRequestDailyPresetModel		as RecipeRequestDailyPresetModel;
use \FooDBar\RecipeConsumptionGroupAggPlannedModel	as RecipeConsumptionGroupAggPlannedModel;

use \FooDBar\Allergies\AllergyController	as AllergyController;
use \FooDBar\Storage 				as Storage;

class IndexController {
    private $DefaultController = true;
    private $DefaultAction = "get";

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

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
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

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
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
		$result["status"] = false;
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function requestAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'recipes_request'};

	$result = array();

	$demand = $data->{'demand'};
	$days = $data->{'days'};
	$parts = $data->{'parts'};
	$n_dist = $data->{'n_dist'};
	if (isset($data->{'recipes'})) {
		$result['recipes'] = $data->{'recipes'};
	} else {
		$result['recipes'] = new \stdClass();
	}
	$product_demand = array();
	if (isset($data->{'demand_data'})) {
		$product_demand = $data->{'demand_data'};
	}
	$banned_rcg_agg_ids = array();
	if (isset($data->{'banned_rcg_agg_ids'})) {
		$banned_rcg_agg_ids = $data->{'banned_rcg_agg_ids'};
	}
	$used_rcg_agg_ids = array();
	if (isset($data->{'used_rcg_agg_ids'})) {
		$used_rcg_agg_ids = $data->{'used_rcg_agg_ids'};
	}

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

	$result["status"] = true;

	for ($d = 0; $d < $days; $d++) {
		for ($p = 0; $p < count($parts[$d]); $p++) {
			if (isset($result['recipes']->{$d . "_" . $p})) continue;
			$found_one = false;
			foreach ($rcg_agg_by_found_products_in_storage as $products_used => $rcg_agg_deep_arrs) {
				$idxs = range(0, count($rcg_agg_deep_arrs)-1);
				foreach ($rcg_agg_deep_arrs as $idx => $rcg_agg_deep_arr) {
					$rcg_agg_arr = $rcg_agg_deep_arr[RecipeConsumptionGroupAggModel::class];
					if (in_array($rcg_agg_arr[RecipeConsumptionGroupAggModel::FIELD_ID], $banned_rcg_agg_ids)) continue;
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

						 $result["recipes"]->{$d . "_" . $p} = array();
						 $result["recipes"]->{$d . "_" . $p}["Id"] = $d . "_" . $p;
						 $result["recipes"]->{$d . "_" . $p}["RecipeConsumptionGroupAggId"] = $rcg_agg_arr[RecipeConsumptionGroupAggModel::FIELD_ID];

 						 $rcg = $rcg_agg_deep_arr[Join::class][RecipeConsumptionGroupModel::class];

						 $products_ids = $rcg[RecipeConsumptionGroupModel::FIELD_PRODUCTS_IDS];
						 $amounts = $rcg[RecipeConsumptionGroupModel::FIELD_AMOUNTS];

						 $products_arr = explode(";", $products_ids);
						 $amounts_arr = explode(";", $amounts);
						 $result["recipes"]->{$d . "_" . $p}["Amounts"] = "";
						 for ($pr = 0; $pr < count($products_arr); $pr++) {
							$pr_id = $products_arr[$pr];
							$pr_d = round($amount_multiplier * $amounts_arr[$pr], 2);

							$result["recipes"]->{$d . "_" . $p}["Amounts"] .= $pr_d;
							if ($pr + 1 < count($products_arr)) $result["recipes"]->{$d . "_" . $p}["Amounts"] .= ";";

							if (isset($products_ids_to_amount[$products_arr[$pr]])) {
								if ($products_ids_to_amount[$pr_id] >= $pr_d) {
									$products_ids_to_amount[$pr_id] -= $pr_d;
								} else {
									$pr_d -= $products_ids_to_amount[$pr_id];
									$products_ids_to_amount[$pr_id] = 0;
									if (!isset($product_demand->{$pr_id})) $product_demand->{$pr_id} = 0;
									$product_demand->{$pr_id} += $pr_d;
								}
							} else {
								if (!isset($product_demand->{$pr_id})) $product_demand->{$pr_id} = 0;
								$product_demand->{$pr_id} += $pr_d;
							}
						 }


                                	         $result["recipes"]->{$d . "_" . $p}["ProductsIds"] = $rcg[RecipeConsumptionGroupModel::FIELD_PRODUCTS_IDS];
						 $result["recipes"]->{$d . "_" . $p}["Mj"] = round($rcg_agg_arr[RecipeConsumptionGroupAggModel::FIELD_MJ_AVG] * $amount_multiplier, 2);
						 $result["recipes"]->{$d . "_" . $p}[RecipeConsumptionGroupModel::FIELD_N_FAT_PERCENT] = $rcg[RecipeConsumptionGroupModel::FIELD_N_FAT_PERCENT];
						 $result["recipes"]->{$d . "_" . $p}[RecipeConsumptionGroupModel::FIELD_N_CARBS_PERCENT] = $rcg[RecipeConsumptionGroupModel::FIELD_N_CARBS_PERCENT];
						 $result["recipes"]->{$d . "_" . $p}[RecipeConsumptionGroupModel::FIELD_N_PROTEIN_PERCENT] = $rcg[RecipeConsumptionGroupModel::FIELD_N_PROTEIN_PERCENT];
						 $found_one = true;
        	                                 break;
					}
				}
				if ($found_one) break;
			}
		}
	}
	$demand_unset = array();
	foreach ($product_demand as $p_id => $demand) {
		$product_demand->{$p_id} = round($demand, 2);
		if ($product_demand->{$p_id} <= 0) {
			$demand_unset[] = $p_id;
		}
	}
	foreach ($demand_unset as $p_id) {
		unset($product_demand->{$p_id});
	}
	$result["product_demand"] = $product_demand;
	$result["used_rcg_agg_ids"] = $used_rcg_agg_ids;
	$result["banned_rcg_agg_ids"] = $banned_rcg_agg_ids;
	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function getAction() {
	$user = LoginController::requireAuth();

	$condition = new Condition("[c1]", array(
		"[c1]" => [
			[RecipeConsumptionGroupAggPlannedModel::class, RecipeConsumptionGroupAggPlannedModel::FIELD_USERS_ID],
			Condition::COMPARISON_EQUALS,
			[Condition::CONDITION_CONST, $user->getId()]
		]
	));

	$join = new Join(new RecipeConsumptionGroupAggModel(), "[j1]", array(
		"[j1]" => [
			[RecipeConsumptionGroupAggPlannedModel::class, RecipeConsumptionGroupAggPlannedModel::FIELD_RECIPE_CONSUMPTION_GROUP_AGG_ID],
			Condition::COMPARISON_EQUALS,
			[RecipeConsumptionGroupAggModel::class, RecipeConsumptionGroupAggModel::FIELD_ID]
		]
	));

	$join_ = new Join(new RecipeConsumptionGroupModel(), "[j2]", array(
		"[j2]" => [
			[RecipeConsumptionGroupAggModel::class, RecipeConsumptionGroupAggModel::FIELD_RECIPE_CONSUMPTION_GROUP_ID],
			Condition::COMPARISON_EQUALS,
			[RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_ID]
		]
	));

	$rcg_agg_p = new RecipeConsumptionGroupAggPlannedModel();
	$rcg_agg_p->find($condition, array($join, $join_));

	$result["status"] = true;
        $result["recipes_planned"] = new \stdClass();
	while ($rcg_agg_p->next()) {
		$rcg = $rcg_agg_p->joinedModelByClass(RecipeConsumptionGroupModel::class);

		$result["recipes_planned"]->{$rcg_agg_p->getId()} = array();
		$result["recipes_planned"]->{$rcg_agg_p->getId()}[RecipeConsumptionGroupAggPlannedModel::FIELD_ID] = $rcg_agg_p->getId();
		$result["recipes_planned"]->{$rcg_agg_p->getId()}[RecipeConsumptionGroupModel::FIELD_PRODUCTS_IDS] = $rcg->getProductsIds();
		$result["recipes_planned"]->{$rcg_agg_p->getId()}[RecipeConsumptionGroupModel::FIELD_AMOUNTS] = $rcg_agg_p->getAmounts();
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function insertAction() {
	$user = LoginController::requireAuth();

        $data = $GLOBALS['POST']->{'recipes'};

	$result["status"] = true;
	$result["recipes_planned"] = new \stdClass();

	$props = get_object_vars($data);
	$keys = array_keys($props);
	asort($keys, SORT_STRING);

	foreach ($keys as $p) {
		$rcg_agg_p = new RecipeConsumptionGroupAggPlannedModel();
		$rcg_agg_p->setUsersId($user->getId());
		$rcg_agg_p->setRecipeConsumptionGroupAggId($data->{$p}->{RecipeConsumptionGroupAggPlannedModel::FIELD_RECIPE_CONSUMPTION_GROUP_AGG_ID});
		$rcg_agg_p->setAmounts($data->{$p}->{RecipeConsumptionGroupAggPlannedModel::FIELD_AMOUNTS});
		$rcg_agg_p->insert();

		$result["recipes_planned"]->{$rcg_agg_p->getId()} = $rcg_agg_p->toArray();
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function removeAction() {
	$user = LoginController::requireAuth();

        $data = $GLOBALS['POST']->{'recipe_planned_id'};

        $result["status"] = true;

	$condition = new Condition("[c1] AND [c2]", array(
		"[c1]" => [
			[RecipeConsumptionGroupAggPlannedModel::class, RecipeConsumptionGroupAggPlannedModel::FIELD_ID],
			Condition::COMPARISON_EQUALS,
			[Condition::CONDITION_CONST, $data]
		],
		"[c2]" => [
			[RecipeConsumptionGroupAggPlannedModel::class, RecipeConsumptionGroupAggPlannedModel::FIELD_USERS_ID],
			Condition::COMPARISON_EQUALS,
			[Condition::CONDITION_CONST, $user->getId()]
		]
	));

	$rcg_agg_p = new RecipeConsumptionGroupAggPlannedModel();
	$rcg_agg_p->find($condition);
	if ($rcg_agg_p->next()) {
		$result["removed_recipe_planned_id"] = $rcg_agg_p->getId();
		$rcg_agg_p->delete();
	} else {
		$result["status"] = false;
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }
}
