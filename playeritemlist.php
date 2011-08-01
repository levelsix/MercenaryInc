<?php include("topmenu.php");
print "You currently have: ";
print "<br>";

mysql_connect($server, $user, $password);
@mysql_select_db($database) or die("Unable to select database");

$itemQuery = "SELECT * FROM users_items WHERE user_id = " . $_SESSION['userID'] . ";";
$itemResult = mysql_query($itemQuery);
$numRows = mysql_numrows($itemResult);

for ($i = 0; $i < $numRows; $i++) {
    $itemID = mysql_result($itemResult, $i, "item_id");
        
    $itemNameQuery = "SELECT name FROM items WHERE id = " . $itemID . ";";    
    $itemNameResult = mysql_query($itemNameQuery);
    
    $itemName = mysql_result($itemNameResult, 0, "name");
    $itemQuantity = mysql_result($itemResult, $i, "quantity");
    
    print $itemQuantity . " " . $itemName;
    print "<br>";
}

mysql_close();
?>