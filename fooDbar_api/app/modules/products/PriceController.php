<?php

namespace FooDBar;

use \Frame\Condition as Condition;
use \Frame\Order as Order;

require_once $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "ProductsPriceModel.php";

class PriceController {
    private $DefaultController = false;
    private $DefaultAction = "get";

    public static function getPrice($products_id, $products_source_id, $datetime) {
	$products_price_cond = new Condition("[c1] AND [c2] AND [c3]", array(
                "[c1]" => [
                                [ProductsPriceModel::class, ProductsPriceModel::FIELD_PRODUCTS_ID],
                                Condition::COMPARISON_LESS_EQUALS,
                                [Condition::CONDITION_CONST, $products_id]
                        ],
                "[c2]" => [
                                [ProductsPriceModel::class, ProductsPriceModel::FIELD_PRODUCTS_SOURCE_ID],
                                Condition::COMPARISON_LESS_EQUALS,
                                [Condition::CONDITION_CONST, $products_source_id]
                        ],
                "[c3]" => [
                                [ProductsPriceModel::class, ProductsPriceModel::FIELD_DATETIME],
                                Condition::COMPARISON_LESS_EQUALS,
                                [Condition::CONDITION_CONST, $datetime]
                        ]
        ));

        $products_price_order = new Order(ProductsPriceModel::class, ProductsPriceModel::FIELD_DATETIME, Order::ORDER_DESC);

	$products_price = new ProductsPriceModel();
        $products_price->find($products_price_cond, null, $products_price_order);

	return $products_price;
    }

    public static function addPrice($products_id, $products_source_id, $datetime, $price) {
	$product_price = new ProductsPriceModel();
	$product_price->setProductsId($products_id);
	$product_price->setProductsSourceId($products_source_id);
	$product_price->setDatetime($datetime);
	$product_price->setPrice($price);
	return $product_price->insert();
    }

    public static function addPriceOnDemand($products_id, $products_source_id, $datetime, $price) {
        $products_price = self::getPrice($products_id, $products_source_id, $datetime);

        $new_price = false;
        if ($products_price->next()) {
                if ($products_price->getPrice() != $price) {
                        $new_price = true;
                }
        } else {
		$new_price = true;
	}
        if ($new_price) {
		return self::addPrice($products_id, $products_source_id, $datetime, $price);
        }
	return $products_price;
    }

    public function getAction() {
	$user = LoginController::requireAuth();

	$result = array();
        $result["status"] = false;

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function insertAction() {
	$user = LoginController::requireAuth();

        $result = array();
        $result["status"] = false;

        exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function removeAction() {
	$user = LoginController::requireAuth();

	$result = array();
        $result["status"] = false;

        exit(json_encode($result, JSON_PRETTY_PRINT));
    }
}
