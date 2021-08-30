<?php

namespace FooDBar\Recipe;

use \FooDBar\Users\LoginController as LoginController;

$GLOBALS['Boot']->loadDBExt("Join");

use \Frame\Condition		as Condition;
use \Frame\Join 		as Join;

$GLOBALS['Boot']->loadModel("RecipeConsumptionGroupModel");
$GLOBALS['Boot']->loadModel("RecipeConsumptionGroupAllergiesModel");
$GLOBALS['Boot']->loadModel("RecipeConsumptionGroupAggModel");

use \FooDBar\RecipeConsumptionGroupModel 		as RecipeConsumptionGroupModel;
use \FooDBar\RecipeConsumptionGroupAllergiesModel 	as RecipeConsumptionGroupAllergiesModel;
use \FooDBar\RecipeConsumptionGroupAggModel 		as RecipeConsumptionGroupAggModel;

class ProcessconsumptionController {
    private $DefaultController = false;
    private $DefaultAction = "get";

/*
    public static function optimizeNutritionDistributionSingle($consumption_groups_nutrition, $kj, $fat_percent, $carbs_percent, $protein_percent, $conditions) {
		$amount_factors = array();
		$weights = array();

		$products_ct = count($consumption_groups_nutrition["p_parts"]);
		$products_ids = array_keys($consumption_groups_nutrition["p_parts"]);
		$total_nutrition = round($consumption_groups_nutrition["Fat"] + $consumption_groups_nutrition["Carbs"] + $consumption_groups_nutrition["Protein"] + $consumption_groups_nutrition["Salt"] + $consumption_groups_nutrition["Fiber"], 2);
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
					"weight_factors"	=> array($products_ids[0] => 1.0),
					"distribution" 		=> $distribution_current,
					"nutrition"	 	=> round($amount_multiplier * $total_nutrition, 2),
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
				"weight_factors"	=> $weights,
				"distribution" 		=> $distribution_current,
				"nutrition" 		=> round($amount_multiplier * $total_nutrition, 2),
				"distribution_start" 	=> $distribution_start,
				"nutrition_start" 	=> $total_nutrition);
    }
*/

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
	$datefrom = 0;

	$date_now = date_create();
	$date_f = $date_now->format("Y-m-d H:i:s");

	$cond = new Condition("[c1] AND [c2]", array(
		"[c1]" => [
                                [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_DATETIME],
                                Condition::COMPARISON_LESS_EQUALS,
                                [Condition::CONDITION_CONST, $date_f]
                        ],
                "[c2]" => [
                                [RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_DATETIME],
                                Condition::COMPARISON_GREATER,
                                [Condition::CONDITION_CONST, $datefrom]
                        ]
	));

	$join_a = new Join(new RecipeConsumptionGroupAllergiesModel(), "[j1]", array(
		"[j1]"	=> [
				[RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_RECIPE_CONSUMPTION_GROUP_ALLERGIES_ID],
				Condition::COMPARISON_EQUALS,
				[RecipeConsumptionGroupAllergiesModel::class, RecipeConsumptionGroupAllergiesModel::FIELD_ID]
		]
	));

	$recipe_consumption_group = new RecipeConsumptionGroupModel();
	$recipe_consumption_group->find($cond, array($join_a));

	$result["recipe_consumption_group"] = new \stdClass();
	while ($recipe_consumption_group->next()) {
		$result["recipe_consumption_group"]->{$recipe_consumption_group->getId()} = $recipe_consumption_group->toArray();
		unset($result["recipe_consumption_group"]->{$recipe_consumption_group->getId()}["UsersId"]);

		$allergies = $recipe_consumption_group->joinedModelByClass(RecipeConsumptionGroupAllergiesModel::class);

		$result["recipe_consumption_group"]->{$recipe_consumption_group->getId()} = array_merge($result["recipe_consumption_group"]->{$recipe_consumption_group->getId()}, $allergies->toArray());
	}

	$join_rcg = new Join(new RecipeConsumptionGroupModel(), "[j1]", array(
		"[j1]" => [
				[RecipeConsumptionGroupAggModel::class, RecipeConsumptionGroupAggModel::FIELD_RECIPE_CONSUMPTION_GROUP_ID],
				Condition::COMPARISON_EQUALS,
				[RecipeConsumptionGroupModel::class, RecipeConsumptionGroupModel::FIELD_ID]
		]
	));

	$recipe_consumption_group_agg = new RecipeConsumptionGroupAggModel();
	$recipe_consumption_group_agg->find(null, array($join_rcg));
	$result["recipe_consumption_group_agg"] = new \stdClass();
	while ($recipe_consumption_group_agg->next()) {
		$result["recipe_consumption_group_agg"]->{$recipe_consumption_group_agg->getId()} = $recipe_consumption_group_agg->toArray();

		$rcg = $recipe_consumption_group_agg->joinedModelByClass(RecipeConsumptionGroupModel::class);
		$result["recipe_consumption_group_agg"]->{$recipe_consumption_group_agg->getId()}["ProductsIds"] = $rcg->getProductsIds();
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }
}
