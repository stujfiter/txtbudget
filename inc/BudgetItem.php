<?php 

class BudgetItem implements JsonSerializable
{
	private $categoryId;
	private $description;
	private $amount;
	private $income;
	
	public function __toString()
	{
		return json_encode($this);	
	}
	
	public function JsonSerialize()
	{
		return [
			'CategoryID' => $this->categoryId,
			'Description' => $this->description,
			'Amount' => $this->amount,
			'Income' => $this->income
		];
	}
	
	public function getCategoryId()
	{
		return $this->categoryId;
	}
	
	public function setCategoryId($value)
	{
		$this->categoryId = $value;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	
	public function setDescription($value)
	{
		$this->description = $value;
	}
	
	public function getAmount()
	{
		return $this->amount;
	}
	
	public function setAmount($value)
	{
		$this->amount = $value;
	}
	
	public function getIncome()
	{
		return $this->income;
	}
	
	public function setIncome($value)
	{
		$this->income = $value;
	}
}
?>