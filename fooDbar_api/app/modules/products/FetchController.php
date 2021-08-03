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

class FetchController {
    private $DefaultController = true;
    private $DefaultAction = "fetch";

    public function fetchAction() {
	$result = array();
	$source_id = "1"; /* ALDI Sued */
	if ($source_id == 1) {
		$base_dir = "../app/crawl_data/aldi/";
		$dirs = scandir($base_dir);

		$categories = array(
			'brot-aufstrich-und-cerealien',
			'getraenke',
			'kuehlung-und-tiefkuehlkost',
			'nahrungsmittel'
		);

		$today = date_create();
		$date_now = $today->format("Y-m-d");

		mkdir($base_dir . $date_now);
		foreach ($categories as $category) {
			$target_dir = $base_dir . $date_now . "/" . $category;
			mkdir($target_dir);
			$counter = 0;
			exec("wget -O {$target_dir}/{$counter}.html https://www.aldi-sued.de/de/produkte/produktsortiment/{$category}.onlyProduct.html?pageNumber={$counter}");
			$contents = file_get_contents($target_dir . "/" . $counter . ".html");
			$lines = explode("\n", $contents);
			for ($l = 0; $l < count($lines); $l++) {
				$needle = "data-pageNumber=\"";
				$pos = strpos($lines[$l], $needle);
				if ($pos > 0) {
					$data_pN = substr($lines[$l], $pos + strlen($needle));
					$data_pN = intval(explode("\"", $data_pN)[0]);
					for ($counter = 1; $counter < $data_pN; $counter++) {
						exec("wget -O {$target_dir}/{$counter}.html https://www.aldi-sued.de/de/produkte/produktsortiment/{$category}.onlyProduct.html?pageNumber={$counter}");
					}
				}
			}
		}
	}

//	exit(json_encode($result, JSON_PRETTY_PRINT));
    }
}
