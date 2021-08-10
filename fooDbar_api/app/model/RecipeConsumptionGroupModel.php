<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class RecipeConsumptionGroupModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_USERS_ID = 'UsersId';
	const FIELD_PRODUCTS_IDS = 'ProductsIds';
	const FIELD_AMOUNTS = 'Amounts';
	const FIELD_DATETIME = 'Datetime';
	const FIELD_MIN_PRICE = 'MinPrice';
	const FIELD_MAX_PRICE = 'MaxPrice';
	const FIELD_MJ = 'Mj';
	const FIELD_N_FAT_PERCENT = 'NFatPercent';
	const FIELD_N_CARBS_PERCENT = 'NCarbsPercent';
	const FIELD_N_PROTEIN_PERCENT = 'NProteinPercent';
	const FIELD_N_FIBER_PERCENT = 'NFiberPercent';
	const FIELD_N_SALT_PERCENT = 'NSaltPercent';
	const FIELD_RECIPE_CONSUMPTION_GROUP_ALLERGIES_ID = 'RecipeConsumptionGroupAllergiesId';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $UsersId;

	/* varchar(255) */
	private $ProductsIds;

	/* varchar(255) */
	private $Amounts;

	/* datetime */
	private $Datetime;

	/* double */
	private $MinPrice;

	/* double */
	private $MaxPrice;

	/* double */
	private $Mj;

	/* double */
	private $NFatPercent;

	/* double */
	private $NCarbsPercent;

	/* double */
	private $NProteinPercent;

	/* double */
	private $NFiberPercent;

	/* double */
	private $NSaltPercent;

	/* int(11) */
	private $RecipeConsumptionGroupAllergiesId;


	public function __construct($values = null) {
		parent::__construct('recipe_consumption_group','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"UsersId":{"Field":"users_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"ProductsIds":{"Field":"products_ids","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"Amounts":{"Field":"amounts","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"Datetime":{"Field":"datetime","Type":"datetime","Null":"NO","Key":"","Default":null,"Extra":""},"MinPrice":{"Field":"min_price","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"MaxPrice":{"Field":"max_price","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"Mj":{"Field":"mj","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NFatPercent":{"Field":"n_fat_percent","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"NCarbsPercent":{"Field":"n_carbs_percent","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"NProteinPercent":{"Field":"n_protein_percent","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"NFiberPercent":{"Field":"n_fiber_percent","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"NSaltPercent":{"Field":"n_salt_percent","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"RecipeConsumptionGroupAllergiesId":{"Field":"recipe_consumption_group_allergies_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
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
	/* @return varchar(255) $this->ProductsIds */
	public function getProductsIds() {
		return $this->ProductsIds;
	}
	/* @param varchar(255) $ProductsIds */
	public function setProductsIds($ProductsIds) {
		$this->ProductsIds = $ProductsIds;
	}
	/* @return varchar(255) $this->Amounts */
	public function getAmounts() {
		return $this->Amounts;
	}
	/* @param varchar(255) $Amounts */
	public function setAmounts($Amounts) {
		$this->Amounts = $Amounts;
	}
	/* @return datetime $this->Datetime */
	public function getDatetime() {
		return $this->Datetime;
	}
	/* @param datetime $Datetime */
	public function setDatetime($Datetime) {
		$this->Datetime = $Datetime;
	}
	/* @return double $this->MinPrice */
	public function getMinPrice() {
		return $this->MinPrice;
	}
	/* @param double $MinPrice */
	public function setMinPrice($MinPrice) {
		$this->MinPrice = $MinPrice;
	}
	/* @return double $this->MaxPrice */
	public function getMaxPrice() {
		return $this->MaxPrice;
	}
	/* @param double $MaxPrice */
	public function setMaxPrice($MaxPrice) {
		$this->MaxPrice = $MaxPrice;
	}
	/* @return double $this->Mj */
	public function getMj() {
		return $this->Mj;
	}
	/* @param double $Mj */
	public function setMj($Mj) {
		$this->Mj = $Mj;
	}
	/* @return double $this->NFatPercent */
	public function getNFatPercent() {
		return $this->NFatPercent;
	}
	/* @param double $NFatPercent */
	public function setNFatPercent($NFatPercent) {
		$this->NFatPercent = $NFatPercent;
	}
	/* @return double $this->NCarbsPercent */
	public function getNCarbsPercent() {
		return $this->NCarbsPercent;
	}
	/* @param double $NCarbsPercent */
	public function setNCarbsPercent($NCarbsPercent) {
		$this->NCarbsPercent = $NCarbsPercent;
	}
	/* @return double $this->NProteinPercent */
	public function getNProteinPercent() {
		return $this->NProteinPercent;
	}
	/* @param double $NProteinPercent */
	public function setNProteinPercent($NProteinPercent) {
		$this->NProteinPercent = $NProteinPercent;
	}
	/* @return double $this->NFiberPercent */
	public function getNFiberPercent() {
		return $this->NFiberPercent;
	}
	/* @param double $NFiberPercent */
	public function setNFiberPercent($NFiberPercent) {
		$this->NFiberPercent = $NFiberPercent;
	}
	/* @return double $this->NSaltPercent */
	public function getNSaltPercent() {
		return $this->NSaltPercent;
	}
	/* @param double $NSaltPercent */
	public function setNSaltPercent($NSaltPercent) {
		$this->NSaltPercent = $NSaltPercent;
	}
	/* @return int(11) $this->RecipeConsumptionGroupAllergiesId */
	public function getRecipeConsumptionGroupAllergiesId() {
		return $this->RecipeConsumptionGroupAllergiesId;
	}
	/* @param int(11) $RecipeConsumptionGroupAllergiesId */
	public function setRecipeConsumptionGroupAllergiesId($RecipeConsumptionGroupAllergiesId) {
		$this->RecipeConsumptionGroupAllergiesId = $RecipeConsumptionGroupAllergiesId;
	}

}