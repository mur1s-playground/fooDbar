<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class RecipeConsumptionGroupAllergiesModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_PRODUCTS_IDS = 'ProductsIds';
	const FIELD_A_NOT_VEGETARIAN = 'ANotVegetarian';
	const FIELD_A_NOT_VEGAN = 'ANotVegan';
	const FIELD_A_GLUTEN = 'AGluten';
	const FIELD_A_CRUSTACEANS = 'ACrustaceans';
	const FIELD_A_EGGS = 'AEggs';
	const FIELD_A_FISH = 'AFish';
	const FIELD_A_PEANUTS = 'APeanuts';
	const FIELD_A_SOYBEANS = 'ASoybeans';
	const FIELD_A_MILK = 'AMilk';
	const FIELD_A_NUTS = 'ANuts';
	const FIELD_A_CELERIAC = 'ACeleriac';
	const FIELD_A_MUSTARD = 'AMustard';
	const FIELD_A_SESAM = 'ASesam';
	const FIELD_A_SULFUR = 'ASulfur';
	const FIELD_A_LUPINS = 'ALupins';
	const FIELD_A_MOLLUSCS = 'AMolluscs';

	/* int(11) */
	private $Id;

	/* varchar(255) */
	private $ProductsIds;

	/* tinyint(4) */
	private $ANotVegetarian;

	/* tinyint(4) */
	private $ANotVegan;

	/* tinyint(4) */
	private $AGluten;

	/* tinyint(4) */
	private $ACrustaceans;

	/* tinyint(4) */
	private $AEggs;

	/* tinyint(4) */
	private $AFish;

	/* tinyint(4) */
	private $APeanuts;

	/* tinyint(4) */
	private $ASoybeans;

	/* tinyint(4) */
	private $AMilk;

	/* tinyint(4) */
	private $ANuts;

	/* tinyint(4) */
	private $ACeleriac;

	/* tinyint(4) */
	private $AMustard;

	/* tinyint(4) */
	private $ASesam;

	/* tinyint(4) */
	private $ASulfur;

	/* tinyint(4) */
	private $ALupins;

	/* tinyint(4) */
	private $AMolluscs;


	public function __construct($values = null) {
		parent::__construct('recipe_consumption_group_allergies','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"ProductsIds":{"Field":"products_ids","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"ANotVegetarian":{"Field":"a_not_vegetarian","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ANotVegan":{"Field":"a_not_vegan","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"AGluten":{"Field":"a_gluten","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ACrustaceans":{"Field":"a_crustaceans","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"AEggs":{"Field":"a_eggs","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"AFish":{"Field":"a_fish","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"APeanuts":{"Field":"a_peanuts","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ASoybeans":{"Field":"a_soybeans","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"AMilk":{"Field":"a_milk","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ANuts":{"Field":"a_nuts","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ACeleriac":{"Field":"a_celeriac","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"AMustard":{"Field":"a_mustard","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ASesam":{"Field":"a_sesam","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ASulfur":{"Field":"a_sulfur","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ALupins":{"Field":"a_lupins","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"AMolluscs":{"Field":"a_molluscs","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return varchar(255) $this->ProductsIds */
	public function getProductsIds() {
		return $this->ProductsIds;
	}
	/* @param varchar(255) $ProductsIds */
	public function setProductsIds($ProductsIds) {
		$this->ProductsIds = $ProductsIds;
	}
	/* @return tinyint(4) $this->ANotVegetarian */
	public function getANotVegetarian() {
		return $this->ANotVegetarian;
	}
	/* @param tinyint(4) $ANotVegetarian */
	public function setANotVegetarian($ANotVegetarian) {
		$this->ANotVegetarian = $ANotVegetarian;
	}
	/* @return tinyint(4) $this->ANotVegan */
	public function getANotVegan() {
		return $this->ANotVegan;
	}
	/* @param tinyint(4) $ANotVegan */
	public function setANotVegan($ANotVegan) {
		$this->ANotVegan = $ANotVegan;
	}
	/* @return tinyint(4) $this->AGluten */
	public function getAGluten() {
		return $this->AGluten;
	}
	/* @param tinyint(4) $AGluten */
	public function setAGluten($AGluten) {
		$this->AGluten = $AGluten;
	}
	/* @return tinyint(4) $this->ACrustaceans */
	public function getACrustaceans() {
		return $this->ACrustaceans;
	}
	/* @param tinyint(4) $ACrustaceans */
	public function setACrustaceans($ACrustaceans) {
		$this->ACrustaceans = $ACrustaceans;
	}
	/* @return tinyint(4) $this->AEggs */
	public function getAEggs() {
		return $this->AEggs;
	}
	/* @param tinyint(4) $AEggs */
	public function setAEggs($AEggs) {
		$this->AEggs = $AEggs;
	}
	/* @return tinyint(4) $this->AFish */
	public function getAFish() {
		return $this->AFish;
	}
	/* @param tinyint(4) $AFish */
	public function setAFish($AFish) {
		$this->AFish = $AFish;
	}
	/* @return tinyint(4) $this->APeanuts */
	public function getAPeanuts() {
		return $this->APeanuts;
	}
	/* @param tinyint(4) $APeanuts */
	public function setAPeanuts($APeanuts) {
		$this->APeanuts = $APeanuts;
	}
	/* @return tinyint(4) $this->ASoybeans */
	public function getASoybeans() {
		return $this->ASoybeans;
	}
	/* @param tinyint(4) $ASoybeans */
	public function setASoybeans($ASoybeans) {
		$this->ASoybeans = $ASoybeans;
	}
	/* @return tinyint(4) $this->AMilk */
	public function getAMilk() {
		return $this->AMilk;
	}
	/* @param tinyint(4) $AMilk */
	public function setAMilk($AMilk) {
		$this->AMilk = $AMilk;
	}
	/* @return tinyint(4) $this->ANuts */
	public function getANuts() {
		return $this->ANuts;
	}
	/* @param tinyint(4) $ANuts */
	public function setANuts($ANuts) {
		$this->ANuts = $ANuts;
	}
	/* @return tinyint(4) $this->ACeleriac */
	public function getACeleriac() {
		return $this->ACeleriac;
	}
	/* @param tinyint(4) $ACeleriac */
	public function setACeleriac($ACeleriac) {
		$this->ACeleriac = $ACeleriac;
	}
	/* @return tinyint(4) $this->AMustard */
	public function getAMustard() {
		return $this->AMustard;
	}
	/* @param tinyint(4) $AMustard */
	public function setAMustard($AMustard) {
		$this->AMustard = $AMustard;
	}
	/* @return tinyint(4) $this->ASesam */
	public function getASesam() {
		return $this->ASesam;
	}
	/* @param tinyint(4) $ASesam */
	public function setASesam($ASesam) {
		$this->ASesam = $ASesam;
	}
	/* @return tinyint(4) $this->ASulfur */
	public function getASulfur() {
		return $this->ASulfur;
	}
	/* @param tinyint(4) $ASulfur */
	public function setASulfur($ASulfur) {
		$this->ASulfur = $ASulfur;
	}
	/* @return tinyint(4) $this->ALupins */
	public function getALupins() {
		return $this->ALupins;
	}
	/* @param tinyint(4) $ALupins */
	public function setALupins($ALupins) {
		$this->ALupins = $ALupins;
	}
	/* @return tinyint(4) $this->AMolluscs */
	public function getAMolluscs() {
		return $this->AMolluscs;
	}
	/* @param tinyint(4) $AMolluscs */
	public function setAMolluscs($AMolluscs) {
		$this->AMolluscs = $AMolluscs;
	}

}