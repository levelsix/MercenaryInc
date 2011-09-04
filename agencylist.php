<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");
?>

<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/recruit.php' method='GET'>
<input type='submit' value='Back'/>
</form>

<?php 
session_start();
$agencyUsers = User::getUsersInAgency($_SESSION['userID']);

foreach($agencyUsers as $agencyUser) {
	?>
<form action='<?php  $_SERVER['DOCUMENT_ROOT'] ?>/externalplayerprofile.php' method='GET'>
<input type='hidden' name='userID' value='<?php echo $agencyUser->getID();?>'/>
<input type='submit' value='<?php echo $agencyUser->getName();?>'/>
</form>
	<?php
}

?>