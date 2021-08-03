<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class AgeGroupModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_AGE_FROM = 'AgeFrom';
	const FIELD_AGE_TO = 'AgeTo';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $AgeFrom;

	/* int(11) */
	private $AgeTo;


	public function __construct($values = null) {
		parent::__construct('age_group','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"AgeFrom":{"Field":"age_from","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"AgeTo":{"Field":"age_to","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return int(11) $this->AgeFrom */
	public function getAgeFrom() {
		return $this->AgeFrom;
	}
	/* @param int(11) $AgeFrom */
	public function setAgeFrom($AgeFrom) {
		$this->AgeFrom = $AgeFrom;
	}
	/* @return int(11) $this->AgeTo */
	public function getAgeTo() {
		return $this->AgeTo;
	}
	/* @param int(11) $AgeTo */
	public function setAgeTo($AgeTo) {
		$this->AgeTo = $AgeTo;
	}

}