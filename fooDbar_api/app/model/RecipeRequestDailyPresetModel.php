<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class RecipeRequestDailyPresetModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_USERS_ID = 'UsersId';
	const FIELD_PRESET_NAME = 'PresetName';
	const FIELD_PRESET = 'Preset';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $UsersId;

	/* varchar(45) */
	private $PresetName;

	/* varchar(255) */
	private $Preset;


	public function __construct($values = null) {
		parent::__construct('recipe_request_daily_preset','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"UsersId":{"Field":"users_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"PresetName":{"Field":"preset_name","Type":"varchar(45)","Null":"NO","Key":"","Default":null,"Extra":""},"Preset":{"Field":"preset","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return int(11) $this->UsersId */
	public function getUsersId() {
		return $this->UsersId;
	}
	/* @param int(11) $UsersId */
	public function setUsersId($UsersId) {
		$this->UsersId = $UsersId;
	}
	/* @return varchar(45) $this->PresetName */
	public function getPresetName() {
		return $this->PresetName;
	}
	/* @param varchar(45) $PresetName */
	public function setPresetName($PresetName) {
		$this->PresetName = $PresetName;
	}
	/* @return varchar(255) $this->Preset */
	public function getPreset() {
		return $this->Preset;
	}
	/* @param varchar(255) $Preset */
	public function setPreset($Preset) {
		$this->Preset = $Preset;
	}

}