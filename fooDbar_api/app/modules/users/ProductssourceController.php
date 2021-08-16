<?php

namespace FooDBar\Users;

$GLOBALS['Boot']->loadDBExt("Fields");
$GLOBALS['Boot']->loadDBExt("Join");
$GLOBALS['Boot']->loadDBExt("DBFunction");
$GLOBALS['Boot']->loadDBExt("DBFunctionExpression");

use \Frame\Fields as Fields;
use \Frame\Join as Join;
use \Frame\Condition as Condition;
use \Frame\Order as Order;
use \Frame\DBFunction as DBFunction;
use \Frame\DBFunctionExpression as DBFunctionExpression;


$GLOBALS['Boot']->loadModel("ProductsSourceModel");
$GLOBALS['Boot']->loadModel("UsersProductsSourcesModel");

use \FooDBar\ProductsSourceModel as ProductsSourceModel;
use \FooDBar\UsersProductsSourcesModel as UsersProductsSourcesModel;

class ProductssourceController {
    private $DefaultController = true;
    private $DefaultAction = "get";

    public static function getUsersProductsSources($user) {
	$users_ps_cond = new Condition("[c1]", array(
                "[c1]" => [
                                [UsersProductsSourcesModel::class, UsersProductsSourcesModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $user->getId()]
                        ]
        ));

	$users_ps_products_source_join = new Join(new ProductsSourceModel(), "[j1]", array(
                "[j1]" => [
                                [UsersProductsSourcesModel::class, UsersProductsSourcesModel::FIELD_PRODUCTS_SOURCE_ID],
                                Condition::COMPARISON_EQUALS,
                                [ProductsSourceModel::class, ProductsSourceModel::FIELD_ID]
                        ]
        ));

        $products_source_concat_expr = array(
                new DBFunctionExpression("[e1]", array("[e1]" => [ProductsSourceModel::class, ProductsSourceModel::FIELD_NAME])),
                new DBFunctionExpression("[e2]", array("[e2]" => [Condition::CONDITION_CONST, ", "])),
                new DBFunctionExpression("[e3]", array("[e3]" => [ProductsSourceModel::class, ProductsSourceModel::FIELD_ADDRESS])),
                new DBFunctionExpression("[e4]", array("[e4]" => [Condition::CONDITION_CONST, ", "])),
                new DBFunctionExpression("[e5]", array("[e5]" => [ProductsSourceModel::class, ProductsSourceModel::FIELD_ZIPCODE])),
                new DBFunctionExpression("[e6]", array("[e6]" => [Condition::CONDITION_CONST, " "])),
                new DBFunctionExpression("[e7]", array("[e7]" => [ProductsSourceModel::class, ProductsSourceModel::FIELD_CITY]))
        );

        $fields = new Fields(array());
        $fields->addField(ProductsSourceModel::class, ProductsSourceModel::FIELD_ID);
        $fields->addFunctionField("ProductsSourceConcat", DBFunction::FUNCTION_CONCAT, $products_source_concat_expr);

        $users_products_sources = new UsersProductsSourcesModel();
        $users_products_sources->find($users_ps_cond, array($users_ps_products_source_join), null, null, $fields);

        $result = new \stdClass();
        while ($users_products_sources->next()) {
                $products_source = $users_products_sources->joinedModelByClass(ProductsSourceModel::class);
                $result->{$products_source->getId()} = array(
                                                                                        "Id" => $products_source->getId(),
                                                                                        "Name" => $users_products_sources->DBFunctionResult("ProductsSourceConcat")
                                                                                );
        }
	return $result;
    }

    public static function insertUsersProductsSource($user, $products_source_id) {
	$users_products_sources = new UsersProductsSourcesModel();
        $users_products_sources->setUsersId($user->getId());
        $users_products_sources->setProductsSourceId($products_source_id);
        $users_products_sources->insert();

	return $users_products_sources;
    }

    public function getAction() {
	$user = LoginController::requireAuth();

	$result = array();
        $result["status"] = true;
        $result["products_source"] = self::getUsersProductsSources($user);

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function insertAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'new_users_products_source_item'};

	$products_sources = self::getUsersProductsSources($user);
	if (!isset($products_sources->{$data->{UsersProductsSourcesModel::FIELD_PRODUCTS_SOURCE_ID}})) {
		$users_products_source = self::insertUsersProductsSource($user, $data->{UsersProductsSourcesModel::FIELD_PRODUCTS_SOURCE_ID});

		$result["status"] = true;
		$result["new_users_products_source_item"] = $users_products_source->toArray();
	} else {
		$result["status"] = false;
		$result["error"] = "Products source already in list";
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function removeAction() {
	$user = LoginController::requireAuth();

	$result = array();
	$result["status"] = false;

        exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }
}
