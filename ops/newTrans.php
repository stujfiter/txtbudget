<?php
	session_start();
	
	include("../inc/dbconnect.inc");
	
	$accountID = $_SESSION['accountid'];
	$transactionDate = strtotime($_POST['transDate']);
	$description = $_POST['transDesc'];
	$budgetCategory = $_POST['budgetCategory'];
	$amount = $_POST['transAmount'];
	$deposit = $_POST['deposit'];
	
	if ($deposit != 'deposit'){
		$amount = -$amount;
	}
	
	$sql = "select categoryID ";
	$sql .= "from budgets ";
	$sql .= "where accountID = ".$accountID." ";
	$sql .= "	and description = '".$budgetCategory."' ";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$categoryID = $row['categoryID'];
	
	$sql = "INSERT INTO trans (accountID, transactionDate, entryDate, ";
	$sql .= "	description, budgetCategory, amount, reconciled) ";
	$sql .= "VALUES (".$accountID.",";
	$sql .= "	'".date("Y-m-d",$transactionDate)."', ";
	$sql .= "	'".date("Y-m-d h:i:s")."', ";
	$sql .= "	'".str_replace("'","''", $description)."', ";
	$sql .= "	'".$categoryID."', ";
	$sql .= "	".$amount.", ";
	$sql .= "	0)";
	
	//echo $sql;
	
	//echo "deposit:".$deposit;
	
	mysql_query($sql) or die ("Error: Could not add new transaction".$sql);
	
	header('Location: ../transactions.php');
	
	
?>