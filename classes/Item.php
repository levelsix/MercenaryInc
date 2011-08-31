<?php
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
	
}