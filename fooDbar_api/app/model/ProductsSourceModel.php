<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class ProductsSourceModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_NAME = 'Name';
	const FIELD_ADDRESS = 'Address';
	const FIELD_ZIPCODE = 'Zipcode';
	const FIELD_CITY = 'City';
	const FIELD_PRODUCTS_SOURCE_TYPE_ID = 'ProductsSourceTypeId';

	/* int(11) */
	private $Id;

	/* varchar(255) */
	private $Name;

	/* varchar(255) */
	private $Address;

	/* varchar(255) */
	private $Zipcode;

	/* varchar(255) */
	private $City;

	/* int(11) */
	private $ProductsSourceTypeId;


	public function __construct($values = null) {
		parent::__construct('products_source','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"Name":{"Field":"name","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"Address":{"Field":"address","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"Zipcode":{"Field":"zipcode","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"City":{"Field":"city","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"ProductsSourceTypeId":{"Field":"products_source_type_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return varchar(255) $this->Name */
	public function getName() {
		return $this->Name;
	}
	/* @param varchar(255) $Name */
	public function setName($Name) {
		$this->Name = $Name;
	}
	/* @return varchar(255) $this->Address */
	public function getAddress() {
		return $this->Address;
	}
	/* @param varchar(255) $Address */
	public function setAddress($Address) {
		$this->Address = $Address;
	}
	/* @return varchar(255) $this->Zipcode */
	public function getZipcode() {
		return $this->Zipcode;
	}
	/* @param varchar(255) $Zipcode */
	public function setZipcode($Zipcode) {
		$this->Zipcode = $Zipcode;
	}
	/* @return varchar(255) $this->City */
	public function getCity() {
		return $this->City;
	}
	/* @param varchar(255) $City */
	public function setCity($City) {
		$this->City = $City;
	}
	/* @return int(11) $this->ProductsSourceTypeId */
	public function getProductsSourceTypeId() {
		return $this->ProductsSourceTypeId;
	}
	/* @param int(11) $ProductsSourceTypeId */
	public function setProductsSourceTypeId($ProductsSourceTypeId) {
		$this->ProductsSourceTypeId = $ProductsSourceTypeId;
	}

}