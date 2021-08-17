<?php

namespace FooDBar\Shopping;

use \FooDBar\Users\LoginController as LoginController;

$GLOBALS['Boot']->loadDBExt("Fields");
$GLOBALS['Boot']->loadDBExt("Join");
$GLOBALS['Boot']->loadDBExt("Limit");

use \Frame\Join 	as Join;
use \Frame\Condition 	as Condition;
use \Frame\Order 	as Order;
use \Frame\Limit	as Limit;


$GLOBALS['Boot']->loadModel("ShoppingListModel");
$GLOBALS['Boot']->loadModel("ProductsModel");
$GLOBALS['Boot']->loadModel("AmountTypeModel");

use \FooDBar\ShoppingListModel		as ShoppingListModel;
use \FooDBar\ProductsModel		as ProductsModel;
use \FooDBar\AmountTypeModel		as AmountTypeModel;

use \FooDBar\Users\ProductssourceController 	as ProductssourceController;
use \FooDBar\Products\PriceController 		as PriceController;

class OrderController {
    private $DefaultController = true;
    private $DefaultAction = "all";

    public function allAction() {
	$user = LoginController::requireAuth();

	$result = array();
        $result["status"] = true;

	/* USERS SOURCE LOCATIONS */
	$GLOBALS['Boot']->loadModule("users", "Productssource");
	$result['products_source'] = ProductssourceController::getUsersProductsSources($user);
	/* ---------------------- */

	$sl_cond = new Condition("[c1] AND [c2]", array(
		"[c1]" => [
				[ShoppingListModel::class, ShoppingListModel::FIELD_USERS_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $user->getId()]
			],
		"[c2]" => [
				[ShoppingListModel::class, ShoppingListModel::FIELD_ORDER_ID],
				Condition::COMPARISON_IS,
				[Condition::CONDITION_RESERVED, Condition::RESERVED_NULL]
		]
        ));

	$sl_join = new Join(new ProductsModel(), "[j1]", array(
		"[j1]" => [
				[ShoppingListModel::class, ShoppingListModel::FIELD_PRODUCTS_ID],
				Condition::COMPARISON_EQUALS,
				[ProductsModel::class, ProductsModel::FIELD_ID]
		]
	));

	$sl_join_amount = new Join(new AmountTypeModel(), "[j2]", array(
		"[j2]" => [
				[ProductsModel::class, ProductsModel::FIELD_AMOUNT_TYPE_ID],
				Condition::COMPARISON_EQUALS,
				[AmountTypeModel::class, AmountTypeModel::FIELD_ID]
		]
	));

	$sl_order = new Order(ShoppingListModel::class, ShoppingListModel::FIELD_ID, Order::ORDER_DESC);

	$sl = new ShoppingListModel();
	$sl->find($sl_cond, array($sl_join, $sl_join_amount), $sl_order);

	$today = date_create();
        $date_now = $today->format("Y-m-d H:i:s");

	$GLOBALS['Boot']->loadModule("products", "Price");

	$ordered_sl_ids = array();

	$message = "";

	$min_price = 0;
	$max_price = 0;

	$min_eff_price = 0;
	$max_eff_price = 0;

	while ($sl->next()) {
		$product = $sl->joinedModelByClass(ProductsModel::class);
		$amount_type = $sl->joinedModelByClass(AmountTypeModel::class);

		$product_count = ceil($sl->getAmount() / $product->getAmount());

		$message .= $product_count . " x " . $product->getAmount() . $amount_type->getName() . " (" . round($sl->getAmount(), 2) . $amount_type->getName() . ") " . $product->getName() . "\r\n";

		$min_p = null;
		$max_p = null;

		foreach ($result['products_source'] as $ps_id => $name_concat) {
			$price = PriceController::getPrice($sl->getProductsId(), $ps_id, $date_now);
			if ($price->next()) {
				$message .= $price->getPrice() . " EUR - " . $name_concat["Name"] . "\r\n";

				if ($min_p == null || $min_p > $price->getPrice()) {
					$min_p = $price->getPrice();
				}
				if ($max_p == null || $max_p < $price->getPrice()) {
					$max_p = $price->getPrice();
				}
			}
		}

		$min_price += $product_count * $min_p;
		$max_price += $product_count * $max_p;

		$min_eff_price += ($sl->getAmount() / $product->getAmount()) * $min_p;
		$max_eff_price += ($sl->getAmount() / $product->getAmount()) * $max_p;

		$message .= "\r\n\r\n";
		$ordered_sl_ids[] = $sl->getId();
	}

	$message .= "Total: {$min_price} - {$max_price} EUR (eff: " . round($min_eff_price, 2) . " - " . round($max_eff_price, 2) . " EUR)\r\n";

	$result["status"] = false;
	if (count($ordered_sl_ids) > 0) {
		if (!mail($user->getEmail(), "Shopping List", $message, "From: mur1s.playground@root.de")) {
			$result["error"] = "email not sent";
		} else {

			$sl_max_id_cond = new Condition("[c1] AND [c2]", array(
		                "[c1]" => [
                		                [ShoppingListModel::class, ShoppingListModel::FIELD_USERS_ID],
        		                        Condition::COMPARISON_EQUALS,
		                                [Condition::CONDITION_CONST, $user->getId()]
	                	        ],
        		        "[c2]" => [
	        	                        [ShoppingListModel::class, ShoppingListModel::FIELD_ORDER_ID],
                		                Condition::COMPARISON_IS_NOT,
        	                	        [Condition::CONDITION_RESERVED, Condition::RESERVED_NULL]
		                ]
			));
			$order  = new Order(ShoppingListModel::class, ShoppingListModel::FIELD_ORDER_ID, Order::ORDER_DESC);
			$limit = new Limit(1);

			$sl = new ShoppingListModel();
			$sl->find($sl_max_id_cond, null, $order, $limit);
			$max_id = 0;
			if ($sl->next()) {
				$max_id = $sl->getOrderId();
			}

			$sl_cond = new Condition("[c1]", array(
	                        "[c1]" => [
        	                                [ShoppingListModel::class, ShoppingListModel::FIELD_ID],
                	                        Condition::COMPARISON_IN,
                        	                [Condition::CONDITION_CONST_ARRAY, $ordered_sl_ids]
	                                ]
        	        ));

			$sl = new ShoppingListModel();
			$sl->find($sl_cond);
			while ($sl->next()) {
				$sl->setOrderId($max_id + 1);
				$sl->save();
			}

			$result["status"] = true;
			$result["order_shopping_list_ids"] = $ordered_sl_ids;
			$result["OrderId"] = $max_id + 1;
		}
	} else {
		$result["status"] = true;
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }
}
