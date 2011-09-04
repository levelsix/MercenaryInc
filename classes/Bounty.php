<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Utils.php");


class Bounty {
	
	private $id;
	private $requester_id;
	private $target_id;
	private $payment;
	private $is_complete;
		
	function __construct($userID, $targetID, $payment) {
		$this->requester_id = $userID;
		$this->target_id = $targetID;
		$this->payment = $payment;
	}
	
	public static function getBounty($bountyID)
	{
		$objBounty = ConnectionFactory::SelectRowAsClass("SELECT * FROM bounties where id = :bountyID", 
											array("bountyID" => $bountyID), __CLASS__);
		return $objBounty;
	}

	public static function createBounty($requester_id, $target_id, $payment) {
		$bountyparams = array();
		$bountyparams['requester_id'] = $requester_id;
		$bountyparams['target_id'] = $target_id;
		$bountyparams['payment'] = $payment;
		$justInsertID = ConnectionFactory::InsertIntoTableBasicReturnInsertID("bounties", $bountyparams);
		if ($justInsertID) {
			return self::getBounty($justInsertID);
		}
		return NULL;
	}
	
}