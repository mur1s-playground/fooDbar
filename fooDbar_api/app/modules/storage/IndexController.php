<?php

namespace FooDBar\Storage;

use \FooDBar\Users\LoginController as LoginController;

$GLOBALS['Boot']->loadDBExt("Fields");
$GLOBALS['Boot']->loadDBExt("Join");
$GLOBALS['Boot']->loadDBExt("GroupBy");
$GLOBALS['Boot']->loadDBExt("DBFunction");
$GLOBALS['Boot']->loadDBExt("DBFunctionExpression");

use \Frame\Fields               as Fields;
use \Frame\Join                 as Join;
use \Frame\Condition            as Condition;
use \Frame\Order                as Order;
use \Frame\GroupBy              as GroupBy;
use \Frame\DBFunction           as DBFunction;
use \Frame\DBFunctionExpression as DBFunctionExpression;


$GLOBALS['Boot']->loadModel("StorageModel");
$GLOBALS['Boot']->loadModel("StoragesModel");
$GLOBALS['Boot']->loadModel("StoragesMembershipModel");
$GLOBALS['Boot']->loadModel("ProductsSourceModel");
$GLOBALS['Boot']->loadModel("ProductsPriceModel");
$GLOBALS['Boot']->loadModel("StorageConsumptionModel");

use \FooDBar\StorageModel 		as StorageModel;
use \FooDBar\StoragesModel 		as StoragesModel;
use \FooDBar\StoragesMembershipModel 	as StoragesMembershipModel;
use \FooDBar\ProductsSourceModel 	as ProductsSourceModel;
use \FooDBar\ProductsPriceModel 	as ProductsPriceModel;
use \FooDBar\StorageConsumptionModel	as StorageConsumptionModel;

use \FooDBar\Users\ProductssourceController 	as ProductssourceController;
use \FooDBar\Products\PriceController 		as PriceController;
use \FooDBar\Shopping				as Shopping;
use \FooDBar\Users\LimitController		as LimitController;

class IndexController {
    private $DefaultController = true;
    private $DefaultAction = "get";

    public static function getStorages($user) {
	$storages_membership_condition = new Condition("[c1]", array(
		"[c1]" => [
				[StoragesMembershipModel::class, StoragesMembershipModel::FIELD_USERS_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $user->getId()]
			]
	));

	$storages_membership_join = new Join(new StoragesMembershipModel(), "[j1]", array(
		"[j1]" => [
				[StoragesModel::class, StoragesModel::FIELD_ID],
				Condition::COMPARISON_EQUALS,
				[StoragesMembershipModel::class, StoragesMembershipModel::FIELD_STORAGES_ID]
			]
	));

	$storages = new StoragesModel();
	$storages->find($storages_membership_condition, $storages_membership_join);

	$result = new \stdClass();
	while ($storages->next()) {
		$result->{$storages->getId()} = $storages->toArray();
	}
	return $result;
    }

    public static function requireStorageMembership($user, $storages_id) {
	$storages = self::getStorages($user);
        $s_id_array = array();
        foreach ($storages as $id => $storage_obj) {
                if ($storages_id == $id) {
			return;
                }
        }

	$result = array("status" => false, "error" => "permission denied");
        exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public static function getStoragesContent($user) {
	$storages = self::getStorages($user);
        $s_id_array = array();
        foreach ($storages as $id => $storage_obj) {
                $s_id_array[] = $id;
        }

	$storage_cond = new Condition("[c1] AND [c2]", array(
                "[c1]" => [
                                [StorageModel::class, StorageModel::FIELD_DATETIME_EMPTY],
                                Condition::COMPARISON_IS,
                                [Condition::CONDITION_RESERVED, Condition::RESERVED_NULL]
                        ],
                "[c2]" => [
                                [StorageModel::class, StorageModel::FIELD_STORAGES_ID],
                                Condition::COMPARISON_IN,
                                [Condition::CONDITION_CONST_ARRAY, $s_id_array]
                        ]
	));

	$storage = new StorageModel();
	$storage->find($storage_cond);

	$result = new \stdClass();
	while ($storage->next()) {
		$result->{$storage->getId()} = $storage->toArray();
	}
	return $result;
    }

    public function getAction() {
	$user = LoginController::requireAuth();

	$result = array();
        $result["status"] = true;

	/* USERS SOURCE LOCATIONS */
	$GLOBALS['Boot']->loadModule("users", "Productssource");
	$result['products_source'] = ProductssourceController::getUsersProductsSources($user);
	/* ---------------------- */

	$storages = self::getStorages($user);
	$s_id_array = array();
	foreach ($storages as $id => $storage_obj) {
		$s_id_array[] = $id;
	}

	$storage_cond = new Condition("/*[c1] AND */[c2] AND [c3]", array(
/*
                "[c1]" => [
                                [StorageModel::class, StorageModel::FIELD_DATETIME_EMPTY],
                                Condition::COMPARISON_IS,
                                [Condition::CONDITION_RESERVED, Condition::RESERVED_NULL]
                        ],
*/
		"[c2]" => [
				[StorageModel::class, StorageModel::FIELD_STORAGES_ID],
				Condition::COMPARISON_IN,
				[Condition::CONDITION_CONST_ARRAY, $s_id_array]
			],
		"[c3]" => [
				[ProductsPriceModel::class, ProductsPriceModel::FIELD_DATETIME],
				Condition::COMPARISON_LESS_EQUALS,
				[StorageModel::class, StorageModel::FIELD_DATETIME_INSERT]
			]
        ));


        $storage_products_source_join = new Join(new ProductsSourceModel(), "[j1]", array(
                "[j1]" => [
                                [StorageModel::class, StorageModel::FIELD_PRODUCTS_SOURCE_ID],
				Condition::COMPARISON_EQUALS,
				[ProductsSourceModel::class, ProductsSourceModel::FIELD_ID]
                        ]
        ));

	$storage_products_price = new Join(new ProductsPriceModel(), "[j2] AND [j3]", array(
		"[j2]" => [
				[StorageModel::class, StorageModel::FIELD_PRODUCTS_ID],
				Condition::COMPARISON_EQUALS,
				[ProductsPriceModel::class, ProductsPriceModel::FIELD_PRODUCTS_ID]
			],
		"[j3]" => [
				[StorageModel::class, StorageModel::FIELD_PRODUCTS_SOURCE_ID],
				Condition::COMPARISON_EQUALS,
				[ProductsPriceModel::class, ProductsPriceModel::FIELD_PRODUCTS_SOURCE_ID]
			]
	));

	$order = new Order(ProductsPriceModel::class, ProductsPriceModel::FIELD_DATETIME, Order::ORDER_DESC);

	$storage = new StorageModel();
	$storage->find($storage_cond, array($storage_products_source_join, $storage_products_price));

	$result["storage"] = new \stdClass();
	while ($storage->next()) {
		$products_price = $storage->joinedModelByClass(ProductsPriceModel::class);

		$result["storage"]->{$storage->getId()} = $storage->toArray();
		$result["storage"]->{$storage->getId()}["Price"] = $products_price->getPrice();
	}
	$result["storages"] = $storages;

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function insertAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'storage_item'};

	$GLOBALS['Boot']->loadModule("users", "Limit");
        $result = array();
        if (LimitController::countInOrDecrement($user, LimitController::LIMIT_FIELD_STORAGE)) {

	        self::requireStorageMembership($user, $data->{'StoragesId'});

		$today = date_create();
        	$date_now = $today->format("Y-m-d H:i:s");

		$GLOBALS['Boot']->loadModule("products", "Price");
		$products_price = PriceController::addPriceOnDemand($user, $data->{'ProductsId'}, $data->{'ProductsSourceId'}, $date_now, $data->{'Price'});
		if ($products_price === false) {
			$result["status"] = false;
			$result["error"] = "data limit exceeded";
		} else {
			$user->save();

			$storage = new StorageModel();
			$storage->setStoragesId($data->{'StoragesId'});
			$storage->setProductsId($data->{'ProductsId'});
			$storage->setProductsSourceId($data->{'ProductsSourceId'});
			$storage->setAmount($data->{'Amount'});
			$storage->setDatetimeInsert($date_now);

			$storage->insert();

			$result["status"] = true;
			$result["new_storage_item"] = $storage->toArray();
			$result["new_storage_item"]["Price"] = $data->{'Price'};

			if (isset($data->{'ShoppingListId'})) {
				$GLOBALS['Boot']->loadModule("shopping", "Index");
				$result["removed_shopping_list_item"] = Shopping\IndexController::removeItem($user, $data->{'ShoppingListId'});
			}
		}
	} else {
		$result["status"] = false;
		$result["error"] = "data limit exceeded";
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function removeAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'storage_item_id'};

	$storage_cond = new Condition("[c1] AND [c2]", array(
                "[c1]" => [
                                [StoragesMembershipModel::class, StoragesMembershipModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $user->getId()]
                        ],
		"[c2]" => [
                                [StorageModel::class, StorageModel::FIELD_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $data]
                        ]
        ));

	$storage_join = new Join(new StorageModel(), "[j1]", array(
		"[j1]" => [
				[StorageModel::class, StorageModel::FIELD_STORAGES_ID],
				Condition::COMPARISON_EQUALS,
				[StoragesMembershipModel::class, StoragesMembershipModel::FIELD_STORAGES_ID]
		]
	));

	$storage_price_join = new Join(new ProductsPriceModel(), "[j2]", array(
		"[j2]" => [
				[StorageModel::class, StorageModel::FIELD_PRODUCTS_ID],
				Condition::COMPARISON_EQUALS,
				[ProductsPriceModel::class, ProductsPriceModel::FIELD_PRODUCTS_ID]
		]
	));

	$storages_membership = new StoragesMembershipModel();
	$storages_membership->find($storage_cond, array($storage_join, $storage_price_join));

	$result = array();
	$result["status"] = true;
	if ($storages_membership->next()) {
		$storage = $storages_membership->joinedModelByClass(StorageModel::class);
		$result["deleted_storage_item"] = array( 'Id' => $storage->getId() );

		$GLOBALS['Boot']->loadModule("users", "Limit");

		$products_price = $storages_membership->joinedModelByClass(ProductsPriceModel::class);
		if ($products_price->getDatetime() == $storage->getDatetimeInsert()) {
			$products_price->delete();
			LimitController::countInOrDecrement($user, LimitController::LIMIT_FIELD_PRODUCTS_PRICE, false);
		}

		$storage->delete();
		LimitController::countInOrDecrement($user, LimitController::LIMIT_FIELD_STORAGE, false);
		$user->save();
	} else {
		$result["status"] = false;
		$result["error"] = "item not found/accessible";
	}

        exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function divideAction() {
	$user = LoginController::requireAuth();

        $data = $GLOBALS['POST']->{'storage_item_id'};
	$target_amount = $GLOBALS['POST']->{'storage_target_amount'};

        $storage_cond = new Condition("[c1] AND [c2]", array(
                "[c1]" => [
                                [StoragesMembershipModel::class, StoragesMembershipModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $user->getId()]
                        ],
                "[c2]" => [
                                [StorageModel::class, StorageModel::FIELD_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $data]
                        ]
        ));

        $storage_join = new Join(new StorageModel(), "[j1]", array(
                "[j1]" => [
                                [StorageModel::class, StorageModel::FIELD_STORAGES_ID],
                                Condition::COMPARISON_EQUALS,
                                [StoragesMembershipModel::class, StoragesMembershipModel::FIELD_STORAGES_ID]
                ]
        ));

        $storages_membership = new StoragesMembershipModel();
        $storages_membership->find($storage_cond, array($storage_join));

        $result = array();
        $result["status"] = true;
        if ($storages_membership->next()) {
                $storage = $storages_membership->joinedModelByClass(StorageModel::class);

		$amount = $storage->getAmount();

		if ($target_amount <= $amount) {
			$diff = $amount - $target_amount;

			$storage_consumption_cond = new Condition("[c1]", array(
				"[c1]" => [
					[StorageConsumptionModel::class, StorageConsumptionModel::FIELD_STORAGE_ID],
					Condition::COMPARISON_EQUALS,
					[Condition::CONDITION_CONST, $storage->getId()]
				]
			));

			$storage_consumption_order = new Order(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_DATETIME, Order::ORDER_DESC);

			$storage_consumption = new StorageConsumptionModel();
			$storage_consumption->find($storage_consumption_cond, null, $storage_consumption_order);

			$consumption_ct = $storage_consumption->count();
			if ($consumption_ct > 0) {
				$amount_per_consumption = $diff / $consumption_ct;

				$storage->setAmount($target_amount);
				$dt_set = false;
				while ($storage_consumption->next()) {
					if (!$dt_set && $target_amount == 0) {
						$storage->setDatetimeEmpty($storage_consumption->getDatetime());
						$dt_set = true;
					}
					$storage_consumption->setAmount(round($storage_consumption->getAmount() + $amount_per_consumption, 4));
					$storage_consumption->save();
				}
				$storage->save();
			}
		}
	} else {
		$result["status"] = false;
                $result["error"] = "item not found/accessible";
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function multiplyAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'storage_item_id'};
	$target_amount = $GLOBALS['POST']->{'storage_target_amount'};

	$storage_cond = new Condition("[c1] AND [c2]", array(
                "[c1]" => [
                                [StoragesMembershipModel::class, StoragesMembershipModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $user->getId()]
                        ],
                "[c2]" => [
                                [StorageModel::class, StorageModel::FIELD_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $data]
                        ]
        ));

        $storage_join = new Join(new StorageModel(), "[j1]", array(
                "[j1]" => [
                                [StorageModel::class, StorageModel::FIELD_STORAGES_ID],
                                Condition::COMPARISON_EQUALS,
                                [StoragesMembershipModel::class, StoragesMembershipModel::FIELD_STORAGES_ID]
                ]
        ));

        $storages_membership = new StoragesMembershipModel();
        $storages_membership->find($storage_cond, array($storage_join));

        $result = array();
        $result["status"] = true;
        if ($storages_membership->next()) {
                $storage = $storages_membership->joinedModelByClass(StorageModel::class);

		$amount = $storage->getAmount();

		$storage_consumption_cond = new Condition("[c1]", array(
                        "[c1]" => [
                                [StorageConsumptionModel::class, StorageConsumptionModel::FIELD_STORAGE_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $storage->getId()]
                        ]
                ));

		$sum_expr = new DBFunctionExpression("[c1]", array(
                	"[c1]" => [StorageConsumptionModel::class, StorageConsumptionModel::FIELD_AMOUNT]
	        ));

	        $fields = new Fields(array());
	        $fields->addFunctionField("SumAmount", DBFunction::FUNCTION_SUM, $sum_expr);

	        $group_by = new GroupBy(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_STORAGE_ID);

                $storage_consumption_order = new Order(StorageConsumptionModel::class, StorageConsumptionModel::FIELD_DATETIME, Order::ORDER_DESC);

                $storage_consumption = new StorageConsumptionModel();
                $storage_consumption->find($storage_consumption_cond, null, $storage_consumption_order, null, $fields, $group_by);

		if ($storage_consumption->next()) {
			$consumption_sum = floatval($storage_consumption->DBFunctionResult("SumAmount"));

			$diff = $target_amount - $amount;

			if ($diff > 0 && $consumption_sum > $diff) {
				$part = 1.0 - $diff/$consumption_sum;

				$storage_consumption_inner = new StorageConsumptionModel();
				$storage_consumption_inner->find($storage_consumption_cond, null, $storage_consumption_order);

				while ($storage_consumption_inner->next()) {
					$storage_consumption_inner->setAmount(round($storage_consumption_inner->getAmount() * $part, 4));
					$storage_consumption_inner->save();
				}
			} else if ($diff < 0) {
				$storage_consumption_inner = new StorageConsumptionModel();
                                $storage_consumption_inner->find($storage_consumption_cond, null, $storage_consumption_order);

				$diff = -1 * $diff;

				$dt_set = false;
				while ($storage_consumption_inner->next()) {
					if (!$dt_set && $target_amount == 0) {
						$storage->setDatetimeEmpty($storage_consumption_inner->getDatetime());
                                                $dt_set = true;
					}

					$s_amount = $storage_consumption_inner->getAmount();

					$part = $s_amount/$consumption_sum;

					$storage_consumption_inner->setAmount($s_amount + $part * $diff);
					$storage_consumption_inner->save();
				}
			}
			$storage->setAmount($target_amount);
                        $storage->save();
		}

	} else {
                $result["status"] = false;
                $result["error"] = "item not found/accessible";
        }

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }
}
