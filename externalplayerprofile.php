<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Item.php");

$userID = $_GET['userID'];
$profileUser = User::getUser($userID);
if (!$profileUser) {
	header("Location: $serverRoot/errorpage.html");
	exit;
}?>
<!--  Action buttons
Give option to attack, add to bounty list -->
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/attackplayer.php' method='POST'>
<input type='hidden' name='userID' value='<?php echo $userID;?>'/>
<input type='submit' value='Attack'/>
</form>

<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/addplayertobounty.php' method='POST'>
<input type='hidden' name='targetID' value='<?php echo $userID;?>'/>
<input type='submit' value='Add to Bounty List'/>
</form>

<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/userinfo.php");
?>