<?php

namespace FooDBar\Storage;

use \FooDBar\Users\LoginController as LoginController;

$GLOBALS['Boot']->loadDBExt("Fields");
$GLOBALS['Boot']->loadDBExt("Join");
$GLOBALS['Boot']->loadDBExt("GroupBy");
$GLOBALS['Boot']->loadDBExt("DBFunction");
$GLOBALS['Boot']->loadDBExt("DBFunctionExpression");

use \Frame\Fields 		as Fields;
use \Frame\Join 		as Join;
use \Frame\Condition 		as Condition;
use \Frame\Order 		as Order;
use \Frame\GroupBy 		as GroupBy;
use \Frame\DBFunction 		as DBFunction;
use \Frame\DBFunctionExpression as DBFunctionExpression;


$GLOBALS['Boot']->loadModel("StorageModel");
$GLOBALS['Boot']->loadModel("StoragesModel");
$GLOBALS['Boot']->loadModel("StoragesMembershipModel");
$GLOBALS['Boot']->loadModel("StorageConsumptionModel");
$GLOBALS['Boot']->loadModel("ProductsModel");

use \FooDBar\StorageModel 		as StorageModel;
use \FooDBar\StoragesModel 		as StoragesModel;
use \FooDBar\StoragesMembershipModel	as StoragesMembershipModel;
use \FooDBar\StorageConsumptionModel 	as StorageConsumptionModel;
use \FooDBar\ProductsModel 		as ProductsModel;
use \FooDBar\UsersModel			as UsersModel;

class ConsumptionController {
    private $DefaultController = false;
    private $DefaultAction = "get";

    public function getAction() {
	$user = LoginController::requireAuth();

	$result = array();
        $result["status"] = true;

	$storages_membership_condition = new Condition("[c1]", array(
                "[c1]" => [
                                [StoragesMembershipModel::class, StoragesMembershipModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $user->getId()]
                        ]
        ));

	$consumption_user_join = new Join(new UsersModel(), "[j1]", array(
		"[j1]" => [
                                [StorageConsumptionModel::class, StorageConsumptionModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [UsersModel::class, UsersModel::FIELD_ID]
                        ]
	));

	$consumption_storage_join = new Join(new StorageModel(), "[j2]", array(
		"[j2]" => [
                                [StorageConsumptionModel::class, StorageConsumptionModel::FIELD_STORAGE_ID],
                                Condition::COMPARISON_EQUALS,
                                [StorageModel::class, StorageModel::FIELD_ID]
                        ]
	));

        $storages_membership_join = new Join(new StoragesMembershipModel(), "[j3]", array(
                "[j3]" => [
                                [StorageModel::class, StorageModel::FIELD_STORAGES_ID],
                                Condition::COMPARISON_EQUALS,
                                [StoragesMembershipModel::class, StoragesMembershipModel::FIELD_STORAGES_ID]
                        ]
        ));

	$fields = new Fields(array());
	$fields->addField(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_ID);
	$fields->addField(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_STORAGE_ID);
	$fields->addField(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_AMOUNT);
	$fields->addField(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_DATETIME);
	$fields->addField(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_RECIPE_ID);
	$fields->addField(UsersModel::class, UsersModel::FIELD_NAME);
	$fields->addField(StorageModel::class, StorageModel::FIELD_STORAGES_ID);
	$fields->addField(StorageModel::class, StorageModel::FIELD_PRODUCTS_ID);

	$consumption = new StorageConsumptionModel();
	$consumption->find($storages_membership_condition, array($consumption_user_join, $consumption_storage_join, $storages_membership_join), null, null, $fields);

	$result["consumption"] = new \stdClass();
	while ($consumption->next()) {
		$users_model = $consumption->joinedModelByClass(UsersModel::class);
		$storage_model = $consumption->joinedModelByClass(StorageModel::class);

		$result["consumption"]->{$consumption->getId()}["Id"] = $consumption->getId();
		$result["consumption"]->{$consumption->getId()}["StorageId"] = $consumption->getStorageId();
		$result["consumption"]->{$consumption->getId()}["Amount"] = $consumption->getAmount();
		$result["consumption"]->{$consumption->getId()}["Datetime"] = $consumption->getDatetime();
		$result["consumption"]->{$consumption->getId()}["User"] = $users_model->getName();
		$result["consumption"]->{$consumption->getId()}["StoragesId"] = $storage_model->getStoragesId();
		$result["consumption"]->{$consumption->getId()}["ProductsId"] = $storage_model->getProductsId();
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function addAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'consumption_item'};
	$trash = false;
	if (isset($GLOBALS['POST']->{'trash'})) {
		if ($GLOBALS['POST']->{'trash'} == true) {
			$trash = true;
		}
	}

	$storage_cond = new Condition("[c1] AND [c2] AND [c3] AND [c4]", array(
		"[c1]" => [
				[StorageModel::class, StorageModel::FIELD_STORAGES_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $data->{'StoragesId'}]
			],
		"[c2]" => [
				[StorageModel::class, StorageModel::FIELD_PRODUCTS_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $data->{'ProductsId'}]
			],
		"[c3]" => [
				[StorageModel::class, StorageModel::FIELD_DATETIME_EMPTY],
				Condition::COMPARISON_IS,
				[Condition::CONDITION_RESERVED, Condition::RESERVED_NULL]
			],
		"[c4]" => [
                                [StoragesMembershipModel::class, StoragesMembershipModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $user->getId()]
                        ]
	));

	$storages_membership_join = new Join(new StoragesMembershipModel(), "[j1]", array(
                "[j1]" => [
                                [StorageModel::class, StorageModel::FIELD_STORAGES_ID],
                                Condition::COMPARISON_EQUALS,
                                [StoragesMembershipModel::class, StoragesMembershipModel::FIELD_STORAGES_ID]
                        ]
        ));


	$sum_expr = new DBFunctionExpression("[c1]", array(
		"[c1]" => [StorageModel::class, StorageModel::FIELD_AMOUNT]
	));

	$fields = new Fields(array());
	$fields->addFunctionField("SumAmount", DBFunction::FUNCTION_SUM, $sum_expr);

	$group_by = new GroupBy(StorageModel::class, StorageModel::FIELD_PRODUCTS_ID);

	$storage = new StorageModel();
	$storage->find($storage_cond, array($storages_membership_join), null, null, $fields, $group_by);

	$result = array();
	$result["status"] = false;
	if ($storage->next()) {
		$available_amount = floatval($storage->DBFunctionResult("SumAmount"));

		$requested_amount = $data->{'Amount'};

		if ($available_amount < $requested_amount) {
			$result["error"] = "not enough product in storage";
			$result["SumAmount"] = $available_amount;
		} else {
			$result["status"] = true;
			$result["new_consumption_item"] = new \stdClass();

			$order = new Order(StorageModel::class, StorageModel::FIELD_DATETIME_INSERT, Order::ORDER_ASC);

			$storage_consume = new StorageModel();
			$storage_consume->find($storage_cond, array($storages_membership_join), $order);

			$consumed_amount = 0;

			$date_f = $data->{'Datetime'};
			if (strlen($date_f) == 0) {
				$date_now = date_create();
				$date_f = $date_now->format("Y-m-d H:i:s");
			}

			while ($storage_consume->next()) {
				$amount = $storage_consume->getAmount();

				if (is_null($storage_consume->getDatetimeOpen())) {
					$storage_consume->setDatetimeOpen($date_f);
				}

				$consumption = new StorageConsumptionModel();
				$consumption->setStorageId($storage_consume->getId());

				if ($amount > $requested_amount) {
					$consumption->setAmount($requested_amount);

					$amount -= $requested_amount;
					$requested_amount = 0.0;
				} else if ($amount <= $requested_amount){
                                        $consumption->setAmount($amount);

					$requested_amount -= $amount;
					$amount = 0.0;

					$storage_consume->setDatetimeEmpty($date_f);
				}

				$storage_consume->setAmount($amount);
				$storage_consume->save();

				$consumption->setDatetime($date_f);
				$username = $user->getName();
				if ($trash) {
					$consumption->setUsersId(0);
					$username = "Trash";
				} else {
	                                $consumption->setUsersId($user->getId());
				}
                                $consumption->insert();


				$result["new_consumption_item"]->{$consumption->getId()}["Id"] = $consumption->getId();
		                $result["new_consumption_item"]->{$consumption->getId()}["Amount"] = $consumption->getAmount();
                		$result["new_consumption_item"]->{$consumption->getId()}["Datetime"] = $consumption->getDatetime();
		                $result["new_consumption_item"]->{$consumption->getId()}["User"] = $username;
                		$result["new_consumption_item"]->{$consumption->getId()}["StoragesId"] = $storage_consume->getStoragesId();
                		$result["new_consumption_item"]->{$consumption->getId()}["ProductsId"] = $storage_consume->getProductsId();

				if ($requested_amount == 0) {
					break;
				}
			}
		}
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function undoAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'consumption_item_id'};

	$consumption_cond = new Condition("[c1] AND [c2]", array(
		"[c1]" => [
				[StorageConsumptionModel::class, StorageConsumptionModel::FIELD_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $data]
		],
		"[c2]" => [
				[StoragesMembershipModel::class, StoragesMembershipModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $user->getId()]
			]

	));

	$storage_join = new Join(new StorageModel(), "[j1]", array(
		"[j1]" => [
				[StorageConsumptionModel::class, StorageConsumptionModel::FIELD_STORAGE_ID],
				Condition::COMPARISON_EQUALS,
				[StorageModel::class, StorageModel::FIELD_ID]
			]
	));

	$product_join = new Join(new ProductsModel(), "[j2]", array(
		"[j2]" => [
				[StorageModel::class, StorageModel::FIELD_PRODUCTS_ID],
				Condition::COMPARISON_EQUALS,
				[ProductsModel::class, ProductsModel::FIELD_ID]
			]
	));

	$storage_membership_join = new Join(new StoragesMembershipModel(), "[j3]", array(
		"[j3]" => [
				[StorageModel::class, StorageModel::FIELD_STORAGES_ID],
				Condition::COMPARISON_EQUALS,
				[StoragesMembershipModel::class, StoragesMembershipModel::FIELD_STORAGES_ID]
			]
	));

	$consumption = new StorageConsumptionModel();
	$consumption->find($consumption_cond, array($storage_join, $product_join, $storage_membership_join));

	$result = array();
        $result["status"] = true;
	if ($consumption->next()) {
		$storage = $consumption->joinedModelByClass(StorageModel::class);
		$amount = $storage->getAmount() + $consumption->getAmount();
		$storage->setAmount($amount);
		if (!is_null($storage->getDatetimeEmpty())) {
			$storage->setDatetimeEmpty(null);
		}

		$product = $consumption->joinedModelByClass(ProductsModel::class);
		if ($product->getAmount() == $amount) {
			$storage->setDatetimeOpen(null);
		}
		$storage->save();

		$result["deleted_consumption_item"] = array( 'Id' => $consumption->getId() );
		$consumption->delete();
	} else {
		$result["status"] = false;
                $result["error"] = "item not found/accessible";
	}

        exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }
}
