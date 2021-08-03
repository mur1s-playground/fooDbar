<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class StorageModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_PRODUCTS_ID = 'ProductsId';
	const FIELD_AMOUNT = 'Amount';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $ProductsId;

	/* int(11) */
	private $Amount;


	public function __construct($values = null) {
		parent::__construct('storage','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"ProductsId":{"Field":"products_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"Amount":{"Field":"amount","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return int(11) $this->ProductsId */
	public function getProductsId() {
		return $this->ProductsId;
	}
	/* @param int(11) $ProductsId */
	public function setProductsId($ProductsId) {
		$this->ProductsId = $ProductsId;
	}
	/* @return int(11) $this->Amount */
	public function getAmount() {
		return $this->Amount;
	}
	/* @param int(11) $Amount */
	public function setAmount($Amount) {
		$this->Amount = $Amount;
	}

}