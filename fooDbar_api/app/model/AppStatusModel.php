<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class AppStatusModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_USERS_ID = 'UsersId';
	const FIELD_FIELD = 'Field';
	const FIELD_VALUE = 'Value';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $UsersId;

	/* varchar(255) */
	private $Field;

	/* blob */
	private $Value;


	public function __construct($values = null) {
		parent::__construct('app_status','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"UsersId":{"Field":"users_id","Type":"int(11)","Null":"YES","Key":"","Default":"0","Extra":""},"Field":{"Field":"field","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"Value":{"Field":"value","Type":"blob","Null":"YES","Key":"","Default":null,"Extra":""}}', $values);
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
	/* @return varchar(255) $this->Field */
	public function getField() {
		return $this->Field;
	}
	/* @param varchar(255) $Field */
	public function setField($Field) {
		$this->Field = $Field;
	}
	/* @return blob $this->Value */
	public function getValue() {
		return $this->Value;
	}
	/* @param blob $Value */
	public function setValue($Value) {
		$this->Value = $Value;
	}

}