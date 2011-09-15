<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Utils.php");

class Mission {
	
	private $id;
	private $name;	
	private $description;
	private $energy_cost;
	private $min_cash_gained;
	private $max_cash_gained;
	private $min_level;
	private $loot_item_id;
	private $chance_of_loot;
	private $exp_gained;
	private $rank_one_times;
	private $rank_two_times;
	private $rank_three_times;
	private $city_id;
	private $min_agency_size;
	
	function __construct() {
	}
	
	public static function getMission($missionID)
	{
		$objMission = ConnectionFactory::SelectRowAsClass("SELECT * FROM missions where id = :missionID", 
											array("missionID" => $missionID), __CLASS__);
		return $objMission;
	}
		
	public static function getMissionsInCity($cityID) {
		$query = "SELECT * from missions where city_id=?";
		
		$objMissions = ConnectionFactory::SelectRowsAsClasses($query, array($cityID), __CLASS__);
		return $objMissions;
	}
	
	public static function getMissionsInCityGivenPlayerLevel($playerLevel, $cityID) {
		$query = "SELECT * FROM missions WHERE min_level <= ? AND city_id = ? ORDER BY min_level";

		$objMissions = ConnectionFactory::SelectRowsAsClasses($query, array($playerLevel, $cityID), __CLASS__);
		return $objMissions;
	}
	
	public static function getMissionRequiredItemsIDsToQuantity($missionID) {
		$query = "SELECT missions_itemreqs.item_quantity, items.id FROM missions_itemreqs JOIN items ON " .
					"(missions_itemreqs.item_id = items.id) WHERE missions_itemreqs.mission_id = ?";
		$itemSth = ConnectionFactory::SelectAsStatementHandler($query, array($missionID));
	
		$itemIDsToQuantity = array();
		while ($row = $itemSth->fetch(PDO::FETCH_ASSOC)) {
			$itemID = $row["id"];
			$itemIDsToQuantity[$itemID] = $row["item_quantity"];
		}
	
		return $itemIDsToQuantity;
	}
		
	public function getName() {
		return $this->name;
	}
	
	public function getID() {
		return $this->id;
	}
	
	public function getMinAgencySize() {
		return $this->min_agency_size;
	}
	
	public function getEnergyCost() {
		return $this->energy_cost;
	}
	
	public function getChanceOfLoot() {
		return $this->chance_of_loot;
	}
	
	public function getLootItemID() {
		return $this->loot_item_id;
	}
	
	public function getMinCashGained() {
		return $this->min_cash_gained;
	}
	
	public function getMaxCashGained() {
		return $this->max_cash_gained;
	}
	
	public function getRandomCashGained() {
		return rand($this->min_cash_gained, $this->max_cash_gained);
	}
	
	public function getExpGained() {
		return $this->exp_gained;
	}
	
	public function getCityID() {
		return $this->city_id;
	}
	
	public function getRankReqTimes($rank) {
		switch($rank) {
			case 1:
				return $this->rank_one_times;
				break;
			case 2:
				return $this->rank_two_times;
				break;
			case 3:
				return $this->rank_three_times;
				break;
		}
	}
	
	public function getMinLevel() {
		return $this->min_level;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
}