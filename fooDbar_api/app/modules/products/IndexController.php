<?php

namespace FooDBar\Products;

use \FooDBar\Users\LoginController as LoginController;

$GLOBALS['Boot']->loadDBExt("Join");

use \Frame\Join 	as Join;
use \Frame\Condition 	as Condition;
use \Frame\Order 	as Order;


$GLOBALS['Boot']->loadModel("ProductsModel");
$GLOBALS['Boot']->loadModel("AmountTypeModel");

use \FooDBar\ProductsModel 	as ProductsModel;
use \FooDBar\AmountTypeModel 	as AmountTypeModel;

use \FooDBar\Users\LimitController as LimitController;

class IndexController {
    private $DefaultController = true;
    private $DefaultAction = "get";

    public function getProductsByIdArray($ids) {
	$cond = new Condition("[c1]", array(
		"[c1]" => [
				[ProductsModel::class, ProductsModel::FIELD_ID],
				Condition::COMPARISON_IN,
				[Condition::CONDITION_CONST_ARRAY, $ids]
		]
	));

	$products = new ProductsModel();
	$products->find($cond);

	$result = new \stdClass();
	while ($products->next()) {
		$result->{$products->getId()} = $products->toArray();
	}

	return $result;
    }

    public function getAction() {
	$user = LoginController::requireAuth();

	$result = array();
        $result["status"] = true;

	$cond = new Condition("[c1]", array(
		"[c1]" => [
			[ProductsModel::class, ProductsModel::FIELD_USERS_ID],
			Condition::COMPARISON_EQUALS,
			[Condition::CONDITION_CONST, $user->getId()]
		]
	));

        $products = new ProductsModel();
        $products->find($cond);

	$result["products"] = new \stdClass();
	while ($products->next()) {
		$result["products"]->{$products->getId()} = $products->toArray();
	}

	$amount_type = new AmountTypeModel();
	$amount_type->find();

	$result["amount_type"] = new \stdClass();
	while ($amount_type->next()) {
		$result["amount_type"]->{$amount_type->getId()} = $amount_type->toArray();
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function insertAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'product'};

	$GLOBALS['Boot']->loadModule("users", "Limit");
	$result = array();
	if (LimitController::countInOrDecrement($user, LimitController::LIMIT_FIELD_PRODUCTS)) {
		$product = new ProductsModel();

 		foreach ($product->fields() as $field_name_camel => $field) {
			$setter = "set" . $field_name_camel;

			if (!isset($data->{$field_name_camel})) continue;

			$value = $data->{$field_name_camel};
			if ($value === true) {
				$value = 1;
			} else if ($value === false) {
				$value = 0;
			}

			if (strlen($value) > 0) {
				$product->$setter($value);
			}
		}

		$today = date_create();
	        $date_now = $today->format("Y-m-d H:i:s");
		$product->setLastSeen($date_now);

		$product->insert();
		$user->save();

		$result["status"] = true;
		$result["new_product"] = $product->toArray();
	} else {
		$result["status"] = false;
		$result["error"] = "data limit exceeded";
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function removeAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'product_id'};

	$product_cond = new Condition("[c1] AND [c2]", array(
		"[c1]" => [
				[ProductsModel::class, ProductsModel::FIELD_USERS_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $user->getId()]
			],
		"[c2]" => [
				[ProductsModel::class, ProductsModel::FIELD_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $data]
			]
        ));

	$product = new ProductsModel();
	$product->find($product_cond);

	$result = array();
	$result["status"] = false;
	if ($product->next()) {
		$result["deleted_product"] = $product->toArray();
		$result["status"] = true;
		$product->delete();
		$GLOBALS['Boot']->loadModule("users", "Limit");
		LimitController::countInOrDecrement($user, LimitController::LIMIT_FIELD_PRODUCTS, false, true);
	}

        exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }
}
