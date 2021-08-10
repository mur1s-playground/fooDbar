<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class RecipeConsumptionGroupAggModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_RECIPE_CONSUMPTION_GROUP_ID = 'RecipeConsumptionGroupId';
	const FIELD_RECIPE_CONSUMPTION_GROUP_COUNT = 'RecipeConsumptionGroupCount';
	const FIELD_PRICE_PER_MJ_MIN = 'PricePerMjMin';
	const FIELD_PRICE_PER_MJ_AVG = 'PricePerMjAvg';
	const FIELD_PRICE_PER_MJ_MAX = 'PricePerMjMax';
	const FIELD_MJ_MIN = 'MjMin';
	const FIELD_MJ_AVG = 'MjAvg';
	const FIELD_MJ_MAX = 'MjMax';
	const FIELD_N_FAT_PERCENT_MIN = 'NFatPercentMin';
	const FIELD_N_FAT_PERCENT_AVG = 'NFatPercentAvg';
	const FIELD_N_FAT_PERCENT_MAX = 'NFatPercentMax';
	const FIELD_N_CARBS_PERCENT_MIN = 'NCarbsPercentMin';
	const FIELD_N_CARBS_PERCENT_AVG = 'NCarbsPercentAvg';
	const FIELD_N_CARBS_PERCENT_MAX = 'NCarbsPercentMax';
	const FIELD_N_PROTEIN_PERCENT_MIN = 'NProteinPercentMin';
	const FIELD_N_PROTEIN_PERCENT_AVG = 'NProteinPercentAvg';
	const FIELD_N_PROTEIN_PERCENT_MAX = 'NProteinPercentMax';
	const FIELD_N_FIBER_PERCENT_MIN = 'NFiberPercentMin';
	const FIELD_N_FIBER_PERCENT_AVG = 'NFiberPercentAvg';
	const FIELD_N_FIBER_PERCENT_MAX = 'NFiberPercentMax';
	const FIELD_N_SALT_PERCENT_MIN = 'NSaltPercentMin';
	const FIELD_N_SALT_PERCENT_AVG = 'NSaltPercentAvg';
	const FIELD_N_SALT_PERCENT_MAX = 'NSaltPercentMax';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $RecipeConsumptionGroupId;

	/* int(11) */
	private $RecipeConsumptionGroupCount;

	/* double */
	private $PricePerMjMin;

	/* double */
	private $PricePerMjAvg;

	/* double */
	private $PricePerMjMax;

	/* double */
	private $MjMin;

	/* double */
	private $MjAvg;

	/* double */
	private $MjMax;

	/* double */
	private $NFatPercentMin;

	/* double */
	private $NFatPercentAvg;

	/* double */
	private $NFatPercentMax;

	/* double */
	private $NCarbsPercentMin;

	/* double */
	private $NCarbsPercentAvg;

	/* double */
	private $NCarbsPercentMax;

	/* double */
	private $NProteinPercentMin;

	/* double */
	private $NProteinPercentAvg;

	/* double */
	private $NProteinPercentMax;

	/* double */
	private $NFiberPercentMin;

	/* double */
	private $NFiberPercentAvg;

	/* double */
	private $NFiberPercentMax;

	/* double */
	private $NSaltPercentMin;

	/* double */
	private $NSaltPercentAvg;

	/* double */
	private $NSaltPercentMax;


	public function __construct($values = null) {
		parent::__construct('recipe_consumption_group_agg','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"RecipeConsumptionGroupId":{"Field":"recipe_consumption_group_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"RecipeConsumptionGroupCount":{"Field":"recipe_consumption_group_count","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"PricePerMjMin":{"Field":"price_per_mj_min","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"PricePerMjAvg":{"Field":"price_per_mj_avg","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"PricePerMjMax":{"Field":"price_per_mj_max","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"MjMin":{"Field":"mj_min","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"MjAvg":{"Field":"mj_avg","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"MjMax":{"Field":"mj_max","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NFatPercentMin":{"Field":"n_fat_percent_min","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NFatPercentAvg":{"Field":"n_fat_percent_avg","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NFatPercentMax":{"Field":"n_fat_percent_max","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NCarbsPercentMin":{"Field":"n_carbs_percent_min","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NCarbsPercentAvg":{"Field":"n_carbs_percent_avg","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NCarbsPercentMax":{"Field":"n_carbs_percent_max","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NProteinPercentMin":{"Field":"n_protein_percent_min","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NProteinPercentAvg":{"Field":"n_protein_percent_avg","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NProteinPercentMax":{"Field":"n_protein_percent_max","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NFiberPercentMin":{"Field":"n_fiber_percent_min","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NFiberPercentAvg":{"Field":"n_fiber_percent_avg","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NFiberPercentMax":{"Field":"n_fiber_percent_max","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NSaltPercentMin":{"Field":"n_salt_percent_min","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NSaltPercentAvg":{"Field":"n_salt_percent_avg","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"NSaltPercentMax":{"Field":"n_salt_percent_max","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return int(11) $this->RecipeConsumptionGroupId */
	public function getRecipeConsumptionGroupId() {
		return $this->RecipeConsumptionGroupId;
	}
	/* @param int(11) $RecipeConsumptionGroupId */
	public function setRecipeConsumptionGroupId($RecipeConsumptionGroupId) {
		$this->RecipeConsumptionGroupId = $RecipeConsumptionGroupId;
	}
	/* @return int(11) $this->RecipeConsumptionGroupCount */
	public function getRecipeConsumptionGroupCount() {
		return $this->RecipeConsumptionGroupCount;
	}
	/* @param int(11) $RecipeConsumptionGroupCount */
	public function setRecipeConsumptionGroupCount($RecipeConsumptionGroupCount) {
		$this->RecipeConsumptionGroupCount = $RecipeConsumptionGroupCount;
	}
	/* @return double $this->PricePerMjMin */
	public function getPricePerMjMin() {
		return $this->PricePerMjMin;
	}
	/* @param double $PricePerMjMin */
	public function setPricePerMjMin($PricePerMjMin) {
		$this->PricePerMjMin = $PricePerMjMin;
	}
	/* @return double $this->PricePerMjAvg */
	public function getPricePerMjAvg() {
		return $this->PricePerMjAvg;
	}
	/* @param double $PricePerMjAvg */
	public function setPricePerMjAvg($PricePerMjAvg) {
		$this->PricePerMjAvg = $PricePerMjAvg;
	}
	/* @return double $this->PricePerMjMax */
	public function getPricePerMjMax() {
		return $this->PricePerMjMax;
	}
	/* @param double $PricePerMjMax */
	public function setPricePerMjMax($PricePerMjMax) {
		$this->PricePerMjMax = $PricePerMjMax;
	}
	/* @return double $this->MjMin */
	public function getMjMin() {
		return $this->MjMin;
	}
	/* @param double $MjMin */
	public function setMjMin($MjMin) {
		$this->MjMin = $MjMin;
	}
	/* @return double $this->MjAvg */
	public function getMjAvg() {
		return $this->MjAvg;
	}
	/* @param double $MjAvg */
	public function setMjAvg($MjAvg) {
		$this->MjAvg = $MjAvg;
	}
	/* @return double $this->MjMax */
	public function getMjMax() {
		return $this->MjMax;
	}
	/* @param double $MjMax */
	public function setMjMax($MjMax) {
		$this->MjMax = $MjMax;
	}
	/* @return double $this->NFatPercentMin */
	public function getNFatPercentMin() {
		return $this->NFatPercentMin;
	}
	/* @param double $NFatPercentMin */
	public function setNFatPercentMin($NFatPercentMin) {
		$this->NFatPercentMin = $NFatPercentMin;
	}
	/* @return double $this->NFatPercentAvg */
	public function getNFatPercentAvg() {
		return $this->NFatPercentAvg;
	}
	/* @param double $NFatPercentAvg */
	public function setNFatPercentAvg($NFatPercentAvg) {
		$this->NFatPercentAvg = $NFatPercentAvg;
	}
	/* @return double $this->NFatPercentMax */
	public function getNFatPercentMax() {
		return $this->NFatPercentMax;
	}
	/* @param double $NFatPercentMax */
	public function setNFatPercentMax($NFatPercentMax) {
		$this->NFatPercentMax = $NFatPercentMax;
	}
	/* @return double $this->NCarbsPercentMin */
	public function getNCarbsPercentMin() {
		return $this->NCarbsPercentMin;
	}
	/* @param double $NCarbsPercentMin */
	public function setNCarbsPercentMin($NCarbsPercentMin) {
		$this->NCarbsPercentMin = $NCarbsPercentMin;
	}
	/* @return double $this->NCarbsPercentAvg */
	public function getNCarbsPercentAvg() {
		return $this->NCarbsPercentAvg;
	}
	/* @param double $NCarbsPercentAvg */
	public function setNCarbsPercentAvg($NCarbsPercentAvg) {
		$this->NCarbsPercentAvg = $NCarbsPercentAvg;
	}
	/* @return double $this->NCarbsPercentMax */
	public function getNCarbsPercentMax() {
		return $this->NCarbsPercentMax;
	}
	/* @param double $NCarbsPercentMax */
	public function setNCarbsPercentMax($NCarbsPercentMax) {
		$this->NCarbsPercentMax = $NCarbsPercentMax;
	}
	/* @return double $this->NProteinPercentMin */
	public function getNProteinPercentMin() {
		return $this->NProteinPercentMin;
	}
	/* @param double $NProteinPercentMin */
	public function setNProteinPercentMin($NProteinPercentMin) {
		$this->NProteinPercentMin = $NProteinPercentMin;
	}
	/* @return double $this->NProteinPercentAvg */
	public function getNProteinPercentAvg() {
		return $this->NProteinPercentAvg;
	}
	/* @param double $NProteinPercentAvg */
	public function setNProteinPercentAvg($NProteinPercentAvg) {
		$this->NProteinPercentAvg = $NProteinPercentAvg;
	}
	/* @return double $this->NProteinPercentMax */
	public function getNProteinPercentMax() {
		return $this->NProteinPercentMax;
	}
	/* @param double $NProteinPercentMax */
	public function setNProteinPercentMax($NProteinPercentMax) {
		$this->NProteinPercentMax = $NProteinPercentMax;
	}
	/* @return double $this->NFiberPercentMin */
	public function getNFiberPercentMin() {
		return $this->NFiberPercentMin;
	}
	/* @param double $NFiberPercentMin */
	public function setNFiberPercentMin($NFiberPercentMin) {
		$this->NFiberPercentMin = $NFiberPercentMin;
	}
	/* @return double $this->NFiberPercentAvg */
	public function getNFiberPercentAvg() {
		return $this->NFiberPercentAvg;
	}
	/* @param double $NFiberPercentAvg */
	public function setNFiberPercentAvg($NFiberPercentAvg) {
		$this->NFiberPercentAvg = $NFiberPercentAvg;
	}
	/* @return double $this->NFiberPercentMax */
	public function getNFiberPercentMax() {
		return $this->NFiberPercentMax;
	}
	/* @param double $NFiberPercentMax */
	public function setNFiberPercentMax($NFiberPercentMax) {
		$this->NFiberPercentMax = $NFiberPercentMax;
	}
	/* @return double $this->NSaltPercentMin */
	public function getNSaltPercentMin() {
		return $this->NSaltPercentMin;
	}
	/* @param double $NSaltPercentMin */
	public function setNSaltPercentMin($NSaltPercentMin) {
		$this->NSaltPercentMin = $NSaltPercentMin;
	}
	/* @return double $this->NSaltPercentAvg */
	public function getNSaltPercentAvg() {
		return $this->NSaltPercentAvg;
	}
	/* @param double $NSaltPercentAvg */
	public function setNSaltPercentAvg($NSaltPercentAvg) {
		$this->NSaltPercentAvg = $NSaltPercentAvg;
	}
	/* @return double $this->NSaltPercentMax */
	public function getNSaltPercentMax() {
		return $this->NSaltPercentMax;
	}
	/* @param double $NSaltPercentMax */
	public function setNSaltPercentMax($NSaltPercentMax) {
		$this->NSaltPercentMax = $NSaltPercentMax;
	}

}