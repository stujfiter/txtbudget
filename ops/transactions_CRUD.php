<?php

	session_start();
	
	if(!isset($_SESSION['uname'])){
		die("Session Expired!");
	}
	
	include("../inc/dbconnect.inc");
	
	
	if($_POST['action'] == "insert"){
		
		$amount = $_POST['transAmount'];
    	$deposit = $_POST['deposit'];
		
		if ($deposit != 'deposit'){
			$amount = -$amount;
		}
	
		$categoryID=$_POST['budgetCategory'];
	
		$sql = "INSERT INTO trans (accountID, transactionDate, entryDate, ";
		$sql .= "	description, budgetCategory, amount, reconciled) ";
		$sql .= "VALUES (".$_SESSION['accountid'].",";
		$sql .= "	'".date("Y-m-d",strtotime($_POST['transDate']))."', ";
		$sql .= "	CURRENT_TIMESTAMP, ";
		$sql .= "	'".str_replace("'","''", $_POST['transDescription'])."', ";
		
		if($categoryID == ""){
			$sql .= "NULL,";
		}
		else {
			$sql .= "'".str_replace("'","''",$categoryID)."',";
		}
		
		if($_POST['deposit'] == "1"){
			$sql .= "	".$_POST['transAmount'].", ";
		}
		else {
			$sql .= "	".-$_POST['transAmount'].", ";
		}
		
		$sql .= "	0)";
		
		mysql_query($sql) or die ("Could Not Insert New Transaction!");
		
		
	}
	else if ($_POST['action'] == "update"){
		
	
		$sql = "UPDATE trans ";
		$sql .= "SET amount=".$_POST['amount']." ";
		
		//update the budget category if it was sent in
		if (isset($_POST['budgetCategory'])){
			$categoryID = $_POST['budgetCategory'];
			if ($_POST['budgetCategory'] == "") {
				$sql .= ", budgetCategory = NULL ";
			} else {
				$sql .= ", budgetCategory = '".str_replace("'","''",$categoryID)."' ";
			}
		}		
		$sql .= "WHERE transid=".$_POST['transID']." ";
		$sql .= "	and accountID=".$_SESSION['accountid']." ";
		mysql_query($sql) or die("Could not update Transaction".$sql);
	}
	else {
		die("Invalid Operation: ".$_POST['action']);
	}
?>