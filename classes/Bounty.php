<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ConnectionFactory.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Utils.php");


class Bounty {
	
	private $id;
	private $requester_id;
	private $target_id;
	private $payment;
	private $is_complete;
		
	function __construct() {
	}
	
	public static function getBounty($bountyID)
	{
		$objBounty = ConnectionFactory::SelectRowAsClass("SELECT * FROM bounties where id = :bountyID", 
											array("bountyID" => $bountyID), __CLASS__);
		return $objBounty;
	}
	
	public static function getBountiesForUser($userID) {
		$query = "SELECT * FROM bounties JOIN users ON (bounties.target_id = users.id) WHERE bounties.is_complete = 0 AND bounties.target_id != ?";
		
		$objBounties = ConnectionFactory::SelectRowsAsClasses($query, array($userID), __CLASS__);
		return $objBounties;
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
		
	public function getTargetID() {
		return $this->target_id;
	}
	
	public function getPayment() {
		return $this->payment;
	}
	
}