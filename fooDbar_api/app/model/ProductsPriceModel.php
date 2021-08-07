<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class ProductsPriceModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_PRODUCTS_ID = 'ProductsId';
	const FIELD_PRODUCTS_SOURCE_ID = 'ProductsSourceId';
	const FIELD_PRICE = 'Price';
	const FIELD_DATETIME = 'Datetime';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $ProductsId;

	/* int(11) */
	private $ProductsSourceId;

	/* double */
	private $Price;

	/* datetime */
	private $Datetime;


	public function __construct($values = null) {
		parent::__construct('products_price','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"ProductsId":{"Field":"products_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"ProductsSourceId":{"Field":"products_source_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"Price":{"Field":"price","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"Datetime":{"Field":"datetime","Type":"datetime","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
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
	/* @return int(11) $this->ProductsSourceId */
	public function getProductsSourceId() {
		return $this->ProductsSourceId;
	}
	/* @param int(11) $ProductsSourceId */
	public function setProductsSourceId($ProductsSourceId) {
		$this->ProductsSourceId = $ProductsSourceId;
	}
	/* @return double $this->Price */
	public function getPrice() {
		return $this->Price;
	}
	/* @param double $Price */
	public function setPrice($Price) {
		$this->Price = $Price;
	}
	/* @return datetime $this->Datetime */
	public function getDatetime() {
		return $this->Datetime;
	}
	/* @param datetime $Datetime */
	public function setDatetime($Datetime) {
		$this->Datetime = $Datetime;
	}

}