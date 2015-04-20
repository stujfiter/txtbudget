var req = getRequest();

function addBudgetItem(income, monthID) {

	var categoryCode;
	var description;
	var amount;
	var floatAmount;
	var income;
	
	if(income){
		categoryCode = document.getElementById("incomeCategoryCode").value.toUpperCase();
		description = document.getElementById("incomeDescription").value;
		amount = document.getElementById("incomeAmount").value;
		income = "true";		
	}
	else{
		categoryCode = document.getElementById("expenseCategoryCode").value.toUpperCase();
		description = document.getElementById("expenseDescription").value;
		amount = document.getElementById("expenseAmount").value;
		income = "false";
	}
	
	//validate the category code
	if(!categoryCode.match(/^\w{2}$/)){
			alert("Category Code must be two letters (A-Z)");
			return;
	}
	
	//validate the amount
	if(!amount.match(/^\$*[0-9\,]*(\.{1}[0-9]{2})?$/)){
		alert("Invalid Budget Amount ($9,999.99)");
		return;
	}
	floatAmount = parseFloat(amount);
	
	
	//Send the New Category to the Server
	var params = "action=create";
	params += "&categoryCode=" + categoryCode;
	params += "&description=" + description.replace(/&/g,"%26");
	params += "&amount=" + floatAmount;
	params += "&income=" + income;
	params += "&fmid=" + monthID;
	req.open("POST","./ops/budgetCategory_CRUD.php",true);
	req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	req.setRequestHeader("Content-length",params.length);
	req.setRequestHeader("Connection","close");		
	req.onreadystatechange = function ()
	{
		if(req.readyState == 4 && req.status == 200) {
			
			if(req.responseText != ""){
				alert(req.responseText);
				//document.write(req.responseText + "\n");
			}
			else {
				location.reload(true);
			}
		}
	};		
	req.send(params);
}

function deleteCategory(categoryCode, monthID){
	var myReq = getRequest();
	
	var params = "action=delete";
	params += "&categoryCode=" + categoryCode;
	params += "&monthID=" + monthID;
	myReq.open("POST","./ops/budgetCategory_CRUD.php",false);
	myReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	myReq.setRequestHeader("Content-length",params.length);
	myReq.setRequestHeader("Connection","close");
	myReq.send(params);
	if(myReq.responseText != ""){
		alert(myReq.responseText);
		//document.write(myReq.responseText);
	}
	else {
		location.reload(true);
	}
}

function nextMonth(monthID){
	var myReq = getRequest();
	
	var newMonthID = monthID + 1;
	var params = "fmid=" + newMonthID;
	myReq.open("POST","./ops/nextFiscalMonth.php",false);
	myReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	myReq.setRequestHeader("Content-length",params.length);
	myReq.setRequestHeader("Connection","close");
	myReq.send(params);
	if(myReq.responseText != "SUCCESS"){
		alert(myReq.responseText);
	}
	else {
		window.location.href = "./budget.php?fmid=" + newMonthID;
	}
}

function prevMonth(monthID){
	var prevMonth = monthID-1;
	window.location.href = "./budget.php?fmid=" + prevMonth;
}