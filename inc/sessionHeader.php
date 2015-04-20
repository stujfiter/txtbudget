<?php
 
// Start or restart the session so the timeout is reset.
session_start();

// Check to see if the session variables are already set
if (isset($_SESSION['uname'])) {
	$uname = $_SESSION['uname'];
	$accountID = $_SESSION['accountid'];
}
// If not, see if the username and accountid have been sent to the page
else {
	if (isset($_POST['uname'])){
		$uname = $_POST['uname'];
		$accountID = $_POST['accountid'];
		$_SESSION['uname'] = $uname;
		$_SESSION['accountid'] = $accountID;
	}
	// If not, the user has not logged in or the session has expired, redirect to home page
	else {
		header('Location: ./index.php');
	}
}

?>