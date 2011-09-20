<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
$targetID = $_POST['targetID'];

if (isset($_SESSION['notEnoughCashForBounty']) && $_SESSION['notEnoughCashForBounty']) {
	echo "you don't have enough cash to set a bounty that high";
	print "<br><br>";
	unset($_SESSION['notEnoughCashForBounty']);
}
?>
Enter your bounty amount:

<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/addtobountylist.php' onsubmit='return validateBountyAmount();'method='GET'>
<input type='text' name='bountyAmount' id='bountyAmount'/>
<input type='hidden' name='targetID' value='<?php echo $targetID;?>'/>
<input type='submit' value='Place Bounty'/>
</form>

<script>
 function validateBountyAmount() {
   var bounty = document.getElementById('bountyAmount').value;

  if (bounty.trim() == '') {
    alert('You did not enter a bounty.');
    return false;
	}

  if (isNaN(bounty.trim())) {
	    alert('That bounty is not a number.');
	    return false;
	  }
  if (bounty.trim() < 0) {
	    alert('You need a positive number bounty.');
	    return false;
	  }

   return true;
 }
</script>