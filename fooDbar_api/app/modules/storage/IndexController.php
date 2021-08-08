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

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "ProductsSourceModel.php";
require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "ProductsPriceModel.php";

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
        exit(json_encode($result, JSON_PRETTY_PRINT));
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

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function insertAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'storage_item'};

        self::requireStorageMembership($user, $data->{'StoragesId'});

	$today = date_create();
        $date_now = $today->format("Y-m-d H:i:s");

	$GLOBALS['Boot']->loadModule("products", "Price");
	$products_price = PriceController::addPriceOnDemand($data->{'ProductsId'}, $data->{'ProductsSourceId'},$date_now, $data->{'Price'});

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

	exit(json_encode($result, JSON_PRETTY_PRINT));
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

	$storages_membership = new StoragesMembershipModel();
	$storages_membership->find($storage_cond, array($storage_join));

	$result = array();
	$result["status"] = true;
	if ($storages_membership->next()) {
		$storage = $storages_membership->joinedModelByClass(StorageModel::class);
		$result["deleted_storage_item"] = array( 'Id' => $storage->getId() );
		$storage->delete();
	} else {
		$result["status"] = false;
		$result["error"] = "item not found/accessible";
	}

        exit(json_encode($result, JSON_PRETTY_PRINT));
    }
}
