<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Utils.php");

class Item {
	
	private $id;
	private $name;
	private $type;
	private $atk_boost;
	private $def_boost;
	private $upkeep;
	private $min_level;
	private $price;
	private $chance_of_loss;
		
	function __construct() {
	}
	
	public static function getItem($itemID)
	{
		$objItem = ConnectionFactory::SelectRowAsClass("SELECT * FROM items where id = :itemID", 
											array("itemID" => $itemID), __CLASS__);
		return $objItem;
	}	
	
	
	public static function getItems($itemIDs) {
		if (count($itemIDs) <= 0) {
			return array();
		}
		
		$condclauses = array();
		$values = array();
		foreach($itemIDs as $key=>$value) {
			array_push($condclauses, "id=?");
			array_push($values, $value);
		}
		$query = "SELECT * from items where ";
		$query .= getArrayInString($condclauses, ' OR ');
		$objItems = ConnectionFactory::SelectRowsAsClasses($query, $values, __CLASS__);
			
		return $objItems;
	}
	
	public static function getItemIDsToItems($itemIDs) {
		$objItems = self::getItems($itemIDs);
		$toreturn = array();
		foreach ($objItems as $objItem) { 
			$itemID = $objItem->getID();
			$toreturn[$itemID] = $objItem;
		}
		return $toreturn;
	}
	
	/* should not be used because item objects do not encapsulate quantity by themselves
	public static function getItemsForUser($userID) {
		$query = "SELECT * FROM users_items WHERE user_id = ?";
		$objItems = ConnectionFactory::SelectRowsAsClasses($query, array($userID), __CLASS__);
		return $objItems;
	}*/
	
	public function getName(){
		return $this->name;
	}
	
	public function getID() {
		return $this->id;
	}
	
	public function getChanceOfLoss() {
		return $this->chance_of_loss;
	}
	
	
}