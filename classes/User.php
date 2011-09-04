<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Utils.php");

class User {
	
	private $id;
	private $name;
	private $level;
	private $type;
	private $attack;
	private $defense;
	private $bank_balance;
	private $cash;
	private $experience;
	private $stamina;
	private $energy;
	private $skill_points;
	private $health;
	private $missions_completed;
	private $fights_won;
	private $fights_lost;
	private $kills;
	private $deaths;
	private $income;
	private $upkeep;
	private $health_max;
	private $energy_max;
	private $stamina_max;
	private $agency_code;
	private $agency_size;
	private $last_login;
	private $num_consecutive_days_played;	
		

	function __construct() {
	}
	
	/*
	 * Returns a user
	 */
	public static function getUser($userID)
	{
		$objUser = ConnectionFactory::SelectRowAsClass("SELECT * FROM users where id = :userID", 
											array("userID" => $userID), __CLASS__);
		return $objUser;
	}
	
	/*
	 * Returns an array of users in agency. currently loops through statementhandler. better way?
	 */
	public static function getUsersInAgency($userID) {
		$agencySth = ConnectionFactory::SelectAsStatementHandler(
		"SELECT * FROM agencies WHERE (user_one_id = ? OR user_two_id = ?) AND accepted = 1", 
		array($userID, $userID));
		
		$agencySize = $agencySth->rowCount();
		$userIDs = array();
		while ($row = $agencySth->fetch(PDO::FETCH_ASSOC)) {
			$agentID = $row["user_one_id"];
			if ($agentID == $userID) $agentID = $row["user_two_id"];
			array_push($userIDs, $agentID);
		}
		
		return self::getUsers($userIDs);
	}
	
	public static function getUsers($userIDs) {
		$condclauses = array();
		$values = array();
		foreach($userIDs as $key=>$value) {
			array_push($condclauses, "id=?");
			array_push($values, $value); 		
		}
		$query = "SELECT * from users where ";
		$query .= getArrayInString($condclauses, ' OR ');
		
		$objUsers = ConnectionFactory::SelectRowsAsClasses($query, $values, __CLASS__);
		return $objUsers;		
	}
	
	public static function createUser($name) {
		$userparams = array();
		$userparams['name'] = $name;
		$justAddedID = ConnectionFactory::InsertIntoTableBasicReturnInsertID("users", $userparams);
		if ($justAddedID) {
			$usercitiesparams = array();
			$usercitiesparams['user_id'] = $justAddedID;
			$usercitiesparams['city_id'] = 1;
			$usercitiesparams['rank_avail'] = 1;
			$success = ConnectionFactory::InsertIntoTableBasic("users_cities", $usercitiesparams);
			if ($success) {
				return self::getUser($justAddedID);
			}
		}
		return NULL;
	}
	
	public function updateUserCash($cashChange) {
		$cashparams = array();
		$cashparams['cash'] = $cashChange;
	
		$conditions = array();
		$conditions['id'] = $this->id;

		return ConnectionFactory::updateTableRowRelativeBasic("users", $cashparams, $conditions);
	}
	
	public function depositBankDeductCash($cashLost, $bankGain) {		
		$bankparams = array();
		$bankparams['bank_balance'] = $bankGain;
		$bankparams['cash'] = $cashLost*-1;
		
		$conditions = array();
		$conditions['id'] = $this->id;
	
		$success = ConnectionFactory::updateTableRowRelativeBasic("users", $bankparams, $conditions);
		
		if ($success) {
			$this->cash -= $cashLost;
			$this->bank_balance += $bankGain;
		}
		return $success;
	}
	
	public function withdrawBankGainCash($cashGain) {
		$bankparams = array();
		$bankparams['bank_balance'] = $cashGain*-1;
		$bankparams['cash'] = $cashGain;
		
		$conditions = array();
		$conditions['id'] = $this->id;
		
		$success = ConnectionFactory::updateTableRowRelativeBasic("users", $bankparams, $conditions);
		
		if ($success) {
			$this->cash += $cashGain;
			$this->bank_balance -= $cashGain;
		}
		return $success;
	}
	
	public function getCash() {
		return $this->cash;
	}
	
	public function getID() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getBankBalance() {
		return $this->bank_balance;
	}
	
}