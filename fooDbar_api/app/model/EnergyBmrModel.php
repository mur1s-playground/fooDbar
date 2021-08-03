<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class EnergyBmrModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_GENDER_ID = 'GenderId';
	const FIELD_AGE_GROUP_ID = 'AgeGroupId';
	const FIELD_KG_FACTOR = 'KgFactor';
	const FIELD_SCALAR_CORRECTION = 'ScalarCorrection';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $GenderId;

	/* int(11) */
	private $AgeGroupId;

	/* double */
	private $KgFactor;

	/* double */
	private $ScalarCorrection;


	public function __construct($values = null) {
		parent::__construct('energy_bmr','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"GenderId":{"Field":"gender_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"AgeGroupId":{"Field":"age_group_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"KgFactor":{"Field":"kg_factor","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"ScalarCorrection":{"Field":"scalar_correction","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return int(11) $this->GenderId */
	public function getGenderId() {
		return $this->GenderId;
	}
	/* @param int(11) $GenderId */
	public function setGenderId($GenderId) {
		$this->GenderId = $GenderId;
	}
	/* @return int(11) $this->AgeGroupId */
	public function getAgeGroupId() {
		return $this->AgeGroupId;
	}
	/* @param int(11) $AgeGroupId */
	public function setAgeGroupId($AgeGroupId) {
		$this->AgeGroupId = $AgeGroupId;
	}
	/* @return double $this->KgFactor */
	public function getKgFactor() {
		return $this->KgFactor;
	}
	/* @param double $KgFactor */
	public function setKgFactor($KgFactor) {
		$this->KgFactor = $KgFactor;
	}
	/* @return double $this->ScalarCorrection */
	public function getScalarCorrection() {
		return $this->ScalarCorrection;
	}
	/* @param double $ScalarCorrection */
	public function setScalarCorrection($ScalarCorrection) {
		$this->ScalarCorrection = $ScalarCorrection;
	}

}