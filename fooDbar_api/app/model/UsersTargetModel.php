<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class UsersTargetModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_USERS_ID = 'UsersId';
	const FIELD_BMI = 'Bmi';
	const FIELD_FAT_PERCENT = 'FatPercent';
	const FIELD_MUSCLE_PERCENT = 'MusclePercent';
	const FIELD_DATE_INSERT = 'DateInsert';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $UsersId;

	/* double */
	private $Bmi;

	/* double */
	private $FatPercent;

	/* double */
	private $MusclePercent;

	/* date */
	private $DateInsert;


	public function __construct($values = null) {
		parent::__construct('users_target','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"UsersId":{"Field":"users_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"Bmi":{"Field":"bmi","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"FatPercent":{"Field":"fat_percent","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"MusclePercent":{"Field":"muscle_percent","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"DateInsert":{"Field":"date_insert","Type":"date","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
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
	/* @return double $this->Bmi */
	public function getBmi() {
		return $this->Bmi;
	}
	/* @param double $Bmi */
	public function setBmi($Bmi) {
		$this->Bmi = $Bmi;
	}
	/* @return double $this->FatPercent */
	public function getFatPercent() {
		return $this->FatPercent;
	}
	/* @param double $FatPercent */
	public function setFatPercent($FatPercent) {
		$this->FatPercent = $FatPercent;
	}
	/* @return double $this->MusclePercent */
	public function getMusclePercent() {
		return $this->MusclePercent;
	}
	/* @param double $MusclePercent */
	public function setMusclePercent($MusclePercent) {
		$this->MusclePercent = $MusclePercent;
	}
	/* @return date $this->DateInsert */
	public function getDateInsert() {
		return $this->DateInsert;
	}
	/* @param date $DateInsert */
	public function setDateInsert($DateInsert) {
		$this->DateInsert = $DateInsert;
	}

}