<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class RecipeConsumptionGroupAggPlannedModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_USERS_ID = 'UsersId';
	const FIELD_RECIPE_CONSUMPTION_GROUP_AGG_ID = 'RecipeConsumptionGroupAggId';
	const FIELD_AMOUNTS = 'Amounts';

	/* int(11) */
	private $Id;

	/* varchar(45) */
	private $UsersId;

	/* varchar(45) */
	private $RecipeConsumptionGroupAggId;

	/* varchar(45) */
	private $Amounts;


	public function __construct($values = null) {
		parent::__construct('recipe_consumption_group_agg_planned','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"UsersId":{"Field":"users_id","Type":"varchar(45)","Null":"NO","Key":"","Default":null,"Extra":""},"RecipeConsumptionGroupAggId":{"Field":"recipe_consumption_group_agg_id","Type":"varchar(45)","Null":"NO","Key":"","Default":null,"Extra":""},"Amounts":{"Field":"amounts","Type":"varchar(45)","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return varchar(45) $this->UsersId */
	public function getUsersId() {
		return $this->UsersId;
	}
	/* @param varchar(45) $UsersId */
	public function setUsersId($UsersId) {
		$this->UsersId = $UsersId;
	}
	/* @return varchar(45) $this->RecipeConsumptionGroupAggId */
	public function getRecipeConsumptionGroupAggId() {
		return $this->RecipeConsumptionGroupAggId;
	}
	/* @param varchar(45) $RecipeConsumptionGroupAggId */
	public function setRecipeConsumptionGroupAggId($RecipeConsumptionGroupAggId) {
		$this->RecipeConsumptionGroupAggId = $RecipeConsumptionGroupAggId;
	}
	/* @return varchar(45) $this->Amounts */
	public function getAmounts() {
		return $this->Amounts;
	}
	/* @param varchar(45) $Amounts */
	public function setAmounts($Amounts) {
		$this->Amounts = $Amounts;
	}

}