<?php

namespace FooDBar\Shopping;

use \FooDBar\Users\LoginController as LoginController;

$GLOBALS['Boot']->loadDBExt("Fields");
$GLOBALS['Boot']->loadDBExt("Join");

use \Frame\Join 	as Join;
use \Frame\Condition 	as Condition;
use \Frame\Order 	as Order;


$GLOBALS['Boot']->loadModel("ShoppingListModel");
$GLOBALS['Boot']->loadModel("ProductsModel");

use \FooDBar\ShoppingListModel		as ShoppingListModel;
use \FooDBar\ProductsModel		as ProductsModel;

use \FooDBar\Users\ProductssourceController 	as ProductssourceController;
use \FooDBar\Products\PriceController 		as PriceController;

class IndexController {
    private $DefaultController = true;
    private $DefaultAction = "get";

    public static function removeItem($user, $id) {
        $sl_cond = new Condition("[c1] AND [c2]", array(
                "[c1]" => [
                                [ShoppingListModel::class, ShoppingListModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $user->getId()]
                        ],
                "[c2]" => [
                                [ShoppingListModel::class, ShoppingListModel::FIELD_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $id]
                ]
        ));

        $sl = new ShoppingListModel();
        $sl->find($sl_cond);
        $result["status"] = false;
        if ($sl->next()) {
                $result["status"] = true;
                $result["deleted_item_id"] = $sl->getId();
                $sl->delete();
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

	$sl_cond = new Condition("[c1]", array(
		"[c1]" => [
				[ShoppingListModel::class, ShoppingListModel::FIELD_USERS_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $user->getId()]
			]
        ));

	$sl_join = new Join(new ProductsModel(), "[j1]", array(
		"[j1]" => [
				[ShoppingListModel::class, ShoppingListModel::FIELD_PRODUCTS_ID],
				Condition::COMPARISON_EQUALS,
				[ProductsModel::class, ProductsModel::FIELD_ID]
		]
	));

	$sl_order = new Order(ShoppingListModel::class, ShoppingListModel::FIELD_ID, Order::ORDER_DESC);

	$sl = new ShoppingListModel();
	$sl->find($sl_cond, array($sl_join), $sl_order);

	$today = date_create();
        $date_now = $today->format("Y-m-d H:i:s");

	$GLOBALS['Boot']->loadModule("products", "Price");

	$result["status"] = true;
	$result["shopping_list"] = new \stdClass();
	while ($sl->next()) {
		$result["shopping_list"]->{$sl->getId()} = $sl->toArray();

		$product = $sl->joinedModelByClass(ProductsModel::class);
		$result["shopping_list"]->{$sl->getId()}["ProductAmount"] = $product->getAmount();

		$prices = array();
		$ps_ids = array();
		foreach ($result['products_source'] as $ps_id => $name_concat) {
			$price = PriceController::getPrice($sl->getProductsId(), $ps_id, $date_now);
			if ($price->next()) {
				$prices[] = $price->getPrice();
				$ps_ids[] = $ps_id;
			}
		}
		$result["shopping_list"]->{$sl->getId()}["Prices"] = implode(';', $prices);
		$result["shopping_list"]->{$sl->getId()}["ProductsSourceIds"] = implode(';', $ps_ids);
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function insertAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'products_demand'};

	$result["status"] = true;
	$result["new_shopping_list_items"] = new \stdClass();
	foreach ($data as $p_id => $product_demand) {
		$sl = new ShoppingListModel();
		$sl->setUsersId($user->getId());
		$sl->setProductsId($p_id);
		$sl->setAmount($product_demand);
		$sl->insert();

		$result["new_shopping_list_items"]->{$sl->getId()} = $sl->toArray();
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function removeAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'shopping_list_item_id'};

	$result = self::removeItem($user, $data);

        exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }
}
