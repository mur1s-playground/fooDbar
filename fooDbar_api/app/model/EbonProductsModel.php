<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class EbonProductsModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_PRODUCTS_SOURCE_ID = 'ProductsSourceId';
	const FIELD_NAME = 'Name';
	const FIELD_IGNORE = 'Ignore';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $ProductsSourceId;

	/* varchar(255) */
	private $Name;

	/* int(11) */
	private $Ignore;


	public function __construct($values = null) {
		parent::__construct('ebon_products','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"ProductsSourceId":{"Field":"products_source_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"Name":{"Field":"name","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"Ignore":{"Field":"ignore","Type":"int(11)","Null":"YES","Key":"","Default":"0","Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return int(11) $this->ProductsSourceId */
	public function getProductsSourceId() {
		return $this->ProductsSourceId;
	}
	/* @param int(11) $ProductsSourceId */
	public function setProductsSourceId($ProductsSourceId) {
		$this->ProductsSourceId = $ProductsSourceId;
	}
	/* @return varchar(255) $this->Name */
	public function getName() {
		return $this->Name;
	}
	/* @param varchar(255) $Name */
	public function setName($Name) {
		$this->Name = $Name;
	}
	/* @return int(11) $this->Ignore */
	public function getIgnore() {
		return $this->Ignore;
	}
	/* @param int(11) $Ignore */
	public function setIgnore($Ignore) {
		$this->Ignore = $Ignore;
	}

}