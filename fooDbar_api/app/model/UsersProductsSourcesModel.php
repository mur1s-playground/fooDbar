<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class UsersProductsSourcesModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_USERS_ID = 'UsersId';
	const FIELD_PRODUCTS_SOURCE_ID = 'ProductsSourceId';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $UsersId;

	/* int(11) */
	private $ProductsSourceId;


	public function __construct($values = null) {
		parent::__construct('users_products_sources','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"UsersId":{"Field":"users_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"ProductsSourceId":{"Field":"products_source_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
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
	/* @return int(11) $this->ProductsSourceId */
	public function getProductsSourceId() {
		return $this->ProductsSourceId;
	}
	/* @param int(11) $ProductsSourceId */
	public function setProductsSourceId($ProductsSourceId) {
		$this->ProductsSourceId = $ProductsSourceId;
	}

}