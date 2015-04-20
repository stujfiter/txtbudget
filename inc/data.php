<?php
	include("dbconnect.inc");
	$sql = "SELECT uname ";
	$sql .= "FROM logins ";
	$sql .= "WHERE loginId = ".$_GET['loginId'];
	
	$result = mysql_query($sql) or die ("Could not Execute Query: ".$sql);
	if ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
		echo $row['uname'];
	}
	else {
		echo "loginId:".$_GET['loginId']." Not Found";
	}
?>