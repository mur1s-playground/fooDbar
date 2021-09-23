<?php

namespace FooDBar\Shopping;

use \FooDBar\Users\LoginController as LoginController;

$GLOBALS['Boot']->loadDBExt("Fields");
$GLOBALS['Boot']->loadDBExt("Join");

use \Frame\Join 	as Join;
use \Frame\Condition 	as Condition;
use \Frame\Order 	as Order;

$GLOBALS['Boot']->loadModel("EbonProductsModel");
$GLOBALS['Boot']->loadModel("EbonProductsParsedModel");
$GLOBALS['Boot']->loadModel("EbonProductsLinkModel");
$GLOBALS['Boot']->loadModel("StorageModel");
$GLOBALS['Boot']->loadModel("ProductsModel");

use \FooDBar\EbonProductsModel		as EbonProductsModel;
use \FooDBar\EbonProductsParsedModel	as EbonProductsParsedModel;
use \FooDBar\EbonProductsLinkModel	as EbonProductsLinkModel;
use \FooDBar\StorageModel		as StorageModel;
use \FooDBar\ProductsModel		as ProductsModel;

use \FooDBar\Users\ProductssourceController 	as ProductssourceController;
use \FooDBar\Products\PriceController 		as PriceController;
use \FooDBar\Users\LimitController              as LimitController;
use \FooDBar\Storage				as Storage;

class EbonController {
    private $DefaultController = false;
    private $DefaultAction = "get";

    public function getAction() {
	$user = LoginController::requireAuth();

	$result = array();
        $result["status"] = true;

	/* USERS SOURCE LOCATIONS */
	$GLOBALS['Boot']->loadModule("users", "Productssource");
	$result['ebon_products_source'] = ProductssourceController::getUsersProductsSources($user);
	/* ---------------------- */

	$el_cond = new Condition("[c1]", array(
		"[c1]" => [
				[EbonProductsParsedModel::class, EbonProductsParsedModel::FIELD_USERS_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $user->getId()]
			]
        ));

	$el_join = new Join(new EbonProductsModel(), "[j1]", array(
		"[j1]" => [
				[EbonProductsParsedModel::class, EbonProductsParsedModel::FIELD_EBON_PRODUCTS_ID],
				Condition::COMPARISON_EQUALS,
				[EbonProductsModel::class, EbonProductsModel::FIELD_ID]
		]
	));

	$el = new EbonProductsParsedModel();
	$el->find($el_cond, array($el_join));

	$result["status"] = true;
	$result["ebon_list_data"] = new \stdClass();
	$result["ebon_products"] = new \stdClass();

	$ebon_products_ids = array();
	while ($el->next()) {
		$result["ebon_list_data"]->{$el->getId()} = $el->toArray();

		$ebon_products_ids[] = $el->getEbonProductsId();

		$ebon_product = $el->joinedModelByClass(EbonProductsModel::class);
		$result["ebon_products"]->{$ebon_product->getId()} = $ebon_product->toArray();
	}

	$epl_cond = new Condition("[c1]", array(
		"[c1]" => [
			[EbonProductsLinkModel::class, EbonProductsLinkModel::FIELD_EBON_PRODUCTS_ID],
			Condition::COMPARISON_IN,
			[Condition::CONDITION_CONST_ARRAY, $ebon_products_ids]
		]
	));

	$epl = new EbonProductsLinkModel();
	$epl->find($epl_cond);
	$result["ebon_products_link"] = new \stdClass();
	while ($epl->next()) {
		if (!isset($result["ebon_products_link"]->{$epl->getEbonProductsId()})) {
			$result["ebon_products_link"]->{$epl->getEbonProductsId()} = array();
		}
		$result["ebon_products_link"]->{$epl->getEbonProductsId()}[] = $epl->getProductsId();
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function tostorageAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'ebon_list_item'};

	$epp_cond = new Condition("[c1] AND [c2]", array(
		"[c1]" => [
                                [EbonProductsParsedModel::class, EbonProductsParsedModel::FIELD_USERS_ID],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, $user->getId()]
                        ],
		"[c2]" => [
				[EbonProductsParsedModel::class, EbonProductsParsedModel::FIELD_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $data->{"Id"}]
		]
	));

	$epp = new EbonProductsParsedModel();
	$epp->find($epp_cond);

	$result["status"] = false;
	if ($epp->next()) {
		$result["status"] = true;

		$p_cond = new Condition("[c1] AND [c2]", array(
			"[c1]" => [
				[ProductsModel::class, ProductsModel::FIELD_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $data->{"ProductsId"}]
			],
			"[c2]" => [
				[ProductsModel::class, ProductsModel::FIELD_USERS_ID],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, $user->getId()]
			]
		));

		$product = new ProductsModel();
		$product->find($p_cond);

		if ($product->next()) {
			$epl_cond = new Condition("[c1] AND [c2]", array(
				"[c1]" => [
					[EbonProductsLinkModel::class, EbonProductsLinkModel::FIELD_EBON_PRODUCTS_ID],
					Condition::COMPARISON_EQUALS,
					[Condition::CONDITION_CONST, $epp->getEbonProductsId()]
				],
				"[c2]" => [
					[EbonProductsLinkModel::class, EbonProductsLinkModel::FIELD_PRODUCTS_ID],
					Condition::COMPARISON_EQUALS,
					[Condition::CONDITION_CONST, $data->{"ProductsId"}]
				]
			));

			$epl = new EbonProductsLinkModel();
			$epl->find($epl_cond);
			if (!$epl->next()) {
				$epl = new EbonProductsLinkModel();
				$epl->setEbonProductsId($epp->getEbonProductsId());
				$epl->setProductsId($data->{"ProductsId"});
				$epl->insert();
			}

			$GLOBALS['Boot']->loadModule("users", "Limit");
		        if (LimitController::countInOrDecrement($user, LimitController::LIMIT_FIELD_STORAGE)) { //FIX storage count for times > 1
				$storages_id = $data->{"StoragesId"};

				$GLOBALS['Boot']->loadModule("storage", "Index");
				Storage\IndexController::requireStorageMembership($user, $storages_id);

				$products_source_id = $epp->getProductsSourceId();
				/*
				$today = date_create();
	                        $date_now = $today->format("Y-m-d H:i:s");
				*/
				/* FIX */
				$date_now = $epp->getDatetime();

		                $GLOBALS['Boot']->loadModule("products", "Price");
		                $products_price = PriceController::addPriceOnDemand($user, $data->{'ProductsId'}, $products_source_id, $date_now, $epp->getPrice());
		                if ($products_price === false) {
                		        $result["status"] = false;
		                        $result["error"] = "data limit exceeded";
                		} else {
		                        $user->save();

					$amount = $epp->getAmount();

					$times = 1;
					if (is_null($amount)) {
						$amount = $product->getAmount();
					} else {
						$amount_type = $epp->getAmountTypeId();
						if ($amount_type == 5) { //Stk
							$times = $amount;
							$amount = $product->getAmount();
						} else if ($amount_type == 4) { //kg to g
							$amount = $amount * 1000;
						}
					}

					for ($t = 0; $t < $times; $t++) {
						$storage = new StorageModel();
		                        	$storage->setStoragesId($storages_id);
		                	        $storage->setProductsId($data->{'ProductsId'});
		        	                $storage->setProductsSourceId($products_source_id);
			                        $storage->setAmount($amount);
        	        		        $storage->setDatetimeInsert($date_now);

			                        $storage->insert();
					}

					$epp->delete();
				}
			}
		}
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function removeAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'ebon_list_item_id'};

	$cond = new Condition("[c1] AND [c2]", array(
		"[c1]" => [
			[EbonProductsParsedModel::class, EbonProductsParsedModel::FIELD_ID],
			Condition::COMPARISON_EQUALS,
			[Condition::CONDITION_CONST, $data]
		],
		"[c2]" => [
			[EbonProductsParsedModel::class, EbonProductsParsedModel::FIELD_USERS_ID],
			Condition::COMPARISON_EQUALS,
			[Condition::CONDITION_CONST, $user->getId()]
		]
	));

	$epp = new EbonProductsParsedModel();
	$epp->find($cond);

	$result = array();
	if ($epp->next()) {
		$result["status"] = true;
		$result["ebon_list_item_id"] = $epp->getId();
		$epp->delete();
	} else {
		$result["status"] = false;
	}

        exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }
}
