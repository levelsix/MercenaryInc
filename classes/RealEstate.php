<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Utils.php");

class RealEstate {
	
	private $id;
	private $name;
	private $income;
	private $price;
	private $min_level;
		
	function __construct() {
	}
	
	public static function getRealEstate($realEstateID)
	{
		$objRE = ConnectionFactory::SelectRowAsClass("SELECT * FROM realestate where id = :realEstateID", 
											array("realEstateID" => $realEstateID), __CLASS__);
		return $objRE;
	}	
	
	public static function getRealEstates($reIDs) {
		if (count($reIDs) <= 0) {
			return array();
		}
		
		$condclauses = array();
		$values = array();
		foreach($reIDs as $key=>$value) {
			array_push($condclauses, "id=?");
			array_push($values, $value);
		}
		$query = "SELECT * from realestate where ";
		$query .= getArrayInString($condclauses, ' OR ');
		$objREs = ConnectionFactory::SelectRowsAsClasses($query, $values, __CLASS__);
			
		return $objREs;
	}
	
	
	public static function getRealEstateIDsToRealEstates($reIDs) {
		$objREs = self::getRealEstates($reIDs);
		$toreturn = array();
		foreach ($objREs as $objRE) { 
			$reID = $objRE->getID();
			$toreturn[$reID] = $objRE;
		}
		return $toreturn;
	}
	
	public static function getRealEstateIDsToRealEstatesVisibleInShop($playerLevel) {
		$query = "SELECT * FROM realestate WHERE min_level <= ? ORDER BY min_level";
		$objREs = ConnectionFactory::SelectRowsAsClasses($query, array($playerLevel+1), __CLASS__);
		$toreturn = array();
		foreach ($objREs as $objRE) { 
			$reID = $objRE->getID();
			$toreturn[$reID] = $objRE;
		}
		return $toreturn;
	}
	
	
	public function getName(){
		return $this->name;
	}
	
	public function getID() {
		return $this->id;
	}
		
	public function getMinLevel() {
		return $this->min_level;
	}
	
	public function getIncome() {
		return $this->income;
	}
	
	public function getPrice() {
		return $this->price;
	}
	
}