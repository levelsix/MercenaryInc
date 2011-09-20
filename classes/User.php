<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
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
	private $diamonds;	
		

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
	
	public static function getAvailableCityIDsToRankAvail($userID) {
		$query = "SELECT * from users_cities WHERE rank_avail > 0 AND user_id =?";
		$citySth = ConnectionFactory::SelectAsStatementHandler($query, array($userID));
				
		$cityIDsToRankAvail = array();
		while ($row = $citySth->fetch(PDO::FETCH_ASSOC)) {
			$cityID = $row['city_id'];
			$cityIDsToRankAvail[$cityID] = $row['rank_avail'];
		}
		
		return $cityIDsToRankAvail;
	}
	
	public static function getUsers($userIDs) {
		if (count($userIDs) <= 0) {
			return array();
		}
		
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
	
	public static function getUsersItemsIDsToQuantity($userID) {
		$query = "SELECT users_items.quantity, items.id FROM users_items JOIN items ON " .
				"(users_items.item_id = items.id) WHERE users_items.user_id = ?";
		$itemSth = ConnectionFactory::SelectAsStatementHandler($query, array($userID));
		
		$itemIDsToQuantity = array();
		while ($row = $itemSth->fetch(PDO::FETCH_ASSOC)) {
			$itemID = $row["id"];
			$itemIDsToQuantity[$itemID] = $row["quantity"];
		}
	
		return $itemIDsToQuantity;
	}
	
	public static function getUsersRealEstateIDsToQuantity($userID) {
		$query = "SELECT users_realestates.quantity, realestate.id FROM users_realestates JOIN realestate ON " .
						"(users_realestates.realestate_id = realestate.id) WHERE users_realestates.user_id = ?";
		$reSth = ConnectionFactory::SelectAsStatementHandler($query, array($userID));
		
		$reIDsToQuantity = array();
		while ($row = $reSth->fetch(PDO::FETCH_ASSOC)) {
			$reID = $row["id"];
			$reIDsToQuantity[$reID] = $row["quantity"];
		}
		
		return $reIDsToQuantity;
	}
	
	
	public function getPendingAgencyInviteUsers() {
		$query = "SELECT * FROM agencies JOIN users ON (agencies.user_one_id = users.id) ";
		$query .= "WHERE agencies.user_two_id = ? AND agencies.accepted = 0";
		
		$objUsers = ConnectionFactory::SelectRowsAsClasses($query, array($this->id), __CLASS__);
		return $objUsers;
	}
	
	public function updateUserCash($cashChange) {
		$cashparams = array();
		$cashparams['cash'] = $cashChange;
	
		$conditions = array();
		$conditions['id'] = $this->id;

		$success = ConnectionFactory::updateTableRowRelativeBasic("users", $cashparams, $conditions);
		if ($success) {
			$this->cash += $cashChange;
		}
	}
	
	public function updateUserCashAndIncome($cashChange, $incomeChange) {
		$cashparams = array();
		$cashparams['cash'] = $cashChange;
		$cashparams['income'] = $incomeChange;
	
		$conditions = array();
		$conditions['id'] = $this->id;
		
		$success = ConnectionFactory::updateTableRowRelativeBasic("users", $cashparams, $conditions);
		if ($success) {
			$this->cash += $cashChange;
			$this->income += $incomeChange;
		}
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
	
	public function decrementUserItem($itemID, $quantity) {
		$itemParams = array();
		$itemParams['quantity'] = $quantity*-1;
		
		$conditions = array();
		$conditions['user_id'] = $this->id;
		$conditions['item_id'] = $itemID;
		$success = ConnectionFactory::updateTableRowRelativeBasic("users_items", $itemParams, $conditions);
		if ($success) {
			$success = ConnectionFactory::DeleteZeroAndBelowQuantity("users_items");				
		}
		return $success;
	}
	
	public function incrementUserItem($itemID, $quantity) {
		$itemParams = array();
		$itemParams['user_id'] = $this->id;
		$itemParams['item_id'] = $itemID;
		$itemParams['quantity'] = $quantity;
	
		//for this to work, need to modify appropriate tables to have unique constraint over two columns
		//http://www.w3schools.com/sql/sql_unique.asp		
		//although i think the two primary keys are doing it
		return ConnectionFactory::InsertOnDuplicateKeyUpdate("users_items", $itemParams, "quantity", $quantity);
	}
	
	public function decrementUserRealEstate($realEstateID, $quantity) {
		$realEstateParams = array();
		$realEstateParams['quantity'] = $quantity*-1;
		
		$conditions = array();
		$conditions['user_id'] = $this->id;
		$conditions['realestate_id'] = $realEstateID;
		$success = ConnectionFactory::updateTableRowRelativeBasic("users_realestates", $realEstateParams, $conditions);
		if ($success) {
			$success = ConnectionFactory::DeleteZeroAndBelowQuantity("users_realestates");
		}
		return $success;
	}
	
	public function incrementUserRealEstate($realEstateID, $quantity) {
		$realEstateParams = array();
		$realEstateParams['user_id'] = $this->id;
		$realEstateParams['realestate_id'] = $realEstateID;
		$realEstateParams['quantity'] = $quantity;
					
		//for this to work, need to modify appropriate tables to have unique constraint over two columns
		//http://www.w3schools.com/sql/sql_unique.asp
		//although i think the two primary keys are doing it
		return ConnectionFactory::InsertOnDuplicateKeyUpdate("users_realestates", $realEstateParams, "quantity", $quantity);
	}
	
	public function updateUserEnergyCashExpCompletedmissions($energyCost, $totalCashGained, $totalExpGained) {
		$missionCompleteParams = array();
		$missionCompleteParams['energy'] = $energyCost*-1;
		$missionCompleteParams['missions_completed'] = 1;
		$missionCompleteParams['cash'] = $totalCashGained;
		$missionCompleteParams['experience'] = $totalExpGained;
		
		$conditions = array();
		$conditions['id'] = $this->id;
		
		$success = ConnectionFactory::updateTableRowRelativeBasic("users", $missionCompleteParams, $conditions);		
		if ($success) {
			$this->cash += $cashLost;
			$this->missions_completed += 1;
			$this->energy -= $energyCost;
			$this->experience += $totalExpGained;	
		}
	}	
		
	/*TODO: change this to use LIMIT in the query and use mysql to randomize instead*/
	public function getPotentialOpponents() {
		$userID = $this->id;
		$level = $this->level;
		$agencySize = $this->agency_size;
				
		// get up to 10 people to list on the attack list
		$attackListSize = 10;
		
		$maxAgencySize = $agencySize + 5;
		$minAgencySize = max(array(1, $agencySize - 5));
		
		// temporary solution for level range
		$minLevel = $level - 5;
		$maxLevel = $level + 5;
		
		$query = "SELECT * FROM users WHERE level <= ? AND level >= ? AND agency_size <= ? AND agency_size >= ? AND id != ?";
		$objAllPotOpps = ConnectionFactory::SelectRowsAsClasses($query, array($maxLevel, $minLevel, $maxAgencySize, $minAgencySize, $userID), __CLASS__);
				
		if (!$objAllPotOpps || count($objAllPotOpps) < $attackListSize) {
			// TODO: execute further queries with higher level or agency size ranges if too few users
			//the next lines is temp solution if there is 1<x<attacklistsize opponents
			if ($objAllPotOpps) return $objAllPotOpps;
			else return array();
		}

		// get random indices
		$randomIntegers = getRandomIntegers($attackListSize, count($objAllPotOpps));
		
		$opponents = array();
		foreach ($randomIntegers as $key=>$value) {
			array_push($opponents, $objAllPotOpps[$key]);
		}
		return $opponents;
	}
	
	/*string based success codes*/
	public function invitePlayer($inviteeAgencyCode) {
		$userIDQuery = "SELECT id FROM users WHERE agency_code = ?";
		$userIDSth = ConnectionFactory::SelectAsStatementHandler($userIDQuery, array($inviteeAgencyCode));
		$userIDRow = $userIDSth->fetch(PDO::FETCH_ASSOC);
		if ($userIDRow) {
			$inviteeID = $userIDRow['id'];
			$success = ConnectionFactory::InsertIgnoreIntoTableBasic("agencies", array('user_one_id'=>$this->id, 
				'user_two_id'=>$inviteeID, 'accepted'=>0));
			if (!$success) {
				return "fail";
			} else {
				return "success";
			}
		} else {
			return "noUserWithAgencyCode";
		}
	}
	
	public function useSkillPoint($attribute) {
		
		$skillParams = array();
		if ($attribute == 'attack') {
			$skillParams['skill_points'] = -1;
			$skillParams['attack'] = 1;
		}
		if ($attribute == 'defense') {
			$skillParams['skill_points'] = -1;
			$skillParams['defense'] = 1;
		}
		if ($attribute == 'energymax') {
			$skillParams['skill_points'] = -1;
			$skillParams['energy_max'] = 1;
		}
		if ($attribute == 'healthmax') {
			$skillParams['skill_points'] = -1;
			$skillParams['health_max'] = 10;
		}
		if ($attribute == 'staminamax') {
			$skillParams['skill_points'] = -2;
			$skillParams['stamina_max'] = 1;
		}
		$conditions = array();
		$conditions['id'] = $this->id;
		
		$success = ConnectionFactory::updateTableRowRelativeBasic("users", $skillParams, $conditions);
		if ($success) {
			$this->skill_points -= $skillParams['skill_points'];
		}
		return $success;
	}
	
	public function updateLogin() {
		$params = array();
		$params['last_login'] = date('Y-m-d H:i:s');
		
		$conditions = array();
		$conditions['id'] = $this->id;
		
		$success = ConnectionFactory::updateTableRowAbsoluteBasic("users", $params, $conditions);
		if ($success) {
			$this->last_login = $params['last_login'];
		}
		return $success;
	}
	
	public function updateCashNumConsecLogin($cash, $numConsecDays) {
		$absParams = array();
		$absParams['num_consecutive_days_played'] = $numConsecDays;
		$absParams['last_login'] = date('Y-m-d H:i:s');
		
		$relParams = array();
		$relParams['cash'] = $cash;
		
		$conditions = array();
		$conditions['id'] = $this->id;
		
		if ($cash != 0) {
			$success = ConnectionFactory::updateTableRowGenericBasic("users", $absParams, $relParams, $conditions);
		} else {
			$success = ConnectionFactory::updateTableRowAbsoluteBasic("users", $absParams, $conditions);
		}
		if ($success) {
			$this->num_consecutive_days_played = $numConsecDays;
			$this->last_login = $absParams['last_login'];
			$this->cash += $cash;
		}
		return $success;
	}
	
	public function getDailyBonuses() {
		$query = "SELECT * FROM users_dailybonuses WHERE user_id = ?";
		$values = array($this->id);
		
		$result = ConnectionFactory::SelectRowAsAssociativeArray($query, $values);
		
		return $result;
	}
	
	public function updateDailyBonus($amount, $numConsecDays) {
		if ($numConsecDays < 6) {
			$params = array();
			$params['user_id'] = $this->id;
		
			$dayString = 'day' . ($numConsecDays + 1);
			$params[$dayString] = $amount;

			return ConnectionFactory::InsertOnDuplicateKeyUpdate("users_dailybonuses", $params, $dayString, $amount);
		} else {
			$params = array();
			for ($i = 1; $i <= 6; $i++)
				$params['day' . $i] = 0;
			
			$conditions = array();
			$conditions['user_id'] = $this->id;
			
			return ConnectionFactory::updateTableRowAbsoluteBasic("users_dailybonuses", $params, $conditions);
		}
	}
		
	public function acceptInvite($inviterID) {
		$conditions = array();
		$conditions['user_one_id'] = $inviterID;
		$conditions['user_two_id'] = $this->id;

		$success = ConnectionFactory::updateTableRowAbsoluteBasic("agencies", array('accepted'=>1), $conditions);

		if ($success) {
			return ConnectionFactory::updateTableRowsRelativeOnIDs("users", array('agency_size'=>1),
				array($this->id, $inviterID));
		}
	}
	
	public function rejectInvite($inviterID) {
		return ConnectionFactory::DeleteRowFromTable("agencies", array('user_one_id'=>$inviterID, 
				'user_two_id'=>$this->id));
	}
	
	// $healthAmt is a relative amount to increment/decrement current health by
	public function updateHealth($healthAmt) {
		$params = array();
		$params['health'] = $healthAmt;
		
		$conditions = array();
		$conditions['id'] = $this->id;
		
		$success = ConnectionFactory::updateTableRowRelativeBasic("user", $params, $conditions);
		
		if ($success) {
			$this->health += $healthAmt;
		}
		return $success;
	}
	
	public function updateHealthStaminaFightsExperience($healthAmt, $staminaAmt, $fightsWon, $fightsLost, $expAmt) {
		$params = array();
		$params['health'] = $healthAmt;
		$params['stamina'] = $staminaAmt;
		$params['fights_won'] = $fightsWon;
		$params['fights_lost'] = $fightsLost;
		$params['experience'] = $expAmt;
		
		$conditions = array();
		$conditions['id'] = $this->id;
		
		$success = ConnectionFactory::updateTableRowRelativeBasic("user", $params, $conditions);
		
		if ($success) {
			$this->health += $healthAmt;
			$this->stamina += $staminaAmt;
			$this->fights_won += $fightsWon;
			$this->fights_lost += $fightsLost;
			$this->experience += $expAmt;
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
	
	public function getLevel() {
		return $this->level;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function getNumMissionsCompleted() {
		return $this->missions_completed;
	}
	
	public function getFightsWon() {
		return $this->fights_won;
	}
	
	public function getFightsLost() {
		return $this->fights_lost;
	}
	
	public function getUserKills() {
		return $this->kills;
	}
	
	public function getUserDeaths() {
		return $this->deaths;
	}
	
	public function getIncome() {
		return $this->income;
	}
	
	public function getUpkeep() {
		return $this->upkeep;
	}
	
	public function getNetIncome() {
		return $this->income - $this->upkeep;
	}
	
	public function getAgencySize() {
		return $this->agency_size;
	}
	
	public function getEnergy() {
		return $this->energy;
	}
	
	public function getHealth() {
		return $this->health;
	}
	
	public function getStamina() {
		return $this->stamina;
	}
	
	public function getSkillPoints() {
		return $this->skill_points;
	}
	
	public function getAttack() {
		return $this->attack;
	}
	
	public function getDefense() {
		return $this->defense;
	}
	
	public function getExperience() {
		return $this->experience;
	}
	
	public function getStaminaMax() {
		return $this->stamina_max;
	}
	
	public function getHealthMax() {
		return $this->health_max;
	}
	
	public function getEnergyMax() {
		return $this->experience;
	}
	
	public function getAgencyCode() {
		return $this->agency_code;
	}
	
	public function getLastLogin() {
		return $this->last_login;
	}
	
	public function getNumConsecDaysPlayed() {
		return $this->num_consecutive_days_played;
	}

	public function getDiamonds() {
		return $this->diamonds;
	}
	
}