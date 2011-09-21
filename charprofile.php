<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");


session_start();
$userID = $_SESSION['userID'];
$user = User::getUser($userID);
if (!$user) {
	// Redirect to error page. this isnt working. b/c theres text above?
	header("Location: $serverRoot/errorpage.html");
	exit;
}

if (isset($_POST['profileTab'])) {
	$_SESSION['profileTab'] = $_POST['profileTab'];
} else {
	$_SESSION['profileTab'] = 'info';
}

?>

<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/charprofile.php' method='POST'>
<input type='hidden' name='profileTab' value='info'/>
<input type='submit' value='Info'/>
</form>
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/charprofile.php' method='POST'>
<input type='hidden' name='profileTab' value='skills'/>
<input type='submit' value='Skills'/>
</form>
<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/charprofile.php' method='POST'>
<input type='hidden' name='profileTab' value='comments'/>
<input type='submit' value='Comments'/>
</form>

<?php
if (isset($_SESSION['profileTab'])) {
	if ($_SESSION['profileTab'] == 'skills') {
		include_once($_SERVER['DOCUMENT_ROOT'] . "/skills.php");
	}
	if ($_SESSION['profileTab'] == 'info') {
		$profileUser = $user;
		include_once($_SERVER['DOCUMENT_ROOT'] . "/userinfo.php");
	}
	if ($_SESSION['profileTab'] == 'comments') {
		$commentsUser = $user;
		include_once($_SERVER['DOCUMENT_ROOT'] . "/comments.php");
	}
	unset($_SESSION['profileTab']);
}


?>