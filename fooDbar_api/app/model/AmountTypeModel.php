<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class AmountTypeModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_NAME = 'Name';

	/* int(11) */
	private $Id;

	/* varchar(45) */
	private $Name;


	public function __construct($values = null) {
		parent::__construct('amount_type','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"Name":{"Field":"name","Type":"varchar(45)","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return varchar(45) $this->Name */
	public function getName() {
		return $this->Name;
	}
	/* @param varchar(45) $Name */
	public function setName($Name) {
		$this->Name = $Name;
	}

}