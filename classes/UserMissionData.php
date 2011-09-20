<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Utils.php");

class UserMissionData {
	
	private $user_id;
	private $mission_id;	
	private $times_complete;
	private $rank_one_times;
	private $rank_two_times;
	private $rank_three_times;
	private $curr_rank;
	
	function __construct() {
	}
	
	public static function getUserMissionData($userID, $missionID)
	{		
		$objData = ConnectionFactory::SelectRowAsClass("SELECT * FROM users_missions where user_id = ? and mission_id= ?",
		array($userID, $missionID), __CLASS__);
		return $objData;
	}
	
	public static function createUserMissionData($userID, $missionID) {
		$umdParams = array();
		$umdParams['user_id'] = $userID;
		$umdParams['mission_id'] = $missionID;
		$umdParams['times_complete'] = 1;
		$umdParams['rank_one_times'] = 1;
		$umdParams['curr_rank'] = 1;
		
		$justInsertID = ConnectionFactory::InsertIntoTableBasicReturnInsertID("users_missions", $umdParams);
		if ($justInsertID) {
			return self::getUserMissionData($userID, $missionID);
		}
		return NULL;
	}
	
	public function completeMission($mission) {
		$completeParams = array();
		$completeParams['times_complete'] = 1;
		$this->times_complete++;
				
		$cityRank = ConnectionFactory::SelectValue("rank_avail", "users_cities", 
			array("user_id" => $this->user_id, "city_id" => $mission->getCityID()));

		switch ($cityRank) {
			case 1:
				$completeParams['rank_one_times']=1;
				$this->rank_one_times++;
				break;
			case 2:
				$completeParams['rank_two_times']=1;
				$this->rank_two_times++;
				break;
			case 3:
				$completeParams['rank_three_times']=1;
				$this->rank_three_times++;
				break;
		}
		
		$currRank = $this->curr_rank;
		
		$userTimesFinishedRankForMission = $this->getRankTimes($currRank);
		if ($cityRank == $currRank) {	//this is cause we need to incorporate changes above
										//that have not hit db yet
			$userTimesFinishedRankForMission++;		
		}
		
		$missionRequirementToFinishRank = $mission->getRankReqTimes($currRank); 
		
		$unlockedMissionRank = false;
		if ($userTimesFinishedRankForMission >= $missionRequirementToFinishRank) {
			if ($userTimesFinishedRankForMission == $missionRequirementToFinishRank) {
				if ($currRank <= 3) {
					$_SESSION['justUnlockedThisMissionRank'] = $currRank + 1;
					$unlockedMissionRank = true;
					$completeParams['curr_rank']=1;
					$this->curr_rank++;
				} else {
					return;
				}
			}
		}
		
		$cityID = $mission->getCityID();
		
		$success = ConnectionFactory::updateTableRowRelativeBasic("users_missions", $completeParams, 
			array("user_id" => $this->user_id, "mission_id" => $mission->getID()));
		
		if ($unlockedMissionRank && $this->allMissionsInCityReadyForNextLevel($currRank+1, $cityID, $this->user_id)) {
			if(!ConnectionFactory::updateTableRowAbsoluteBasic("users_cities", array("rank_avail"=>$currRank+1), 
					array("user_id"=>$this->user_id, "city_id"=>$cityID))) {
				redirect($GLOBALS['serverRoot'] . "/errorpage.html");
			}
			$_SESSION['justUnlockedThisCityRank'] = $currRank+1;
		}		
	}
	
	private function allMissionsInCityReadyForNextLevel($nextLevel, $cityID, $userID) {
		$cityMissions = Mission::getMissionsInCity($cityID);
		$numCityMissions = count($cityMissions);
				
		$query = "SELECT * from users_missions WHERE user_id=? AND curr_rank=? AND (";
		
		$missionConditions = array();
		$values = array();
		array_push($values, $userID);
		array_push($values, $nextLevel);
		
		foreach($cityMissions as $mission) {
			array_push($missionConditions, "mission_id=?");
			$missionID = $mission->getID();
			array_push($values, $missionID);
		}	
		
		$query .= getArrayInString($missionConditions, ' OR ') . ")";
		$usersMissionsReadyInCity = ConnectionFactory::SelectRowsAsClasses($query, $values, __CLASS__);

		return count($usersMissionsReadyInCity) >= $numCityMissions;
	}
	
	public function getCurrRank() {
		return $this->curr_rank;
	}
	
	public function getRankTimes($rank) {
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
	
}	
?>