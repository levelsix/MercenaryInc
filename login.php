<?php 
$id = $_POST['id'];
session_start();
$_SESSION['userID'] = $id;
header("Location: charhome.php");
exit;
?>