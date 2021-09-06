<?php

namespace FooDBar\Users;

$GLOBALS['Boot']->loadDBExt("Join");
$GLOBALS['Boot']->loadDBExt("Fields");
$GLOBALS['Boot']->loadDBExt("DBFunction");
$GLOBALS['Boot']->loadDBExt("DBFunctionExpression");

use \Frame\Join			as Join;
use \Frame\Condition		as Condition;
use \Frame\Fields		as Fields;
use \Frame\DBFunction           as DBFunction;
use \Frame\DBFunctionExpression as DBFunctionExpression;

$GLOBALS['Boot']->loadModel("ProductsModel");
$GLOBALS['Boot']->loadModel("ProductsPriceModel");
$GLOBALS['Boot']->loadModel("StorageModel");
$GLOBALS['Boot']->loadModel("StorageConsumptionModel");
$GLOBALS['Boot']->loadModel("StoragesMembershipModel");

use \FooDBar\UsersModel			as UsersModel;
use \FooDBar\ProductsModel      	as ProductsModel;
use \FooDBar\ProductsPriceModel 	as ProductsPriceModel;
use \FooDBar\StorageModel		as StorageModel;
use \FooDBar\StorageConsumptionModel	as StorageConsumptionModel;
use \FooDBar\StoragesMembershipModel	as StoragesMembershipModel;

use \FooDBar\Storage			as Storage;

class LimitController {
    private $DefaultController = false;
    private $DefaultAction = null;

    const LIMIT_FIELD_PRODUCTS = "Products";
    const LIMIT_SIZE_PRODUCTS = 396;

    const LIMIT_FIELD_PRODUCTS_PRICE = "ProductsPrice";
    const LIMIT_SIZE_PRODUCTS_PRICE = 32;

    const LIMIT_FIELD_STORAGE = "Storage";
    const LIMIT_SIZE_STORAGE = 48;

    const LIMIT_FIELD_STORAGE_CONSUMPTION = "StorageConsumption";
    const LIMIT_SIZE_STORAGE_CONSUMPTION = 32;

    public static function countInOrDecrement($user, $limit_field, $increment = true, $save = false) {
	$fields = [
			self::LIMIT_FIELD_PRODUCTS,
			self::LIMIT_FIELD_PRODUCTS_PRICE,
			self::LIMIT_FIELD_STORAGE,
			self::LIMIT_FIELD_STORAGE_CONSUMPTION
		];

	$sizes = [
			self::LIMIT_SIZE_PRODUCTS,
			self::LIMIT_SIZE_PRODUCTS_PRICE,
			self::LIMIT_SIZE_STORAGE,
			self::LIMIT_SIZE_STORAGE_CONSUMPTION
		];

	$data_count = 0;
	$limit_field_id = -1;
	for ($f = 0; $f < count($fields); $f++) {
		$getter_c = "get" . $fields[$f] . "Count";
		$value = $user->$getter_c();
		$data_count += $value * $sizes[$f];
		if ($fields[$f] == $limit_field) $limit_field_id = $f;
	}

	$getter_c = "get" . $fields[$limit_field_id] . "Count";
	$value = $user->$getter_c();
	$limit = $user->getDataLimit();
	$setter = "set" . $fields[$limit_field_id] . "Count";
	if ($increment && $data_count + $sizes[$limit_field_id] < $limit) {
		$user->$setter($value + 1);
		if ($save) $user->save();
		return true;
	} else if (!$increment && $value > 0) {
		$user->$setter($value - 1);
		if ($save) $user->save();
		return true;
	}
	return false;
    }

    public static function countAndSetDataCounters($user) {
	/* PRODUCTS */
	$p_cond = new Condition("[c1]", array(
		"[c1]" => [
				[ProductsModel::class, ProductsModel::FIELD_USERS_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $user->getId()]
		]
	));

	$count_expr = new DBFunctionExpression("[e0]", array(
        	"[e0]" => [ProductsModel::class, ProductsModel::FIELD_ID]
        ));

        $fields = new Fields(array());
        $fields->addFunctionField("Count", DBFunction::FUNCTION_COUNT, $count_expr);

	$products = new ProductsModel();
	$products->find($p_cond, null, null, null, $fields);
	if ($products->next()) {
		$user->setProductsCount($products->DBFunctionResult("Count"));
	}

	/* PRODUCTS_PRICE */
	$pp_cond =  new Condition("[c1]", array(
                "[c1]" => [
                                [ProductsPriceModel::class, ProductsPriceModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $user->getId()]
                ]
        ));

	$count_expr = new DBFunctionExpression("[e0]", array(
                "[e0]" => [ProductsPriceModel::class, ProductsPriceModel::FIELD_ID]
        ));

        $fields = new Fields(array());
        $fields->addFunctionField("Count", DBFunction::FUNCTION_COUNT, $count_expr);

	$products_price = new ProductsPriceModel();
	$products_price->find($pp_cond, null, null, null, $fields);
	if ($products_price->next()) {
		$user->setProductsPriceCount($products_price->DBFunctionResult("Count"));
	}

	/* STORAGE */
	$GLOBALS['Boot']->loadModule("storage", "Index");
	$storages = Storage\IndexController::getStorages($user);
        $s_id_array = array();
        foreach ($storages as $id => $storage_obj) {
                $s_id_array[] = $id;
        }

        $storage_cond = new Condition("[c1]", array(
                "[c1]" => [
                                [StorageModel::class, StorageModel::FIELD_STORAGES_ID],
                                Condition::COMPARISON_IN,
                                [Condition::CONDITION_CONST_ARRAY, $s_id_array]
                        ]
        ));

	$count_expr =  new DBFunctionExpression("[e0]", array(
                "[e0]" => [StorageModel::class, StorageModel::FIELD_ID]
        ));

	$fields = new Fields(array());
        $fields->addFunctionField("Count", DBFunction::FUNCTION_COUNT, $count_expr);

        $storage = new StorageModel();
        $storage->find($storage_cond, null, null, null, $fields);
	if ($storage->next()) {
		$user->setStorageCount($storage->DBFunctionResult("Count"));
	}

	/* STORAGE_CONSUMPTION */
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

	$count_expr =  new DBFunctionExpression("[e0]", array(
                "[e0]" => [StorageConsumptionModel::class, StorageConsumptionModel::FIELD_ID]
        ));

        $fields = new Fields(array());
        $fields->addFunctionField("Count", DBFunction::FUNCTION_COUNT, $count_expr);

	$storage_consumption = new StorageConsumptionModel();
	$storage_consumption->find($storages_membership_condition, array($consumption_user_join, $consumption_storage_join, $storages_membership_join), null, null, $fields);
	if ($storage_consumption->next()) {
		$user->setStorageConsumptionCount($storage_consumption->DBFunctionResult("Count"));
	}
	$user->save();
    }
}
