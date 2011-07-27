<?php 
$user="ccs108cchan91";
$password="azeodexu";
$database="c_cs108_cchan91";
$server="mysql-user-master.stanford.edu";
mysql_connect($server,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");

$query = 

  //if taken, echo "taken"
  //if not taken, create account, echo "success"
?>