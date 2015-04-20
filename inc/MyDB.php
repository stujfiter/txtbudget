<?php

include_once './inc/autoloader.php';

/**
 * txtbudget data access layer
 * 
 * The main txtbudget data access layer.  This class includes methods for all
 * database operations and is the only file that should include any SQL code
 * 
 * @author kearl
 *
 */
class MyDB
{
	
	private $db;
	
	/**
	 * Creates a new txtBudgetDB object and establishes a connection to the database
	 */
	public function __construct()
	{
		$this->db = new mysqli("127.0.0.1","txtbudgetadmin","changeit", "txtbudget");
			
		if ($this->db->connect_errno) {
			echo "Failed to connect to MySQL: " . $this->db->connect_error;
		}
	}
	
	public function __destruct()
	{
		$this->db->close();
	}
	
	public function getTransactions($limit, $accountID)
	{	
		$ret = array();
		
		$sql = "set @balance := 0; ";
		$this->db->query($sql);
		
		$sql = "select t2.transId, t2.transactionDate, t2.description ";
		$sql .= "	, t2.budgetCategory, t2.amount, t2.balance, t2.reconciled ";
		$sql .= "from ( ";
		$sql .= "	select t.transId, t.transactionDate, t.entryDate, t.Description ";
		$sql .= "		, t.budgetCategory, t.Amount, t.reconciled ";
		$sql .= "		, (@balance := @balance + t.Amount) as balance ";
		$sql .= "	from ( ";
		$sql .= "		select trans.transId, trans.transactionDate, trans.entryDate, trans.Description ";
		$sql .= "			, coalesce(b.description, '-') as budgetCategory ";
		$sql .= "			, trans.Amount, trans.reconciled ";
		$sql .= "		from trans ";
		$sql .= "			left outer join ( ";
		$sql .= "				select distinct categoryID, description ";
		$sql .= "				from budgets ";
		$sql .= "				where accountID = ?) b ";
		$sql .= "				on trans.budgetCategory = b.categoryID ";
		$sql .= "		where accountID = ? ";
		$sql .= "		order by trans.transactionDate, trans.entryDate) as t ";
		$sql .= "	) as t2 ";
		$sql .= "where 1=1 ";
		
		$orderBy = "order by t2.transactionDate desc, t2.entryDate desc ";
		
		// Apply the limit and transaction order
		if ($limit == 'unreconciled') {
			$sql .= "	and t2.reconciled = 0 ";
			$sql .= $orderBy;
		}
		elseif (preg_match('/[a-zA-Z]{3}\s[0-9]{4}/',$limit))
		{
			$month = $this->getFiscalMonths(
				(new DateTime($limit))->getTimeStamp(), 
				(new DateTime($limit))->getTimeStamp(), 
				$accountID
			)[0];
			
			$sql .= "	and t2.transactionDate >= '".date('Y-m-d', $month->getStartDate())."' ";
			$sql .= "	and t2.transactionDate <= '".date('Y-m-d', $month->getEndDate())."' ";
			$sql .= $orderBy;
		}
		else {
			$sql .= $orderBy;
			$sql .= "limit 50 ";
		}		
		
		if (!$stmt = $this->db->prepare($sql))
		{
			echo "Prepare failed: ". $this->db->errno . ") " . $this->db->error;
		}
		
		$stmt->bind_param("ii", $accountID, $accountID);
		$stmt->execute();
		$res = $stmt->get_result();
		
		while ($row = $res->fetch_assoc())
		{
			$myTrans = new Transaction;
			$myTrans->setID($row['transId']);
			$myTrans->setDate($row['transactionDate']);
			$myTrans->setDescription($row['description']);
			$myTrans->setBudgetCategory($row['budgetCategory']);
			$myTrans->setAmount($row['amount']);
			$myTrans->setBalance($row['balance']);
			$myTrans->setReconciled($row['reconciled']);
			
			$ret[] = $myTrans;
		}

		$res->free();
		
		return $ret;
	}
	
	public function getFiscalMonths($startDate, $endDate, $accountId){
		
		// Declare the return array
		$ret = array();
		
		// Setup the sql query
		$sql = "SELECT accountId, monthId ";
		$sql .= "	,UNIX_TIMESTAMP(startDate) as startDate ";
		$sql .= "	,UNIX_TIMESTAMP(endDate) as endDate ";
		$sql .= "FROM fiscalMonths ";
		$sql .= "WHERE accountID = ? ";
		$sql .= "	AND endDate >= ? ";
		$sql .= "	AND startDate <= ? ";
		$sql .= "ORDER BY startDate desc ";		
				
		// Prepare the parameterized query for execution
		if (!$stmt = $this->db->prepare($sql))
		{
			echo "Prepare failed: ". $this->db->errno . ") " . $this->db->error;
		}
		
		$sd = date('Y-m-d', $startDate); 
		$ed = date('Y-m-d', $endDate);	
		
		$stmt->bind_param("iss", $accountId
				, $sd
				, $ed);
		
		
		$stmt->execute();		
		$res = $stmt->get_result();
		
		while ($row = $res->fetch_assoc()){
			$myMonth = new FiscalMonth();
			$myMonth->setAccountId($row['accountId']);
			$myMonth->setMonthId($row['monthId']);
			$myMonth->setStartDate($row['startDate']);
			$myMonth->setEndDate($row['endDate']);
			
			$ret[] = $myMonth;
		}
		
		$res->free();
		
		return $ret;
				
	}

	public function getBankBalance($accountId)
	{
		$sql = "select sum(coalesce(reconciled * amount,0)) as bankBalance ";
		$sql .= "from trans ";
		$sql .= "where accountId = ? ";
		
		//Prepare the parameterized query for execution
		if (!$stmt = $this->db->prepare($sql))
		{
			echo "Prepare failed: ". $this->db->errno . ") " . $this->db->error;
		}
		
		$stmt->bind_param("i", $accountId);
		$stmt->execute();
		$res = $stmt->get_result();
		
		if ($row = $res->fetch_assoc())
		{
			$balance = $row['bankBalance'];
		}
		
		$res->free();
		
		return $balance;
		
	}
	
	public function getBudgetItems($fiscalMonth)
	{
		$ret = array();
		
		$sql = "select categoryID, description, amount, income ";
		$sql .= "from budgets ";
		$sql .= "where accountID = ? ";
		$sql .= "	and monthID = ? ";
		$sql .= "order by income, description ";
		
		// Prepare the parameterized query for execution
		if (!$stmt = $this->db->prepare($sql))
		{
			echo "Prepare failed: ". $this->db->errno . ") " . $this->db->error;
		}
		
		$accountId = $fiscalMonth->getAccountId();
		$monthId = $fiscalMonth->getMonthId();
		$stmt->bind_param("ii", $accountId, $monthId );
		$stmt->execute();
		$res = $stmt->get_result();
		
		while ($row = $res->fetch_assoc())
		{
			$myItem = new BudgetItem();
			$myItem->setCategoryId($row['categoryID']);
			$myItem->setDescription($row['description']);
			$myItem->setAmount($row['amount']);
			$myItem->setIncome($row['income']);
			
			$ret[] = $myItem;
		}
		
		$res->free();
		
		return $ret;
	}
	
	public function test()
	{
		$sql = "select * from logins where uname = ?";
		
		if (!$stmt = $this->db->prepare($sql))
		{
			echo "Prepare failed: ". $this->db->errno . ") " . $this->db->error;
		}
	}
}

?>
