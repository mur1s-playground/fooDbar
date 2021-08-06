<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class StorageConsumptionModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_STORAGE_ID = 'StorageId';
	const FIELD_AMOUNT = 'Amount';
	const FIELD_DATETIME = 'Datetime';
	const FIELD_RECIPE_ID = 'RecipeId';
	const FIELD_USERS_ID = 'UsersId';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $StorageId;

	/* double */
	private $Amount;

	/* datetime */
	private $Datetime;

	/* int(11) */
	private $RecipeId;

	/* int(11) */
	private $UsersId;


	public function __construct($values = null) {
		parent::__construct('storage_consumption','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"StorageId":{"Field":"storage_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"Amount":{"Field":"amount","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"Datetime":{"Field":"datetime","Type":"datetime","Null":"NO","Key":"","Default":null,"Extra":""},"RecipeId":{"Field":"recipe_id","Type":"int(11)","Null":"YES","Key":"","Default":null,"Extra":""},"UsersId":{"Field":"users_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return int(11) $this->StorageId */
	public function getStorageId() {
		return $this->StorageId;
	}
	/* @param int(11) $StorageId */
	public function setStorageId($StorageId) {
		$this->StorageId = $StorageId;
	}
	/* @return double $this->Amount */
	public function getAmount() {
		return $this->Amount;
	}
	/* @param double $Amount */
	public function setAmount($Amount) {
		$this->Amount = $Amount;
	}
	/* @return datetime $this->Datetime */
	public function getDatetime() {
		return $this->Datetime;
	}
	/* @param datetime $Datetime */
	public function setDatetime($Datetime) {
		$this->Datetime = $Datetime;
	}
	/* @return int(11) $this->RecipeId */
	public function getRecipeId() {
		return $this->RecipeId;
	}
	/* @param int(11) $RecipeId */
	public function setRecipeId($RecipeId) {
		$this->RecipeId = $RecipeId;
	}
	/* @return int(11) $this->UsersId */
	public function getUsersId() {
		return $this->UsersId;
	}
	/* @param int(11) $UsersId */
	public function setUsersId($UsersId) {
		$this->UsersId = $UsersId;
	}

}