<?php

namespace FooDBar;

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "Join.php";
use \Frame\Join as Join;

//require_once $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "Condition.php";
use \Frame\Condition as Condition;

//require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "Order.php";
use \Frame\Order as Order;

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "AgeGroupModel.php";
require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "UsersStateModel.php";
require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "UsersTargetModel.php";
require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "EnergyBmrModel.php";

class IndexController {
    private $DefaultController = true;
    private $DefaultAction = "index";

    public function indexAction() {
	$user = LoginController::requireAuth();

	$user_state_cond = new Condition("[c1]", array(
		"[c1]" => [
				[UsersStateModel::class, UsersStateModel::FIELD_USERS_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $user->getId()]
			]
	));

	$user_target_join = new Join(new UsersTargetModel(), "[j1]", array(
		"[j1]" => [
			[UsersStateModel::class, UsersStateModel::FIELD_USERS_ID],
                        Condition::COMPARISON_EQUALS,
                        [UsersTargetModel::class, UsersTargetModel::FIELD_USERS_ID]
		]
	), Join::JOIN_LEFT);

	$order_state = new Order(UsersStateModel::class, UsersStateModel::FIELD_DATETIME_INSERT, Order::ORDER_DESC);
	$order_target = new Order(UsersTargetModel::class, UsersTargetModel::FIELD_DATE_INSERT, Order::ORDER_DESC);

	$users_state = new UsersStateModel();
	$users_state->find($user_state_cond, array($user_target_join), array($order_state, $order_target));

	$result = array();
	$result["status"] = false;
	if ($users_state->next()) {
		$result["status"] = true;
		$birthdate = $user->getBirthdate();
		$bdate = date_create_from_format("Y-m-d", $birthdate);
		$today = date_create();

		$age_interval = $bdate->diff($today);
		$age = $age_interval->y;		/* AGE */

		$age_group_cond = new Condition("[c1] AND [c2]", array(
		"[c1]" => [
                                [AgeGroupModel::class, AgeGroupModel::FIELD_AGE_FROM],
                                Condition::COMPARISON_LESS_EQUALS,
                                [Condition::CONDITION_CONST, $age]
                        ],
                "[c2]" => [
                                [AgeGroupModel::class, AgeGroupModel::FIELD_AGE_TO],
                                Condition::COMPARISON_GREATER,
                                [Condition::CONDITION_CONST, $age]
                        ]
		));

		$age_group_join_bmr = new Join(new EnergyBmrModel(), "[j1]", array(
	                "[j1]" => [
                                [AgeGroupModel::class, AgeGroupModel::FIELD_ID],
                                Condition::COMPARISON_EQUALS,
                                [EnergyBmrModel::class, EnergyBmrModel::FIELD_AGE_GROUP_ID]
                        ]
		));

		$age_group = new AgeGroupModel();
		$age_group->find($age_group_cond, array($age_group_join_bmr));
		$age_group->next();
		$age_group_id = $age_group->getId();	/* AGE GROUP ID */

		$bmi_current = $users_state->getWeight() / ($users_state->getHeight() * $users_state->getHeight());	/* BMI CURRENT */

		$bmr = $age_group->joinedModelByClass(EnergyBmrModel::class);
		$MJperDay_pal_1_0 = $bmr->getKgFactor()*$users_state->getWeight() + $bmr->getScalarCorrection();
		$MJperDay_pal_user = $users_state->getPal() * $MJperDay_pal_1_0;	/* MJ/day maintain */


		$result["MJ/day"] = array(
			 "maintain"      => $MJperDay_pal_user
		);

		$users_target = $users_state->joinedModelByClass(UsersTargetModel::class);
		if (!is_null($users_target->getBmi())) {
			$bmi_target = $users_target->getBmi();
			$kg_target = $bmi_target * ($users_state->getHeight() * $users_state->getHeight());
			$MJperDay_pal_1_0_target = $bmr->getKgFactor()*$kg_target + $bmr->getScalarCorrection();
			$MJperDay_pal_user_target = $users_state->getPal() * $MJperDay_pal_1_0_target;	/* MJ/day target */

			$result["MJ/day"]["target"] = $MJperDay_pal_user_target;
		}
	}

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }
}
