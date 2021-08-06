<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class StorageModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_STORAGES_ID = 'StoragesId';
	const FIELD_PRODUCTS_ID = 'ProductsId';
	const FIELD_AMOUNT = 'Amount';
	const FIELD_DATETIME_INSERT = 'DatetimeInsert';
	const FIELD_DATETIME_OPEN = 'DatetimeOpen';
	const FIELD_DATETIME_EMPTY = 'DatetimeEmpty';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $StoragesId;

	/* int(11) */
	private $ProductsId;

	/* int(11) */
	private $Amount;

	/* datetime */
	private $DatetimeInsert;

	/* datetime */
	private $DatetimeOpen;

	/* datetime */
	private $DatetimeEmpty;


	public function __construct($values = null) {
		parent::__construct('storage','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"StoragesId":{"Field":"storages_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"ProductsId":{"Field":"products_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"Amount":{"Field":"amount","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"DatetimeInsert":{"Field":"datetime_insert","Type":"datetime","Null":"NO","Key":"","Default":null,"Extra":""},"DatetimeOpen":{"Field":"datetime_open","Type":"datetime","Null":"YES","Key":"","Default":null,"Extra":""},"DatetimeEmpty":{"Field":"datetime_empty","Type":"datetime","Null":"YES","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return int(11) $this->StoragesId */
	public function getStoragesId() {
		return $this->StoragesId;
	}
	/* @param int(11) $StoragesId */
	public function setStoragesId($StoragesId) {
		$this->StoragesId = $StoragesId;
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
	/* @return datetime $this->DatetimeInsert */
	public function getDatetimeInsert() {
		return $this->DatetimeInsert;
	}
	/* @param datetime $DatetimeInsert */
	public function setDatetimeInsert($DatetimeInsert) {
		$this->DatetimeInsert = $DatetimeInsert;
	}
	/* @return datetime $this->DatetimeOpen */
	public function getDatetimeOpen() {
		return $this->DatetimeOpen;
	}
	/* @param datetime $DatetimeOpen */
	public function setDatetimeOpen($DatetimeOpen) {
		$this->DatetimeOpen = $DatetimeOpen;
	}
	/* @return datetime $this->DatetimeEmpty */
	public function getDatetimeEmpty() {
		return $this->DatetimeEmpty;
	}
	/* @param datetime $DatetimeEmpty */
	public function setDatetimeEmpty($DatetimeEmpty) {
		$this->DatetimeEmpty = $DatetimeEmpty;
	}

}