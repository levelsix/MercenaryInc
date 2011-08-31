<?php

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
	
	public function createBounty() {
		$bountyparams = array();
		$bountyparams['requester_id'] = $this->requester_id;
		$bountyparams['target_id'] = $this->target_id;
		$bountyparams['payment'] = $this->payment;
		return ConnectionFactory::InsertIntoTableBasic("bounties", $bountyparams);
	}
	
}