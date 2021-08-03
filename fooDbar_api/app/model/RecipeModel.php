<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class RecipeModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_NAME = 'Name';
	const FIELD_DESCRIPTION = 'Description';
	const FIELD_INGREDIENTS_LIST = 'IngredientsList';

	/* int(11) */
	private $Id;

	/* varchar(255) */
	private $Name;

	/* longblob */
	private $Description;

	/* longblob */
	private $IngredientsList;


	public function __construct($values = null) {
		parent::__construct('recipe','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"Name":{"Field":"name","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"Description":{"Field":"description","Type":"longblob","Null":"NO","Key":"","Default":null,"Extra":""},"IngredientsList":{"Field":"ingredients_list","Type":"longblob","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
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
	/* @return longblob $this->Description */
	public function getDescription() {
		return $this->Description;
	}
	/* @param longblob $Description */
	public function setDescription($Description) {
		$this->Description = $Description;
	}
	/* @return longblob $this->IngredientsList */
	public function getIngredientsList() {
		return $this->IngredientsList;
	}
	/* @param longblob $IngredientsList */
	public function setIngredientsList($IngredientsList) {
		$this->IngredientsList = $IngredientsList;
	}

}