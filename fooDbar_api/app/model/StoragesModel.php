<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class StoragesModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_DESC = 'Desc';

	/* int(11) */
	private $Id;

	/* varchar(255) */
	private $Desc;


	public function __construct($values = null) {
		parent::__construct('storages','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"Desc":{"Field":"desc","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return varchar(255) $this->Desc */
	public function getDesc() {
		return $this->Desc;
	}
	/* @param varchar(255) $Desc */
	public function setDesc($Desc) {
		$this->Desc = $Desc;
	}

}