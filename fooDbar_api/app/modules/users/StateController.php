<?php

namespace FooDBar\Users;

use \Frame\Condition as Condition;
use \Frame\Order as Order;


$GLOBALS['Boot']->loadModel("UsersStateModel");

use \FooDBar\UsersStateModel as UsersStateModel;


class StateController {
    private $DefaultController = false;
    private $DefaultAction = "get";

    public function getAction() {
	$user = LoginController::requireAuth();

	$user_state_cond = new Condition("[c1]", array(
		"[c1]" => [
				[UsersStateModel::class, UsersStateModel::FIELD_USERS_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $user->getId()]
			]
	));

	$order_state = new Order(UsersStateModel::class, UsersStateModel::FIELD_ID, Order::ORDER_DESC);

	$users_state = new UsersStateModel();
	$users_state->find($user_state_cond, null, array($order_state));

	$result = array();
	$result["status"] = true;
	$result["no_state"] = true;
	$result["state"] = new \stdClass();
	while ($users_state->next()) {
		$result["no_state"] = false;
		$result["state"]->{$users_state->getId()} = $users_state->toArray();
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function insertAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'state'};

	$user_state = new UsersStateModel();
	$user_state->setUsersId($user->getId());
	$user_state->setHeight($data->{'Height'});
	$user_state->setWeight($data->{'Weight'});
	if ($data->{'BonePercent'} != "") {
		$user_state->setBonePercent($data->{'BonePercent'});
	}
	if ($data->{'FatPercent'} != "") {
		$user_state->setFatPercent($data->{'FatPercent'});
	}
	if ($data->{'WaterPercent'} != "") {
		$user_state->setWaterPercent($data->{'WaterPercent'});
	}
	if ($data->{'MusclePercent'} != "") {
		$user_state->setMusclePercent($data->{'MusclePercent'});
	}

	$today = date_create();
        $date_now = $today->format("Y-m-d H:i:s");
	$user_state->setDatetimeInsert($date_now);

	$user_state->setPal(floatval($data->{'Pal'}));
	$user_state->insert();

	$result = array();
	$result["status"] = true;
	$result["new_state"] = $user_state->toArray();

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function removeAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'state_id'};

	$user_state_cond = new Condition("[c1] AND [c2]", array(
                "[c1]" => [
                                [UsersStateModel::class, UsersStateModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $user->getId()]
                        ],
		"[c2]" => [
				[UsersStateModel::class, UsersStateModel::FIELD_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $data]
			]
        ));

        $users_state = new UsersStateModel();
        $users_state->find($user_state_cond);

	$result = array();
	$result["status"] = false;
	if ($users_state->next()) {
		$result["deleted_state"] = $users_state->toArray();
		$result["status"] = true;
		$users_state->delete();
	}

        exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }
}
