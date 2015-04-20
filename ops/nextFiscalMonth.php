<?php
	session_start();
	
	
		include("../inc/dbconnect.inc");
		
		if (isset($_SESSION['uname'])) {
			$uname = $_SESSION['uname'];
			$accountID = $_SESSION['accountid'];
		}
		
		else {
			header('Location: http://www.txtbudget.net');
		}
		
	//echo $_POST['fmid'];
	$sql = "SELECT monthID ";
	$sql .= "FROM fiscalMonths ";
	$sql .= "WHERE accountID = ".$_SESSION['accountid']." ";
	$sql .= "	and monthID = ".$_POST['fmid']." ";
	$result = mysql_query($sql) or die("Could not Execute Query");
	
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	if($row['monthID'] == ''){
		$prevMonthID = $_POST['fmid'] - 1;
		$sql = "INSERT INTO fiscalMonths (accountID, monthID, startDate, endDate) ";
		$sql .= "SELECT accountID, ".$_POST['fmid']." as monthID, ";
		$sql .= "	DATE_ADD(startDate, INTERVAL 1 MONTH) as startDate, ";
		$sql .= "	DATE_ADD(endDate, INTERVAL 1 MONTH) as endDate ";
		$sql .= "FROM fiscalMonths ";
		$sql .= "WHERE accountID = ".$_SESSION['accountid']." ";
		$sql .= "	and monthID =".$prevMonthID." ";
		//echo($sql);
		mysql_query($sql) or die ("could not execute query");
		
		$sql = "INSERT INTO budgets (accountID, monthID, categoryID, description, amount, income) ";
		$sql .= "SELECT accountID, monthID + 1 as monthID, categoryID, description, amount, income ";
		$sql .= "FROM budgets ";
		$sql .= "where accountID = ".$_SESSION['accountid']." ";
		$sql .= "	and monthID = ".$prevMonthID." ";
		//echo($sql);
		mysql_query($sql) or die ("Could not execute query");
	}
	
	echo "SUCCESS";
	

?>