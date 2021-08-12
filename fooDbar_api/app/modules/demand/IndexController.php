<?php

namespace FooDBar\Demand;

use \FooDBar\Users\LoginController as LoginController;

$GLOBALS['Boot']->loadDBExt("Join");

use \Frame\Join 	as Join;
use \Frame\Condition 	as Condition;
use \Frame\Order 	as Order;


$GLOBALS['Boot']->loadModel("UsersStateModel");
$GLOBALS['Boot']->loadModel("UsersTargetModel");

use \FooDBar\UsersStateModel as UsersStateModel;
use \FooDBar\UsersTargetModel as UsersTargetModel;

class IndexController {
    private $DefaultController = true;
    private $DefaultAction = "index";
/*
    const GENDER = array(
			0 => 'Male',
			1 => 'Female'
		);
*/

    const AGE_GROUPS = array(
				//age_group_id	age_to
				0 =>		3,
                                1 =>    	10,
                                2 =>    	18,
                                3 =>    	30,
                                4 =>    	60,
                                5 =>    	200
			);

    const BMR = array(
				//gender_id	age_group_id	kg_factor	scalar_correction
				0 => array(
						0 => array(	0.249,		-0.127		),
						1 => array(	0.095,		2.11		),
                                                2 => array(	0.074,		2.754           ),
                                                3 => array(	0.063,		2.896           ),
                                                4 => array(	0.048,		3.653           ),
                                                5 => array(	0.049,		2.459           )	),
                                1 => array(
                                                0 => array(	0.244,		-0.13           ),
                                                1 => array(	0.085,		2.033           ),
                                                2 => array(	0.056,		2.898           ),
                                                3 => array(	0.062,		2.036           ),
                                                4 => array(	0.034,		3.538           ),
                                                5 => array(	0.038,		2.755           )	)
			);


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

		$age_group_id = 0;			/* AGE GROUP ID */
		for ($a = 0; $a < count(self::AGE_GROUPS); $a++) {
			if ($age < self::AGE_GROUPS[$a]) {
				$age_group_id = $a;
				break;
			}
		}

		$bmi_current = $users_state->getWeight() / ($users_state->getHeight() * $users_state->getHeight());	/* BMI CURRENT */

		$bmr_kg_factor = self::BMR[$user->getGenderId()][$age_group_id][0];
		$bmr_scalar_correction = self::BMR[$user->getGenderId()][$age_group_id][1];

		$MJperDay_pal_1_0 = $bmr_kg_factor*$users_state->getWeight() + $bmr_scalar_correction;
		$MJperDay_pal_user = $users_state->getPal() * $MJperDay_pal_1_0;	/* MJ/day maintain */


		$result["MJPerDay"] = array(
			 "maintain"      => $MJperDay_pal_user
		);

		$users_target = $users_state->joinedModelByClass(UsersTargetModel::class);
		if (!is_null($users_target->getBmi())) {
			$bmi_target = $users_target->getBmi();
			$kg_target = $bmi_target * ($users_state->getHeight() * $users_state->getHeight());
			$MJperDay_pal_1_0_target = $bmr_kg_factor*$kg_target + $bmr_scalar_correction;
			$MJperDay_pal_user_target = $users_state->getPal() * $MJperDay_pal_1_0_target;	/* MJ/day target */

			$result["MJPerDay"]["target"] = $MJperDay_pal_user_target;
		}
	}

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }
}
