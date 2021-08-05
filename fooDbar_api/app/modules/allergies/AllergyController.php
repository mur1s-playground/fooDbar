<?php

namespace FooDBar;

use \Frame\Condition as Condition;

class AllergyController {
    private $DefaultController = true;
    private $DefaultAction = null;

    public static function getAllergyFields($db_table) {
	$result = array();
	foreach ($db_table->fields() as $field_name_camel => $field) {
		$field_name = $field["Field"];

                $starts_with_a_ = strpos($field_name, "a_");
                if ($starts_with_a_ !== false && $starts_with_a_ === 0) {
			$result[] = $field_name_camel;
		}
	}
	return $result;
    }

    public static function getAllergyValues($db_table) {
	$allergy_fields = AllergyController::getAllergyFields($db_table);

	$result = new \stdClass();
	$result->{'has_unset_allergies'} = false;

	foreach ($allergy_fields as $field_name_camel) {
		$getter = "get" . $field_name_camel;
		$result->{$field_name_camel} = $db_table->$getter();
		if (is_null($result->{$field_name_camel})) {
			$result->{'has_unset_allergies'} = true;
		}
	}
	return $result;
    }

    public static function setAllergyValues($db_table, $values) {
	$allergy_fields = AllergyController::getAllergyFields($db_table);

	foreach ($allergy_fields as $field_name_camel) {
		$setter = "set" . $field_name_camel;
		if (isset($values->{$field_name_camel})) {
			$db_table->$setter($values->{$field_name_camel} === true ? 1 : 0);
		}
	}
	$db_table->save();
    }
}
