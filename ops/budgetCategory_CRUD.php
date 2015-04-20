<?php
	session_start();
	
	if(!isset($_SESSION['uname'])){
		die("Session Expired!");
	}
	
	include("../inc/dbconnect.inc");
	
	/*echo "Action: ".$_POST["action"]."\n";
	echo "New Category Item: ".$_POST["categoryCode"]."\n";
	echo "Description: ".$_POST["description"]."\n";
	echo "Amount: ".$_POST["amount"]."\n";
	echo "Income: ".$_POST["income"]."\n";*/
	
	if($_POST["action"] == "create") {
		$sql = "select categoryID ";
		$sql .= "from budgets ";
		$sql .= "where accountID = ".$_SESSION['accountid']." ";
		$sql .= "	and categoryID = '".$_POST["categoryCode"]."' ";
		$result = mysql_query($sql) or die("Could not Execute Query!");
	
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		if($row['categoryID'] != ""){
			die("Category ID Already Exists");
		}
	
		//echo("everything ok");
	
		$sql = "insert into budgets (accountID, categoryID, description, amount, income, monthID) ";
		$sql .= "select fm.accountID, ";
		$sql .= "	'".$_POST['categoryCode']."',";
		$sql .= "	'".str_replace("'","''", $_POST['description'])."', ";
		$sql .= "	".$_POST['amount'].",";		
		if($_POST['income'] == "true"){
			$sql .= "1,";
		}
		else {
			$sql .= "0,";
		}
		$sql .= "	fm.monthID ";
		$sql .= "from fiscalMonths fm ";
		$sql .= "where fm.monthID >= ".$_POST['fmid']." ";
		$sql .= "	and fm.accountID = ".$_SESSION['accountid']." ";
		
	
		mysql_query($sql) or die("Add Category Failed!");
	}
	elseif($_POST["action"] == "retrieve"){
		$sql = "select distinct B.categoryID, B.description ";
		$sql .= "from budgets B ";
		$sql .= "	inner join fiscalMonths FM on B.accountID = FM.accountID ";
		$sql .= "		and B.monthID = FM.monthID ";
		$sql .= "where B.accountID = ".$_SESSION['accountid']." ";
		$sql .= "	and FM.startDate <= '".date("Y-m-d",strtotime($_POST['transDate']))."' ";
		$sql .= "	and FM.endDate >= '".date("Y-m-d",strtotime($_POST['transDate']))."' ";
		$sql .= "order by B.income, B.description ";
		$result = mysql_query($sql) or die("Could Not Retrieve Budget Categories");
		
		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo "<options>\n";
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			echo "<option categoryID='".$row['categoryID']."'>".str_replace("&","&amp;",$row['description'])."</option>\n";
		}
		echo "</options>\n";
	}
	elseif($_GET["action"] == "retrieve"){
		$sql = "select distinct B.categoryID, B.description ";
		$sql .= "from budgets B ";
		$sql .= "	inner join fiscalMonths FM on B.accountID = FM.accountID ";
		$sql .= "		and B.monthID = FM.monthID ";
		$sql .= "where B.accountID = ".$_SESSION['accountid']." ";
		$sql .= "	and FM.startDate <= '".date("Y-m-d",strtotime($_GET['transDate']))."' ";
		$sql .= "	and FM.endDate >= '".date("Y-m-d",strtotime($_GET['transDate']))."' ";
		$sql .= "order by B.income, B.description ";
		$result = mysql_query($sql) or die("Could Not Retrieve Budget Categories");
		
		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo "<options>\n";
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			echo "<option categoryID='".$row['categoryID']."'>".str_replace("&","&amp;",$row['description'])."</option>\n";
		}
		echo "</options>\n";
	}
	elseif($_POST["action"] == "delete"){
	
		/*echo "Action: ".$_POST["action"]."\n";
		echo "Delete Category Item: ".$_POST["categoryCode"]."\n";*/
		
		$sql = "delete from budgets ";
		$sql .= "where accountID = ".$_SESSION['accountid']." ";
		$sql .= "	and categoryID = '".$_POST['categoryCode']."' ";		
		$sql .= " 	and monthID >= ".$_POST['monthID']." ";
		
		
		mysql_query($sql) or die("Delete Category Failed");
	}
	else {
		echo("Unknown Action\n ".$_POST["action"]);
		echo($_GET["action"]);
	}
?>
