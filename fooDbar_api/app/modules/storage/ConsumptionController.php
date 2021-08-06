<?php

namespace FooDBar;

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "Fields.php";
use \Frame\Fields as Fields;
require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "Join.php";
use \Frame\Join as Join;
use \Frame\Condition as Condition;
use \Frame\Order as Order;

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "StorageModel.php";
require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "StoragesModel.php";
require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "StoragesMembershipModel.php";

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "StorageConsumptionModel.php";

class ConsumptionController {
    private $DefaultController = false;
    private $DefaultAction = "get";

    public function getAction() {
	$user = LoginController::requireAuth();

	$result = array();
        $result["status"] = true;

	/* filter by current users storages_membership */
	$storages_membership_condition = new Condition("[c1]", array(
                "[c1]" => [
                                [StoragesMembershipModel::class, StoragesMembershipModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $user->getId()]
                        ]
        ));

	/* get user name of consumption */
	$consumption_user_join = new Join(new UsersModel(), "[j1]", array(
		"[j1]" => [
                                [StorageConsumptionModel::class, StorageConsumptionModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [UsersModel::class, UsersModel::FIELD_ID]
                        ]
	));

	/* get storages_id and products_id */
	$consumption_storage_join = new Join(new StorageModel(), "[j2]", array(
		"[j2]" => [
                                [StorageConsumptionModel::class, StorageConsumptionModel::FIELD_STORAGE_ID],
                                Condition::COMPARISON_EQUALS,
                                [StorageModel::class, StorageModel::FIELD_ID]
                        ]
	));

	/* get storages_membership */
        $storages_membership_join = new Join(new StoragesMembershipModel(), "[j3]", array(
                "[j3]" => [
                                [StorageModel::class, StorageModel::FIELD_STORAGES_ID],
                                Condition::COMPARISON_EQUALS,
                                [StoragesMembershipModel::class, StoragesMembershipModel::FIELD_STORAGES_ID]
                        ]
        ));

	$fields = new Fields(array());
	$fields->addField(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_ID);
	$fields->addField(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_AMOUNT);
	$fields->addField(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_DATETIME);
	$fields->addField(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_RECIPE_ID);
	$fields->addField(UsersModel::class, UsersModel::FIELD_NAME);
	$fields->addField(StorageModel::class, StorageModel::FIELD_STORAGES_ID);
	$fields->addField(StorageModel::class, StorageModel::FIELD_PRODUCTS_ID);

	$consumption = new StorageConsumptionModel();
	$consumption->find($consumption_cond, array($consumption_user_join, $consumption_storage_join, $storages_membership_join), null, null, $fields);

	$result["consumption"] = new \stdClass();
	while ($consumption->next()) {
		$users_model = $consumption->joinedModelByClass(UsersModel::class);
		$storage_model = $consumption->joinedModelByClass(StorageModel::class);

		$result["consumption"]->{$consumption->getId()}["Id"] = $consumption->getId();
		$result["consumption"]->{$consumption->getId()}["Amount"] = $consumption->getAmount();
		$result["consumption"]->{$consumption->getId()}["Datetime"] = $consumption->getDatetime();
		$result["consumption"]->{$consumption->getId()}["User"] = $users_model->getName();
		$result["consumption"]->{$consumption->getId()}["StoragesId"] = $storage_model->getStoragesId();
		$result["consumption"]->{$consumption->getId()}["ProductsId"] = $storage_model->getProductsId();
	}

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function addAction() {
	$user = LoginController::requireAuth();

	$result["status"] = false;

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function undoAction() {
	$user = LoginController::requireAuth();

	$result["status"] = false;

        exit(json_encode($result, JSON_PRETTY_PRINT));
    }
}
