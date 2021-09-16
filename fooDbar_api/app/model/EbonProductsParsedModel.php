<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class EbonProductsParsedModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_USERS_ID = 'UsersId';
	const FIELD_DATETIME = 'Datetime';
	const FIELD_EBON_PRODUCTS_ID = 'EbonProductsId';
	const FIELD_AMOUNT_TYPE_ID = 'AmountTypeId';
	const FIELD_AMOUNT = 'Amount';
	const FIELD_PRICE = 'Price';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $UsersId;

	/* datetime */
	private $Datetime;

	/* int(11) */
	private $EbonProductsId;

	/* int(11) */
	private $AmountTypeId;

	/* double */
	private $Amount;

	/* double */
	private $Price;


	public function __construct($values = null) {
		parent::__construct('ebon_products_parsed','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"UsersId":{"Field":"users_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"Datetime":{"Field":"datetime","Type":"datetime","Null":"NO","Key":"","Default":null,"Extra":""},"EbonProductsId":{"Field":"ebon_products_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"AmountTypeId":{"Field":"amount_type_id","Type":"int(11)","Null":"YES","Key":"","Default":null,"Extra":""},"Amount":{"Field":"amount","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"Price":{"Field":"price","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
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
	/* @return datetime $this->Datetime */
	public function getDatetime() {
		return $this->Datetime;
	}
	/* @param datetime $Datetime */
	public function setDatetime($Datetime) {
		$this->Datetime = $Datetime;
	}
	/* @return int(11) $this->EbonProductsId */
	public function getEbonProductsId() {
		return $this->EbonProductsId;
	}
	/* @param int(11) $EbonProductsId */
	public function setEbonProductsId($EbonProductsId) {
		$this->EbonProductsId = $EbonProductsId;
	}
	/* @return int(11) $this->AmountTypeId */
	public function getAmountTypeId() {
		return $this->AmountTypeId;
	}
	/* @param int(11) $AmountTypeId */
	public function setAmountTypeId($AmountTypeId) {
		$this->AmountTypeId = $AmountTypeId;
	}
	/* @return double $this->Amount */
	public function getAmount() {
		return $this->Amount;
	}
	/* @param double $Amount */
	public function setAmount($Amount) {
		$this->Amount = $Amount;
	}
	/* @return double $this->Price */
	public function getPrice() {
		return $this->Price;
	}
	/* @param double $Price */
	public function setPrice($Price) {
		$this->Price = $Price;
	}

}