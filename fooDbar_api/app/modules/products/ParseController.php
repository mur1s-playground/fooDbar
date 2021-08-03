<?php

namespace FooDBar;

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "Join.php";
use \Frame\Join as Join;

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "Condition.php";
use \Frame\Condition as Condition;

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "Order.php";
use \Frame\Order as Order;


require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "ProductsModel.php";
require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "AmountTypeModel.php";

class ParseController {
    private $DefaultController = true;
    private $DefaultAction = "parse";

    private function parseUnit($str) {
	$result = array(
		"factor" => 1.0,
		"unit" => "g"
	);
	if (strstr($str, "kg")) {
		$result["factor"] 	= 0.001;
		$result["unit"] 	= "g";
	} else if (strstr($str, "g")) {		/* 100 g = */
		$result["factor"]	= 0.01;
	} else if (strstr($str, "ml")) {	/* 100 ml = */
		$result["factor"]	= 0.01;
		$result["unit"]		= "l";
	} else if (strstr($str, "l")) {
		$result["factor"]	= 1.0;
		$result["unit"]		= "l";
	}
	return $result;
    }

    private function parsePrice($str) {
	$res = str_replace("€", "", $str);
        $res = trim(str_replace(",", ".", $res));
	return floatval($res);
    }

    public function parseAction() {
	$result = array();
	$source_id = "1"; /* ALDI Sued */
	if ($source_id == 1) {
		$base_dir = "../app/crawl_data/aldi/";
		$dirs = scandir($base_dir);
		$valid_dirs = array();
		foreach ($dirs as $dir) {
			if ($dir == "." || $dir == "..") continue;
			$valid_dirs[] = $dir;
		}
		sort($valid_dirs);
		if (count($valid_dirs) > 0) {
			$newest_dir = $valid_dirs[count($valid_dirs) - 1];
			$categories = scandir($base_dir . $newest_dir);
			foreach ($categories as $category) {
				if ($category == "." || $category == "..") continue;
				$pages = scandir($base_dir . $newest_dir . "/" . $category);
				$products = array();
				foreach ($pages as $page) {
					if (!strstr($page, ".html")) continue;
					$page_contents = file_get_contents($base_dir . $newest_dir . "/" . $category . "/" . $page);
//					var_dump($page_contents);
					$page_lines = explode("\n", $page_contents);
					$product_current = null;
					$product_amount = null;
					$product_price_current = null;
					$product_additional_current = null;
					$p = 0;
					for ($l = 0; $l < count($page_lines); $l++) {
						if (strstr($page_lines[$l], "product-title at-all-productName-lbl")) {
							$name_p = (explode(">", $page_lines[$l]))[1];
							$name = (explode("<", $name_p))[0];
							$product_current = $name;
						} else if (strstr($page_lines[$l], "price at-product-price_lbl")) {
							for ($ls = $l; $ls < count($page_lines); $ls++) {
								if (strstr($page_lines[$ls], "€")) {
									$product_price_current = $page_lines[$ls];
									break;
								}
							}
							$product_price_current = $this->parsePrice($product_price_current);
						} else if (strstr($page_lines[$l], "additional-product-info")) {
							for ($ls = $l; $ls < count($page_lines); $ls++) {
								if (strstr($page_lines[$ls], "€")) {
                                                                        $product_additional_current = $page_lines[$ls];
									break;
                                                                }

							}
							$add_splt = explode("=", $product_additional_current);
							$price_splt = explode(")", $add_splt[1])[0];
							$unit = $this->parseUnit($add_splt[0]);
							$unit_type = $unit["unit"];
							$unit_price = $this->parsePrice($price_splt) * $unit["factor"];
							$amount = round($product_price_current / $unit_price, 3);
							$products[$p] = array(
								"name" 		=> $product_current,
								"amount"	=> $amount,
								"price" 	=> $product_price_current,
								"price_default" => $unit_price,
								"price_dafault_unit" => $unit_type
							);

							$product_condition = new Condition("[c1]", array(
								"[c1]" => [
									[ProductsModel::class, ProductsModel::FIELD_NAME],
									Condition::COMPARISON_EQUALS,
									[Condition::CONDITION_CONST, $product_current]
								]
							));

							$amount_type_condition = new Condition("[c1]", array(
								"[c1]" => [
									[AmountTypeModel::class, AmountTypeModel::FIELD_NAME],
									Condition::COMPARISON_EQUALS,
									[Condition::CONDITION_CONST, $unit_type]
								]
							));
							$amount_type = new AmountTypeModel();
							$amount_type->find($amount_type_condition);
							if ($amount_type->next()) {
								$amount_type_id = $amount_type->getId();

                                                	        $product = new ProductsModel();
                                        	                $product->find($product_condition);
                                	                        if ($product->next()) {
                        	                                        $product->setPrice($product_price_current);
                	                                                $product->setAmount($amount);
        	                                                        $product->setAmountTypeId($amount_type_id);

									$now = date_create();
									$product->setLastSeen($now->format('Y-m-d H:i:s'));

									$product->save();
	                                                        } else {
									$product = new ProductsModel();
									$product->setName($product_current);
									$product->setProductsSourceId(1);
									$product->setPrice($product_price_current);
									$product->setAmount($amount);
									$product->setAmountTypeId($amount_type_id);

									$now = date_create();
                                                                        $product->setLastSeen($now->format('Y-m-d H:i:s'));

									$product->insert();
	                                                        }

							} else {
								echo "error amount type not found: " . $unit_type . "<br>";
							}

							$product_current = null;
							$product_amount = null;
							$product_price_current = null;
							$product_additional_current = null;
							$p++;
						}
					}
				}
			}
		}
	}

//	exit(json_encode($result, JSON_PRETTY_PRINT));
    }
}
