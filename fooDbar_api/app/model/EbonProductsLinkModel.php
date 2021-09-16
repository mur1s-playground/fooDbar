<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class EbonProductsLinkModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_EBON_PRODUCTS_ID = 'EbonProductsId';
	const FIELD_PRODUCTS_ID = 'ProductsId';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $EbonProductsId;

	/* int(11) */
	private $ProductsId;


	public function __construct($values = null) {
		parent::__construct('ebon_products_link','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"EbonProductsId":{"Field":"ebon_products_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"ProductsId":{"Field":"products_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return int(11) $this->EbonProductsId */
	public function getEbonProductsId() {
		return $this->EbonProductsId;
	}
	/* @param int(11) $EbonProductsId */
	public function setEbonProductsId($EbonProductsId) {
		$this->EbonProductsId = $EbonProductsId;
	}
	/* @return int(11) $this->ProductsId */
	public function getProductsId() {
		return $this->ProductsId;
	}
	/* @param int(11) $ProductsId */
	public function setProductsId($ProductsId) {
		$this->ProductsId = $ProductsId;
	}

}