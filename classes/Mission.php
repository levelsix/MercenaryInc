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
	
	public function getName() {
		return $this->name;
	}
	
}