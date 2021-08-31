<?php

namespace FooDBar\App;

use \FooDBar\Users\LoginController as LoginController;

$GLOBALS['Boot']->loadDBExt("Limit");

use \Frame\Condition as Condition;
use \Frame\Order as Order;
use \Frame\Limit as Limit;

class BackupController {
    private $DefaultController = false;
    private $DefaultAction = "get";

    public function getAction() {
	$user = LoginController::requireAuth();

	$result["status"] = false;
	if ($user->getIsAdmin() == 1) {
		$data = $GLOBALS['POST']->{'table'};

		$model_name = $data->{'name'};
		$offset = intval($data->{'offset'});
		$limit = intval($data->{'limit'});

		if (preg_match("/^[a-zA-Z0-9]+$/", $model_name) != 0 && preg_match("/^[a-zA-Z0-9]+$/", $model_name) !== false) {
			$GLOBALS['Boot']->loadModel($model_name . "Model");

			$model_full_name = "\\FooDBar\\" . $model_name . "Model";
			$m = new $model_full_name();

			$limit_o = new Limit($limit, $offset);

			$m->find(null, null, null, $limit_o);

			$result["status"] = true;
			$result["{$model_full_name}"] = new \stdClass();
			while ($m->next()) {
				$result["{$model_full_name}"]->{$m->getId()} = $m->toArray();
			}
		}
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function insertAction() {
	$user = LoginController::requireAuth();

	$result["status"] = false;
	if ($user->getIsAdmin() == 1) {
		$data = $GLOBALS['POST']->{'table'};

                $model_name = $data->{'name'};
		$rows = $data->{'rows'};

		if (preg_match("/^[a-zA-Z0-9]+$/", $model_name) != 0 && preg_match("/^[a-zA-Z0-9]+$/", $model_name) !== false) {
                        $GLOBALS['Boot']->loadModel($model_name . "Model");

                        $model_full_name = "\\FooDBar\\" . $model_name . "Model";
			$result["status"] = true;
			foreach ($rows as $r => $row) {
				$m = new $model_full_name();
				$fields = array_keys($m->fields());
				foreach ($row as $field_name_camel => $value) {
					if (in_array($field_name_camel, $fields)) {
						$setter = "set" . $field_name_camel;
						$m->$setter($value);
					}
				}
				$m->insert(false, true);
			}
		}
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function truncateAction() {
	$user = LoginController::requireAuth();

        $result["status"] = false;
        if ($user->getIsAdmin() == 1) {
                $data = $GLOBALS['POST']->{'table'};

                $model_name = $data->{'name'};

                if (preg_match("/^[a-zA-Z0-9]+$/", $model_name) != 0 && preg_match("/^[a-zA-Z0-9]+$/", $model_name) !== false) {
                        $GLOBALS['Boot']->loadModel($model_name . "Model");

                        $model_full_name = "\\FooDBar\\" . $model_name . "Model";
			$result["status"] = true;
			$m = new $model_full_name();
			$m->truncate();
		}
	}

	exit(json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE));
    }
}
