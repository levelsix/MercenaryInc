<?php 
$id = $_POST['id'];
session_start();
$_SESSION['userid'] = $id;
header("Location: charhome.php");
exit;
?>