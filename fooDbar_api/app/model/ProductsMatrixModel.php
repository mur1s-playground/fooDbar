<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class ProductsMatrixModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_USERS_ID = 'UsersId';
	const FIELD_PRODUCTS_ID = 'ProductsId';
	const FIELD_COMBINATION_TOTAL = 'CombinationTotal';
	const FIELD_COMBINATION_ROW = 'CombinationRow';
	const FIELD_COMBINATION_ROW_TOPDOWN = 'CombinationRowTopdown';
	const FIELD_COMBINATION_SIMILARITY_ROW = 'CombinationSimilarityRow';
	const FIELD_COMBINATION_SIMILARITY_ROW_TOPDOWN = 'CombinationSimilarityRowTopdown';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $UsersId;

	/* int(11) */
	private $ProductsId;

	/* int(11) */
	private $CombinationTotal;

	/* blob */
	private $CombinationRow;

	/* blob */
	private $CombinationRowTopdown;

	/* blob */
	private $CombinationSimilarityRow;

	/* blob */
	private $CombinationSimilarityRowTopdown;


	public function __construct($values = null) {
		parent::__construct('products_matrix','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"UsersId":{"Field":"users_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"ProductsId":{"Field":"products_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"CombinationTotal":{"Field":"combination_total","Type":"int(11)","Null":"YES","Key":"","Default":null,"Extra":""},"CombinationRow":{"Field":"combination_row","Type":"blob","Null":"YES","Key":"","Default":null,"Extra":""},"CombinationRowTopdown":{"Field":"combination_row_topdown","Type":"blob","Null":"YES","Key":"","Default":null,"Extra":""},"CombinationSimilarityRow":{"Field":"combination_similarity_row","Type":"blob","Null":"YES","Key":"","Default":null,"Extra":""},"CombinationSimilarityRowTopdown":{"Field":"combination_similarity_row_topdown","Type":"blob","Null":"YES","Key":"","Default":null,"Extra":""}}', $values);
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
	/* @return int(11) $this->ProductsId */
	public function getProductsId() {
		return $this->ProductsId;
	}
	/* @param int(11) $ProductsId */
	public function setProductsId($ProductsId) {
		$this->ProductsId = $ProductsId;
	}
	/* @return int(11) $this->CombinationTotal */
	public function getCombinationTotal() {
		return $this->CombinationTotal;
	}
	/* @param int(11) $CombinationTotal */
	public function setCombinationTotal($CombinationTotal) {
		$this->CombinationTotal = $CombinationTotal;
	}
	/* @return blob $this->CombinationRow */
	public function getCombinationRow() {
		return $this->CombinationRow;
	}
	/* @param blob $CombinationRow */
	public function setCombinationRow($CombinationRow) {
		$this->CombinationRow = $CombinationRow;
	}
	/* @return blob $this->CombinationRowTopdown */
	public function getCombinationRowTopdown() {
		return $this->CombinationRowTopdown;
	}
	/* @param blob $CombinationRowTopdown */
	public function setCombinationRowTopdown($CombinationRowTopdown) {
		$this->CombinationRowTopdown = $CombinationRowTopdown;
	}
	/* @return blob $this->CombinationSimilarityRow */
	public function getCombinationSimilarityRow() {
		return $this->CombinationSimilarityRow;
	}
	/* @param blob $CombinationSimilarityRow */
	public function setCombinationSimilarityRow($CombinationSimilarityRow) {
		$this->CombinationSimilarityRow = $CombinationSimilarityRow;
	}
	/* @return blob $this->CombinationSimilarityRowTopdown */
	public function getCombinationSimilarityRowTopdown() {
		return $this->CombinationSimilarityRowTopdown;
	}
	/* @param blob $CombinationSimilarityRowTopdown */
	public function setCombinationSimilarityRowTopdown($CombinationSimilarityRowTopdown) {
		$this->CombinationSimilarityRowTopdown = $CombinationSimilarityRowTopdown;
	}

}