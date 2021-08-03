<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class UsersStateModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_USERS_ID = 'UsersId';
	const FIELD_HEIGHT = 'Height';
	const FIELD_WEIGHT = 'Weight';
	const FIELD_BONE_PERCENT = 'BonePercent';
	const FIELD_FAT_PERCENT = 'FatPercent';
	const FIELD_WATER_PERCENT = 'WaterPercent';
	const FIELD_MUSCLE_PERCENT = 'MusclePercent';
	const FIELD_DATETIME_INSERT = 'DatetimeInsert';
	const FIELD_PAL = 'Pal';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $UsersId;

	/* double */
	private $Height;

	/* double */
	private $Weight;

	/* double */
	private $BonePercent;

	/* double */
	private $FatPercent;

	/* double */
	private $WaterPercent;

	/* double */
	private $MusclePercent;

	/* datetime */
	private $DatetimeInsert;

	/* double */
	private $Pal;


	public function __construct($values = null) {
		parent::__construct('users_state','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"UsersId":{"Field":"users_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"Height":{"Field":"height","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"Weight":{"Field":"weight","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"BonePercent":{"Field":"bone_percent","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"FatPercent":{"Field":"fat_percent","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"WaterPercent":{"Field":"water_percent","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"MusclePercent":{"Field":"muscle_percent","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"DatetimeInsert":{"Field":"datetime_insert","Type":"datetime","Null":"NO","Key":"","Default":null,"Extra":""},"Pal":{"Field":"pal","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
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
	/* @return double $this->Height */
	public function getHeight() {
		return $this->Height;
	}
	/* @param double $Height */
	public function setHeight($Height) {
		$this->Height = $Height;
	}
	/* @return double $this->Weight */
	public function getWeight() {
		return $this->Weight;
	}
	/* @param double $Weight */
	public function setWeight($Weight) {
		$this->Weight = $Weight;
	}
	/* @return double $this->BonePercent */
	public function getBonePercent() {
		return $this->BonePercent;
	}
	/* @param double $BonePercent */
	public function setBonePercent($BonePercent) {
		$this->BonePercent = $BonePercent;
	}
	/* @return double $this->FatPercent */
	public function getFatPercent() {
		return $this->FatPercent;
	}
	/* @param double $FatPercent */
	public function setFatPercent($FatPercent) {
		$this->FatPercent = $FatPercent;
	}
	/* @return double $this->WaterPercent */
	public function getWaterPercent() {
		return $this->WaterPercent;
	}
	/* @param double $WaterPercent */
	public function setWaterPercent($WaterPercent) {
		$this->WaterPercent = $WaterPercent;
	}
	/* @return double $this->MusclePercent */
	public function getMusclePercent() {
		return $this->MusclePercent;
	}
	/* @param double $MusclePercent */
	public function setMusclePercent($MusclePercent) {
		$this->MusclePercent = $MusclePercent;
	}
	/* @return datetime $this->DatetimeInsert */
	public function getDatetimeInsert() {
		return $this->DatetimeInsert;
	}
	/* @param datetime $DatetimeInsert */
	public function setDatetimeInsert($DatetimeInsert) {
		$this->DatetimeInsert = $DatetimeInsert;
	}
	/* @return double $this->Pal */
	public function getPal() {
		return $this->Pal;
	}
	/* @param double $Pal */
	public function setPal($Pal) {
		$this->Pal = $Pal;
	}

}