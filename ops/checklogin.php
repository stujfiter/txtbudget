<?php
	include("../inc/dbconnect.inc");
	
	$uname = str_replace("'", "''", $_POST['userName']);
	$passwd = str_replace("'", "''", $_POST['passWord']);
	
	$sql = "SELECT accountid ";
	$sql .= "FROM logins ";
	$sql .= "WHERE uname = '".$uname."' ";
	$sql .= "	AND passwd = '".$passwd."'";
	$result = mysql_query($sql) or die("Cannot Execute Query<br>".$sql);
	
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	
	if ($row == "") {
		echo "Invalid Login";
	}
	else {
		echo $row['accountid'];
		
		session_start();
		$_SESSION['uname'] = $uname;
		$_SESSION['accountid'] = $row['accountid'];
	}
	
	
?>