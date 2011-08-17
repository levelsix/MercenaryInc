<?php
include($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
$targetID = $_POST['targetID'];
?>
Enter your bounty amount:

NOTE: need to put error checking/validation to see if its a positive int <br>
and backend needs to check if user has enough money
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/addtobountylist.php' method='GET'>
<input type='text' name='bountyAmount'/>
<input type='hidden' name='targetID' value='<?php echo $targetID;?>'/>
<input type='submit' value='Place Bounty'/>
</form>
