<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class ShoppingListModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_USERS_ID = 'UsersId';
	const FIELD_PRODUCTS_ID = 'ProductsId';
	const FIELD_AMOUNT = 'Amount';
	const FIELD_ORDER_ID = 'OrderId';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $UsersId;

	/* int(11) */
	private $ProductsId;

	/* double */
	private $Amount;

	/* int(11) */
	private $OrderId;


	public function __construct($values = null) {
		parent::__construct('shopping_list','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"UsersId":{"Field":"users_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"ProductsId":{"Field":"products_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"Amount":{"Field":"amount","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"OrderId":{"Field":"order_id","Type":"int(11)","Null":"YES","Key":"","Default":null,"Extra":""}}', $values);
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
	/* @return int(11) $this->ProductsId */
	public function getProductsId() {
		return $this->ProductsId;
	}
	/* @param int(11) $ProductsId */
	public function setProductsId($ProductsId) {
		$this->ProductsId = $ProductsId;
	}
	/* @return double $this->Amount */
	public function getAmount() {
		return $this->Amount;
	}
	/* @param double $Amount */
	public function setAmount($Amount) {
		$this->Amount = $Amount;
	}
	/* @return int(11) $this->OrderId */
	public function getOrderId() {
		return $this->OrderId;
	}
	/* @param int(11) $OrderId */
	public function setOrderId($OrderId) {
		$this->OrderId = $OrderId;
	}

}