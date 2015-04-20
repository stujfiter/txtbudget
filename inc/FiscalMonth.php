<?php 

class FiscalMonth implements JsonSerializable {
	private $accountId;
	private $monthId;
	private $startDate;
	private $endDate;
	
	public function __toString() {
		return json_encode($this);
	}
	
	public function jsonSerialize()
	{
		return [
		'AccountId' => $this->accountId,
		'MonthId' => $this->monthId,
		'StartDate' => date('Y-m-d', $this->startDate),
		'EndDate' => date('Y-m-d', $this->endDate)
		];
	}
	
	public function getAccountId(){
		return $this->accountId;
	}
	
	public function setAccountId($value) {
		$this->accountId = $value;
	}
	
	public function getMonthId() {
		return $this->monthId;
	}
	
	public function setMonthId($value) {
		$this->monthId = $value;
	}
	
	public function getStartDate() {
		return $this->startDate;
	}
	
	public function setStartDate($value) {
		$this->startDate = $value;
	}
	
	public function getEndDate() {
		return $this->endDate;		
	}
	
	public function setEndDate($value) {
		$this->endDate = $value;
	}		
}

?>