<?php

namespace FooDBar;

use \Frame\Condition as Condition;
use \Frame\Order as Order;

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "UsersTargetModel.php";

class TargetController {
    private $DefaultController = false;
    private $DefaultAction = "get";

    public function getAction() {
	$user = LoginController::requireAuth();

	$user_target_cond = new Condition("[c1]", array(
		"[c1]" => [
				[UsersTargetModel::class, UsersTargetModel::FIELD_USERS_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $user->getId()]
			]
	));

	$order_target = new Order(UsersTargetModel::class, UsersTargetModel::FIELD_DATE_INSERT, Order::ORDER_DESC);

	$users_target = new UsersTargetModel();
	$users_target->find($user_target_cond, null, array($order_target));

	$result = array();
	$result["status"] = true;
	$result["no_target"] = true;
	$result["target"] = new \stdClass();
	while ($users_target->next()) {
		$result["no_target"] = false;
		$result["target"]->{$users_target->getDateInsert()} = $users_target->toArray();
	}

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function insertAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'target'};

	$user_target = new UsersTargetModel();
	$user_target->setUsersId($user->getId());
	$user_target->setBmi($data->{'Bmi'});
	if ($data->{'FatPercent'} != "") {
		$user_target->setFatPercent($data->{'FatPercent'});
	}
	if ($data->{'MusclePercent'} != "") {
		$user_target->setMusclePercent($data->{'MusclePercent'});
	}

	$today = date_create();
        $date_now = $today->format("Y-m-d");
	$user_target->setDateInsert($date_now);

	$user_target->insert();

	$result = array();
	$result["status"] = true;
	$result["new_target"] = $user_target->toArray();

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function removeAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'target_id'};

	$user_target_cond = new Condition("[c1] AND [c2]", array(
                "[c1]" => [
                                [UsersTargetModel::class, UsersTargetModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $user->getId()]
                        ],
		"[c2]" => [
				[UsersTargetModel::class, UsersTargetModel::FIELD_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $data]
			]
        ));

        $users_target = new UsersTargetModel();
        $users_target->find($user_target_cond);

	$result = array();
	$result["status"] = false;
	if ($users_target->next()) {
		$result["deleted_target"] = $users_target->toArray();
		$result["status"] = true;
		$users_target->delete();
	}

        exit(json_encode($result, JSON_PRETTY_PRINT));
    }
}
