<?php
include("topmenu.php");
$targetID = $_GET['targetID'];
print "Enter your bounty amount: ";
print "<form action='backend/addtobountylist.php' method='GET'/>";
print "<input type='text' name='bountyAmount'/>";
print "<input type='hidden' name='targetID' value='" . $targetID . "'/>";
print "<input type='submit' value='Place Bounty'/>";
print "</form>";
?>