<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class ProductsModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_NAME = 'Name';
	const FIELD_PRODUCTS_SOURCE_ID = 'ProductsSourceId';
	const FIELD_AMOUNT = 'Amount';
	const FIELD_AMOUNT_TYPE_ID = 'AmountTypeId';
	const FIELD_PRICE = 'Price';
	const FIELD_LAST_SEEN = 'LastSeen';
	const FIELD_KJ = 'Kj';
	const FIELD_N_FAT = 'NFat';
	const FIELD_N_CARBS = 'NCarbs';
	const FIELD_N_PROTEIN = 'NProtein';
	const FIELD_N_FIBER = 'NFiber';
	const FIELD_N_SALT = 'NSalt';
	const FIELD_N_CALCIUM = 'NCalcium';
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
	private $Name;

	/* int(11) */
	private $ProductsSourceId;

	/* double */
	private $Amount;

	/* int(11) */
	private $AmountTypeId;

	/* double */
	private $Price;

	/* datetime */
	private $LastSeen;

	/* double */
	private $Kj;

	/* double */
	private $NFat;

	/* double */
	private $NCarbs;

	/* double */
	private $NProtein;

	/* double */
	private $NFiber;

	/* double */
	private $NSalt;

	/* double */
	private $NCalcium;

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
		parent::__construct('products','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"Name":{"Field":"name","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"ProductsSourceId":{"Field":"products_source_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"Amount":{"Field":"amount","Type":"double","Null":"NO","Key":"","Default":null,"Extra":""},"AmountTypeId":{"Field":"amount_type_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"Price":{"Field":"price","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"LastSeen":{"Field":"last_seen","Type":"datetime","Null":"NO","Key":"","Default":null,"Extra":""},"Kj":{"Field":"kj","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"NFat":{"Field":"n_fat","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"NCarbs":{"Field":"n_carbs","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"NProtein":{"Field":"n_protein","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"NFiber":{"Field":"n_fiber","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"NSalt":{"Field":"n_salt","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"NCalcium":{"Field":"n_calcium","Type":"double","Null":"YES","Key":"","Default":null,"Extra":""},"ANotVegetarian":{"Field":"a_not_vegetarian","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ANotVegan":{"Field":"a_not_vegan","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"AGluten":{"Field":"a_gluten","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ACrustaceans":{"Field":"a_crustaceans","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"AEggs":{"Field":"a_eggs","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"AFish":{"Field":"a_fish","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"APeanuts":{"Field":"a_peanuts","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ASoybeans":{"Field":"a_soybeans","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"AMilk":{"Field":"a_milk","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ANuts":{"Field":"a_nuts","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ACeleriac":{"Field":"a_celeriac","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"AMustard":{"Field":"a_mustard","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ASesam":{"Field":"a_sesam","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ASulfur":{"Field":"a_sulfur","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"ALupins":{"Field":"a_lupins","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""},"AMolluscs":{"Field":"a_molluscs","Type":"tinyint(4)","Null":"YES","Key":"","Default":null,"Extra":""}}', $values);
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
	/* @return int(11) $this->ProductsSourceId */
	public function getProductsSourceId() {
		return $this->ProductsSourceId;
	}
	/* @param int(11) $ProductsSourceId */
	public function setProductsSourceId($ProductsSourceId) {
		$this->ProductsSourceId = $ProductsSourceId;
	}
	/* @return double $this->Amount */
	public function getAmount() {
		return $this->Amount;
	}
	/* @param double $Amount */
	public function setAmount($Amount) {
		$this->Amount = $Amount;
	}
	/* @return int(11) $this->AmountTypeId */
	public function getAmountTypeId() {
		return $this->AmountTypeId;
	}
	/* @param int(11) $AmountTypeId */
	public function setAmountTypeId($AmountTypeId) {
		$this->AmountTypeId = $AmountTypeId;
	}
	/* @return double $this->Price */
	public function getPrice() {
		return $this->Price;
	}
	/* @param double $Price */
	public function setPrice($Price) {
		$this->Price = $Price;
	}
	/* @return datetime $this->LastSeen */
	public function getLastSeen() {
		return $this->LastSeen;
	}
	/* @param datetime $LastSeen */
	public function setLastSeen($LastSeen) {
		$this->LastSeen = $LastSeen;
	}
	/* @return double $this->Kj */
	public function getKj() {
		return $this->Kj;
	}
	/* @param double $Kj */
	public function setKj($Kj) {
		$this->Kj = $Kj;
	}
	/* @return double $this->NFat */
	public function getNFat() {
		return $this->NFat;
	}
	/* @param double $NFat */
	public function setNFat($NFat) {
		$this->NFat = $NFat;
	}
	/* @return double $this->NCarbs */
	public function getNCarbs() {
		return $this->NCarbs;
	}
	/* @param double $NCarbs */
	public function setNCarbs($NCarbs) {
		$this->NCarbs = $NCarbs;
	}
	/* @return double $this->NProtein */
	public function getNProtein() {
		return $this->NProtein;
	}
	/* @param double $NProtein */
	public function setNProtein($NProtein) {
		$this->NProtein = $NProtein;
	}
	/* @return double $this->NFiber */
	public function getNFiber() {
		return $this->NFiber;
	}
	/* @param double $NFiber */
	public function setNFiber($NFiber) {
		$this->NFiber = $NFiber;
	}
	/* @return double $this->NSalt */
	public function getNSalt() {
		return $this->NSalt;
	}
	/* @param double $NSalt */
	public function setNSalt($NSalt) {
		$this->NSalt = $NSalt;
	}
	/* @return double $this->NCalcium */
	public function getNCalcium() {
		return $this->NCalcium;
	}
	/* @param double $NCalcium */
	public function setNCalcium($NCalcium) {
		$this->NCalcium = $NCalcium;
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