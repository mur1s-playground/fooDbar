<?php

namespace FooDBar\App;

use \Frame\Condition as Condition;

$GLOBALS['Boot']->loadModel("AppStatusModel");

use \FooDBar\AppStatusModel as AppStatusModel;

class StatusController {
    private $DefaultController = true;
    private $DefaultAction = null;

    private static function getFieldsCondition($names, $user_id) {
	$cond_str = "(";
        $cond_arr = array();
        for ($c = 0; $c < count($names); $c++) {
                if ($c > 0) {
                        $cond_str .= " OR ";
                }
                $cond_str .= "[c{$c}]";
                $cond_arr["[c{$c}]"] = array(
                        [AppStatusModel::class, AppStatusModel::FIELD_FIELD],
                        Condition::COMPARISON_LIKE,
                        [Condition::CONDITION_CONST, $names[$c]]
                );
        }
	$cond_str .= ") AND [cu]";
	$cond_arr["[cu]"] = array(
		[AppStatusModel::class, AppStatusModel::FIELD_USERS_ID],
                Condition::COMPARISON_EQUALS,
                [Condition::CONDITION_CONST, $user_id]
	);
        return new Condition($cond_str, $cond_arr);
    }

    public static function getFields($names, $user_id = 0) {
	if (!is_array($names)) {
		$names = array($names);
	}

	$cond = self::getFieldsCondition($names, $user_id);

	$app_status = new AppStatusModel();
	$app_status->find($cond);

	$result["status"] = true;
	$result["app_status"] = new \stdClass();
	while ($app_status->next()) {
		$value = $app_status->getValue();
		if (is_null($value)) {
			$result["app_status"]->{$app_status->getField()} = null;
		} else {
			$result["app_status"]->{$app_status->getField()} = json_decode($value);
		}
	}
	return $result;
    }

    public static function setFields($arr, $user_id = 0) {
	$names = array_keys($arr);
	$cond = self::getFieldsCondition($names, $user_id);

	$app_status = new AppStatusModel();
        $app_status->find($cond);

	while ($app_status->next()) {
		$field = $app_status->getField();
		if (isset($arr[$field])) {
			if (is_null($arr[$field])) {
				$app_status->setValue(null);
			} else {
				$app_status->setValue(json_encode($arr[$field], JSON_INVALID_UTF8_SUBSTITUTE));
			}
			$app_status->save();
			unset($arr[$field]);
		}
	}

	foreach ($arr as $field => $value) {
		$app_status = new AppStatusModel();
		$app_status->setField($field);
		if (!is_null($value)) {
			$app_status->setValue(json_encode($value, JSON_INVALID_UTF8_SUBSTITUTE));
		}
		$app_status->insert();
	}

	$result["status"] = true;
	return $result;
    }

}
