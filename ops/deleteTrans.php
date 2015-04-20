<?php
	session_start();
	
	include('../inc/dbconnect.inc');
	
	if (isset($_SESSION['uname']) && isset($_SESSION['accountid'])){
		//$sql = "select accountId from logins ";
		//$sql .= "where uname = '".$_POST['uname']."' ";
		//$sql .= "	and sessionID = '".$_POST['sessionId']."'";
	
		//echo $sql."<br>";
	
		//$result = mysql_query($sql) or die("Error in Query<br>".$sql);
	
		//$row = mysql_fetch_array($result, MYSQL_ASSOC);
	
		//if ($row['accountId'] != "") {
			$sql = "delete from trans ";
			$sql .= "where transId = ".$_POST['transId']." ";
			$sql .= "and accountId = ".$_SESSION['accountid']." ";
			
			//echo $sql;
		
			mysql_query($sql) or die("Error in Query<br>".$sql);
		
			//header("Location: ../transactions.php?uname=".$_POST['uname']."");
		//}
		//else{
		//	echo "Invalid Session";
		//}
	}
	else {
		echo "session variables not set";
	}
	
	
?>