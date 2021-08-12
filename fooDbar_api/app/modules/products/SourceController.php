<?php

namespace FooDBar\Products;

use \FooDBar\Users\LoginController as LoginController;

use \Frame\Condition as Condition;
use \Frame\Order as Order;


$GLOBALS['Boot']->loadModel("ProductsSourceModel");

use \FooDBar\ProductsSourceModel as ProductsSourceModel;

use \FooDBar\Users\ProductssourceController as ProductssourceController;

class SourceController {
    private $DefaultController = false;
    private $DefaultAction = "find";

    public function findAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'products_source_search'};

	$conds_ct = 0;
	$conds_str = "";
	$conds = array();

	$fields = [ ProductsSourceModel::FIELD_NAME, ProductsSourceModel::FIELD_ADDRESS, ProductsSourceModel::FIELD_ZIPCODE, ProductsSourceModel::FIELD_CITY];
	for ($f = 0; $f < count($fields); $f++) {
		if (isset($data->{$fields[$f]}) && strlen($data->{$fields[$f]}) > 0) {
			if ($conds_ct > 0) $conds_str .= " AND ";
			$cond_spec = "[c" . $conds_ct . "]";

			$conds_str .= $cond_spec;
			$conds[$cond_spec] = [
				[ProductsSourceModel::class, $fields[$f]],
				Condition::COMPARISON_LIKE,
				[Condition::CONDITION_CONST, "%" . $data->{$fields[$f]} . "%"]
			];
			$conds_ct++;
		}
	}

	$find_cond = new Condition($conds_str, $conds);

        $products_source = new ProductsSourceModel();
	$products_source->find($find_cond);

	$result = array();
        $result["status"] = true;
	$result["products_source"] = new \stdClass();
	while ($products_source->next()) {
		$result["products_source"]->{$products_source->getId()} = $products_source->toArray();
	}

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function insertAction() {
	$user = LoginController::requireAuth();

	$data = $GLOBALS['POST']->{'products_source_search'};

	$products_source = new ProductsSourceModel();
	$products_source->setName($data->{ProductsSourceModel::FIELD_NAME});
	$products_source->setAddress($data->{ProductsSourceModel::FIELD_ADDRESS});
	$products_source->setZipcode($data->{ProductsSourceModel::FIELD_ZIPCODE});
	$products_source->setCity($data->{ProductsSourceModel::FIELD_CITY});
	$products_source->setProductsSourceTypeId($data->{ProductsSourceModel::FIELD_PRODUCTS_SOURCE_TYPE_ID});
	$products_source->insert();

	$GLOBALS['Boot']->loadModule("users", "Productssource");
        ProductssourceController::insertUsersProductsSource($user, $products_source->getId());

        $result = array();
        $result["status"] = true;
	$result["new_products_source"] = $products_source->toArray();

        exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function removeAction() {
	$user = LoginController::requireAuth();

	$result = array();
        $result["status"] = false;

        exit(json_encode($result, JSON_PRETTY_PRINT));
    }
}
