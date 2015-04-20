<?php
	
	ini_set('display_errors',1);
	error_reporting(E_ALL);

	// Include the autoloader so that our object types get loaded dynamically
	include_once './inc/autoloader.php';
	
	// Setup the session
	include_once './inc/sessionHeader.php';	
	
	// Set the default number of transactions to show
	$ROWLIMIT = '50';
	
	// Get the limit from the URL parameter
	if (isset($_GET['limit'])){
		$limit = $_GET['limit'];
	}
	else {
		$limit = $ROWLIMIT;
	}
	
	// Create a new Data Access Layer object
	$data = new MyDB();
	
	// Get the 4 most recent fiscal months from the database
	$intervalStart = (new DateTime())->sub(new DateInterval('P3M'));
	$intervalEnd = new DateTime();		
	$fiscalMonths = $data->getFiscalMonths(
			$intervalStart->getTimeStamp(),
			$intervalEnd->getTimeStamp(),
			$accountID
	);

	// Get the current account balance
	$bankBalance = $data->getBankBalance($accountID);
	
	// Get the current month's budget categories
	if (count($fiscalMonths) > 0)
		$budgetItems = $data->getBudgetItems($fiscalMonths[0]);
	
	//Get the transactions
	$trans = $data->getTransactions($limit, $accountID);
	
?>

<html>
<head>
	<title>txtBudget.net - Transactions</title>
	
	<link rel="stylesheet" type="text/css" href="./css/default.css" />
	
	<script type="text/javascript" src="./javascript/jquery-2.0.0.min.js"></script>
	<script type="text/javascript" src="./javascript/ajax.js"></script>
	<script type="text/javascript" src="./javascript/transactions.js"></script>
	
</head>
<body>

	<div class='login'>
		Logged In As <?php echo "$uname "; ?>
		<a href='index.php'>Logout</a>
	</div>
	
	
	<?php include('./inc/menu.html'); ?>
	
	<center>
	<h1>txtBudget.net</h1>	
	
	<table cellpadding="0" cellspacing="0" border="0" style='text-align: center;' >
		<tr>
			<td width=16><img src="./img/tl_C1ECF1.gif" width="16" height="16" border="0" alt="..." /></td>
			<td bgcolor=#C1ECF1 ></td>
			<td width=16 height=16><img src="./img/tr_C1ECF1.gif" width="16" height="16" border="0" alt="..."/></td>
		</tr>
	
	<tr>
		<td width=16 bgcolor=#C1ECF1 ></td>
		<td bgcolor=#C1ECF1>
		
		<h2>Transactions</h2>
		
		This is where you enter in your bank account transactions and categorize them into your budget categories.<br />
		To add a new transaction, type it into the first row and click the "Add" button.<br/>
		<br/>
		
		<div id='ops-container'>
			&nbsp
			
			<span id='left-ops'>
				Show Only: 
				<select id="sellimit" >
				
				<?php			
					
					if ($limit == $ROWLIMIT){
						echo "<option selected='true' value='".$ROWLIMIT."'>Last ".$ROWLIMIT."</option>";
					}
					else{
						echo "<option value='".$ROWLIMIT."'>Last ".$ROWLIMIT."</option>";
					}
					
					if ($limit == 'unreconciled'){
						echo "<option selected='true' value='unreconciled'>Unreconciled</option>";
					}
					else {
						echo "<option value='unreconciled'>Unreconciled</option>";
					}					

					foreach ($fiscalMonths as $m)
					{
						// Ensure that the proper month name is selected for this fiscal month
						if (date('d', $m->getStartDate()) >= 16)
						{
							$monthName = date('M Y', $m->getEndDate());
						}
						else 
						{
							$monthName = date('M Y', $m->getStartDate());
						}
						
						if ($limit == $monthName)
						{
							echo "<option selected='true' value='".$monthName."'>".$monthName."</option>";
							$startDate = $m->getStartDate();
							$endDate = $m->getEndDate();
						}
						else 
						{
							echo "<option value='".$monthName."'>".$monthName."</option>";
						}
					}
					
					
					if ($limit == 'All Transactions') {
						echo "<option selected='true' value='All Transactions'>All Transactions</option>";
					}
					else {
						echo "<option value='All Transactions'>All Transactions</option>";
					}
								
				?>				
					
				</select>
			</span>
			
			<span id='right-ops'>			
				<?php
					//Show the current bank balance
					echo "<b style='font-size:120%;' >Bank Balance: $<span id='bankBalance'>"
						.number_format($bankBalance,2,'.','')
						."</span>";
				?>
			</span>
			
		</div>
		
		<p>
		
		<?php
			
			//Create the first row of the table
			echo "<table width=900>\n";
			echo "<tr class='header'>\n
				<th>Date (mm/dd/yyyy)</th>\n
				<th width='30%'>Description</th>\n
				<th>Budget Category</th>\n
				<th>Amount</th>\n
				<th width=10%>Balance</th>\n
				<th>Reconciled</th>\n
				<th>Action</th>\n
				</tr>\n";
			
			//Create the Insert New Row
			echo "<tr><td align='center'><input id='transDate' type='text' style='text-align:center;' maxlength='12' size=15></td>\n";
			echo "	<td><input id='transDescription' type='text' maxlength='50' size=30></td>\n";
			echo "	<td><select id='budgetCategory'>\n";
			echo "		<option value='Budget Category'>Budget Category</option>\n";
			
			if (isset($budgetItems)) 
			{
				foreach ($budgetItems as $bi)
				{
					echo "\t\t<option value='".$bi->getCategoryId()."' >".$bi->getDescription()."</option>\n";
				}
			}
			
			
			echo "</input></td>\n";
			echo "	<td align='right'><input id='transAmount' type='text' style='text-align:right;' maxlength='20' size=13></td>\n";
			echo "	<td colspan='2' align='center'>Deposit:<input id='deposit' type='checkbox' value='deposit' ></td>\n";
			echo "	<td align='center'><input id='Add_Button' type='button' value='Add' ></td></tr>\n";
			
			//create the rest of the table
			$rowCount = 0;
			foreach ($trans as $t)
			{
				$rowCount += 1;
				$transid = $t->getID();
				$transDate = date("m/d/Y",strtotime($t->getDate()));
				$formatAmount = number_format($t->getAmount(),2);
				
				echo "<span id='trans".$t->getID()."' >\n";
				if($rowCount%2==1){
					echo "<tr class='highlight'>\n";
				}
				else{
					echo "<tr>\n";
				}
				
				echo "<td align='center' id='TransDate".$transid."'>$transDate</td>\n
					<td>".$t->getDescription()."</td>\n
					<td id='category".$transid."' >".$t->getBudgetCategory()."</td>\n
					<td id='amount".$transid."' align='right'>\$".$formatAmount."</td>\n
					<td align='right'>\$".number_format($t->getBalance(),2)."</td>\n";
				
				if ($t->getReconciled() == 1) {
					echo "<td align='center'><input type='checkbox' id='cb".$transid."' class='reconciled_checkBox'
							checked='checked' value='checked' ></td>\n";
				}
				else {
					echo "<td align='center'><input type='checkbox' id='cb".$transid."' class='reconciled_checkBox'
							value='checked'></td>\n";
				}
				
				echo "<td align='center'>\n";
				echo "  <input id='EditSave".$transid."' class='EditSave_Button' type='image' 
						src='./img/b_edit.png' alt='-' />\n";
				
				echo "	<input id='Delete".$transid."' class='Delete_Button' type='image' 
						src='./img/b_drop.png' alt='-' />\n";
				
				echo "	<Input type='hidden' name='transid' value='".$transid."' />\n";
				echo "</td></tr>\n";
				echo "</span>\n";
			}
			
			echo "</table>\n";
		?>
		
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
</html>
