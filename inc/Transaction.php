<?php 

/**
 * A plain old php object used to store the details of a transaction
 * 
 * @author kearl
 *
 */
class Transaction implements JsonSerializable
{
	private $ID;
	private $date;
	private $description;
	private $budgetCategory;	
	private $amount;
	private $balance;
	private $reconciled;
	
	public function __toString()
	{	
		return json_encode($this);
	}
	
	public function jsonSerialize()
	{
		return [
			'ID' => $this->ID,
			'Date' => $this->date,
			'Description' => $this->description,
			'BudgetCategory' => $this->budgetCategory,
			'Amount' => $this->amount,
			'Balance' => $this->balance,
			'Reconciled' => $this->reconciled
		];
	}
	
	/**
	 * Return the database ID of the Transaction object
	 * 
	 * @return integer
	 */
	public function getID()
	{
		return $this->ID;
	}
	
	/**
	 * Set the database ID of the Transaction object
	 * 
	 * @param integer $value
	 */
	public function setID($value)
	{
		$this->ID = $value;
	}
	
	public function getDate()
	{
		return $this->date;
	}
	
	public function setDate($value)
	{
		$this->date = $value;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	
	public function setDescription($value)
	{
		$this->description = $value;
	}
	
	public function getBudgetCategory()
	{
		return $this->budgetCategory;
	}
	
	public function setBudgetCategory($value)
	{
		$this->budgetCategory = $value;
	}
	
	public function getAmount()
	{
		return $this->amount;
	}
	
	public function setAmount($value)
	{
		$this->amount = $value;
	}
	
	public function getBalance()
	{
		return $this->balance;
	}
	
	public function setBalance($value)
	{
		$this->balance = $value;
	}
	
	public function getReconciled()
	{
		return $this->reconciled;
	}
	
	public function setReconciled($value)
	{
		$this->reconciled = $value;
	}
}

?>