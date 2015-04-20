<?php
	session_start();
	
	
		include("./inc/dbconnect.inc");
		
		if (isset($_SESSION['uname'])) {
			$uname = $_SESSION['uname'];
			$accountID = $_SESSION['accountid'];
		}
		
		else {
			header('Location: http://www.txtbudget.net');
		}	
?>

<html>
<head>
	<title>txtBudget.net - Budget Editor</title>
	<script type="text/javascript" src="./inc/ajax.js" ></script>
	<script type="text/javascript" src="./javascript/budget.js" ></script>
	<link rel="stylesheet" type="text/css" href="./css/default.css" />
</head>
<body>

	<div class='login'>
		Logged In As <?php echo "$uname "; ?>
		<a href='index.php'>Logout</a>
	</div>
	
	
	<?php include('./inc/menu.html'); ?>
	
	<center>
	<h1>txtBudget.net</h1>
	
	
	
	<?php
		$sql = "select monthID, UNIX_TIMESTAMP(startDate) as startDate, UNIX_TIMESTAMP(endDate) as endDate ";
		$sql .= "from fiscalMonths ";
		$sql .= "where accountID = ".$_SESSION['accountid']." ";
		if (isset($_GET['fmid'])) {
			$sql .= "	and monthID = ".$_GET['fmid']." ";
		}
		else {
			$sql .= "	and startDate <= '".date('Y-m-d')."' ";
			$sql .= "	and endDate >= '".date('Y-m-d')."' ";
		}
		$result = mysql_query($sql) or die("Could Not Execute Query ");
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		
		if ($row['monthID'] == '') {
			$sql = "select monthID, UNIX_TIMESTAMP(startDate) as startDate, UNIX_TIMESTAMP(endDate) as endDate ";
			$sql .= "from fiscalMonths ";
			$sql .= "where accountID = ".$_SESSION['accountid']." ";
			$sql .= "	and monthID = ( ";
			$sql .= "		select max(monthID) ";
			$sql .= "		from fiscalMonths ";
			$sql .= "		where accountID = ".$_SESSION['accountid'].") ";
			$result = mysql_query($sql) or die("Could Not Execute Query " + sql);
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
		}
		
		if ((int) date('d',$row['startDate']) >= 16) {
			$budgetDate = date('F Y', mktime(0,0,0,date('m', $row['startDate'])+1,date('d',$row['startDate']),date('Y',$row['startDate'])));
		}
		else {
			$budgetDate = date('F Y', $row['startDate']);
		}		
		
	?>
	
	
	
	<div class="tablediv">
	
	<?php
		$varMonthID = $row['monthID'];
		
		echo "<h2><!-- <p style='text-align: center;' > -->";
		if ($row['monthID'] > 1){
			echo "<a class='monthChange' onclick='prevMonth(".$row['monthID'].");' >&lt&lt </a>";
		}		
		echo $budgetDate;
		if ($row['endDate'] < mktime(0,0,0,date("m")+1,date("d"),date("Y"))) {
			echo "<a class='monthChange' onclick='nextMonth(".$row['monthID'].");' > &gt&gt</a>";
		}
		echo "<!--</p>--></h2>\n";
	?>
	
	<!-- This is the upper left piece of the rounded corner background -->
	<table cellpadding="0" cellspacing="0" border="0" style='text-align: center;' >
	<tr>
		<td width=16><img src="./img/tl_C1ECF1.gif" width="16" height="16" border="0" alt="..." /></td>
		<td bgcolor=#C1ECF1 ></td>
		<td width=16 height=16><img src="./img/tr_C1ECF1.gif" width="16" height="16" border="0" alt="..."/></td>
	</tr>
	<tr>
		<td width=16 bgcolor=#C1ECF1 ></td>
		<td bgcolor=#C1ECF1>
	<!-- Now all the content -->	
		
	<h3>Income</h3>	
	This is a list of all your sources of income each month.  To add a new item, type it into the first<br>
	row and click the "Add" button.<br>	
	<?php
		//Display the Income Table
		
		/*$sql = "select * ";
		$sql .= "from budgets ";
		$sql .= "where accountID = ".$_SESSION['accountid']." ";
		$sql .= "	and income = 1 "; 
		$sql .= "order by description";*/
		$sql = "SELECT b.categoryID, b.description, b.amount, b.income, b.monthID, fm.startDate, ";
        $sql .= "	abs(sum(round(coalesce(t.amount,0),2))) as transAmount ";
		$sql .= "FROM budgets b ";
   		$sql .= "	inner join fiscalMonths fm on b.accountID = fm.accountID ";
     	$sql .= " 		and b.monthID = fm.monthID ";
   		$sql .= "	left outer join trans t on b.accountID = t.accountID ";
      	$sql .= "		and b.categoryID = t.budgetCategory ";
        $sql .= "		and t.transactionDate >= fm.startDate ";
        $sql .= "		and transactionDate < DATE_ADD( fm.endDate, INTERVAL 1 DAY ) ";
		$sql .= "WHERE b.accountID = ".$_SESSION['accountid']." ";
		if(isset($_GET['fmid'])){
			$sql .= "	and fm.monthID = ".$_GET['fmid']." ";
		}
		else {		
			$sql .= "	and fm.monthID = ( ";
			$sql .= "		select monthID ";
			$sql .= "		from fiscalMonths ";
			$sql .= "		where accountID = ".$_SESSION['accountid']." ";
			$sql .= "			and startDate <= '".date("Y-m-d")."' ";
			$sql .= "			and endDate >= '".date("Y-m-d")."') ";
        }
        $sql .= "	and b.income = 1 ";
		$sql .= "group by b.categoryID, b.description, b.amount, b.income, b.monthID, fm.startDate ";
		
		$result = mysql_query($sql)
			or die("Could Not Execute Query".$sql);
		
		echo "<table border='0' width=700>\n";
		echo "<tr class='header'><th align='center'>Category Code</th>";
		echo "	<th align='center' width='40%'>Description</th>";
		echo "	<th align='center'>Budget Amount</th>\n";
		echo "	<th align='center'>Actual Income</th>\n";
		echo "	<th align='center'>Action</th></tr>\n";
		
		//add the row for inserting new income items
		echo "<tr><td align='center'>\n";
		echo "\t<input id='incomeCategoryCode' style='text-align:center; text-transform:uppercase' type='text' maxlength=2 size=5 /></td>\n";
		echo "\t<td width='40%'><input id='incomeDescription' type='text' maxlength=50 size=50 /></td>\n";
		echo "\t<td align='right'><input id='incomeAmount' type='text' style='text-align:right' maxlength=20 size=12 /></td>\n";
		echo "\t<td></td>\n";
		echo "\t<td><input type='button' value='Add' onclick='addBudgetItem(true,".$varMonthID.");' /></td>\n";
		
		$rowCount = 0;
		$incomeTotal = 0;
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)){		
			$rowCount = $rowCount + 1;
			if($rowCount%2==1){
				echo "<tr class='highlight'>\n";
			}
			else {
				echo "<tr>\n";
			}
			
			echo "\t<td align='center'>".$row['categoryID']."</td>\n";
			echo "\t<td width='40%'>".$row['description']."</td>\n";
			echo "\t<td align='right'>$".number_format($row['amount'],2)."</td>\n";
			echo "\t<td align='right'>$".number_format($row['transAmount'],2)."</td>\n";
			echo "\t<td align='center'><input type='image' src='./img/b_drop.png' 
				onclick='deleteCategory(\"".$row['categoryID']."\",".$row['monthID'].");' ></td>\n";
			echo "</tr>\n";
			
			$incomeTotal += $row['amount'];
		}
		echo "<tr><td></td><td align='right'><b>Total Monthly Income:</b></td>\n";
		echo "\t<td align='right'><b>$".number_format($incomeTotal,2)."</b></td><td></td><td></td></tr>\n";
		
		echo "</table>\n";
		
	?>
	</div>
	
	<h3>Expenditures</h3>
	This is a list of all your estimated expenses each month.  To add a new item, type it into the first<br>
	row and click the "Add" button.
	
	<div class="tablediv">
	<?php	
		//Display the Expenditures Table
		
		/*$sql = "select * ";
		$sql .= "from budgets ";
		$sql .= "where accountID = ".$_SESSION['accountid']." ";
		$sql .= "	and income = 0 "; 
		$sql .= "order by description";*/
		$sql = "SELECT b.categoryID, b.description, b.amount, b.income, b.monthID, ";
        $sql .= "	abs(sum(round(coalesce(t.amount,0),2))) as transAmount ";
		$sql .= "FROM budgets b ";
   		$sql .= "	inner join fiscalMonths fm on b.accountID = fm.accountID ";
     	$sql .= " 		and b.monthID = fm.monthID ";
   		$sql .= "	left outer join trans t on b.accountID = t.accountID ";
      	$sql .= "		and b.categoryID = t.budgetCategory ";
        $sql .= "		and t.transactionDate >= fm.startDate ";
        $sql .= "		and transactionDate < DATE_ADD( fm.endDate, INTERVAL 1 DAY ) ";
		$sql .= "WHERE b.accountID = ".$_SESSION['accountid']." ";
   		if(isset($_GET['fmid'])){
			$sql .= "	and fm.monthID = ".$_GET['fmid']." ";
		}
		else {		
			$sql .= "	and fm.monthID = ( ";
			$sql .= "		select monthID ";
			$sql .= "		from fiscalMonths ";
			$sql .= "		where accountID = ".$_SESSION['accountid']." ";
			$sql .= "			and startDate <= '".date("Y-m-d")."' ";
			$sql .= "			and endDate >= '".date("Y-m-d")."') ";
        }
        $sql .= "	and b.income = 0 ";
		$sql .= "group by b.categoryID, b.description, b.amount, b.income, b.monthID ";
		
		//echo $sql;
		$result = mysql_query($sql)
			or die("Could Not Execute Query");
		
		echo "<table width=700>\n";
		echo "<tr class='header'><th align='center'>Category Code</th>";
		echo "	<th width='40%' >Description</th>";
		echo "	<th align='center'>Budget Amount</th>\n";
		echo "	<th align='center'>Actual Expense</th>\n";
		echo "	<th align='center'>Action</th></tr>\n";
		
		//place the new expenditures row
		echo "<td align='center'><input id='expenseCategoryCode' type='text' style='text-align:center; text-transform:uppercase' maxlength=2 size=5></td>\n";
		echo "<td><input id='expenseDescription' type='text' maxlength=50 size=50 /></td>\n";
		echo "<td align='right'><input id='expenseAmount' type='text' style='text-align:right' maxlength=20 size=12></td>\n";
		echo "<td></td>\n";
		echo "<td><input type='button' value='Add' onclick='addBudgetItem(false,".$varMonthID.");' /></td></tr>\n";

		$rowCount = 0;
		$expensesTotal = 0;
		$expensesActual = 0;
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
			$rowCount = $rowCount + 1;
			if($rowCount%2==1){
				echo "<tr class='highlight'>\n";
			}
			else {
				echo "<tr>\n";
			}
			echo "\t<td align='center'>".$row['categoryID']."</td>\n";
			echo "\t<td>".$row['description']."</td>\n";
			echo "\t<td align='right'>$".number_format($row['amount'],2)."</td>\n";
			if($row['transAmount'] > $row['amount']){
				echo "\t<td align='right'><font color='FF0000'>$".number_format($row['transAmount'],2)."</font></td>\n";
			}
			else {
				echo "\t<td align='right'>$".number_format($row['transAmount'],2)."</td>\n";
			}
			
			echo "\t<td align='center'><input type='image' src='./img/b_drop.png' 
					onclick='deleteCategory(\"".$row['categoryID']."\",".$row['monthID'].");' /></td>\n";
			echo "</tr>\n";
			$expensesTotal += $row['amount'];
			$expensesActual += $row['transAmount'];
		}
		echo "<tr><td></td><td align='right'><b>Total Monthly Expenses:</b></td>\n";
		echo "\t<td align='right'><b>$".number_format($expensesTotal,2)."</b></td>\n";
		echo "\t<td align='right'><b>$".number_format($expensesActual,2)."</b></td>\n";
		echo "\t<td></td></tr>\n";
		
		echo "</table>\n";
		
		
	?>
	</div>
	
	</td>
		<td width=16 bgcolor=#C1ECF1 ></td>
	</tr>
	<tr>
		<td width=16><img src="./img/bl_C1ECF1.gif" width="16" height="16" border="0" alt="..." /></td>
		<td bgcolor=#C1ECF1 ></td>
		<td width=16><img src="./img/br_C1ECF1.gif" width="16" height="16" border="0" alt="..." /></td>
	</tr>
	<table>
	
	</center>
</body>