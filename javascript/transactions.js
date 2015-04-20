var myReq;
var editing = false;

/**
 * Initialize the DOM Events
 */
$().ready( function () {
	
	// Set up the change event for the limit selection box
	$('#sellimit').change( function () {
		window.location = "transactions.php?limit=" + $(this).val();
	});
	
	// Setup the click event for the add transaction button
	$('#Add_Button').click(function () {
		// Prevent the user from clicking add twice on a slow response.
		$(this).prop('disabled','disabled');
		
		addTransaction();
	});
	
	// Setup the onClick event of the transaction reconciled checkboxes
	$('.reconciled_checkBox').click(function () {
		// Get the transaction id from the element id
		var myreg = new RegExp(/^cb([0-9]+)$/);
		var transid = $(this).attr('id').match(myreg)[1];
		
		setReconciled(transid, getTransactionAmount(transid));
	});
	
	// Setup the onClick event of the edit transaction icon
	$('.EditSave_Button').click(function () {
		
		//Get the transaction id from the element id
		var myreg = new RegExp(/^EditSave([0-9]+)$/);
		var transid = $(this).attr('id').match(myreg)[1];
		
		editTransaction(transid);
	});
	
	// Setup the onClick event of the delete transaction icon
	$('.Delete_Button').click(function() {
		
		//Get the transaction id from the element id
		var myreg = new RegExp(/^Delete([0-9]+)$/);
		var transid = $(this).attr('id').match(myreg)[1];
		
		deleteTransaction(transid, getTransactionAmount(transid));		
	});
});

/**
 * Gets the transaction amount from the DOM based on a transaction id
 * 
 * @param transid
 * @returns transaction amount for transaction with id=transid
 */
function getTransactionAmount(transid){
	// Get the amount of the transaction
	var amountString = $('#amount'+transid).html();
	var amount = Number(amountString.replace(/[^0-9\.\-]+/g,""));
	return amount;
}

/**
 * Deletes a transaction from the database and reload the page
 * 
 * @param transID
 * @param amount
 */
function deleteTransaction(transID,amount){
	
	// Do nothing if the user is currently editing the transaction
	if (editing) {return;}
	
	// Setup the ajax parameters
	var params = "transId=" + transID ;
	
	// Send the ajax call to delete the transaction
	$.ajax({
		type: 'POST',
		url: './ops/deleteTrans.php',
		data: params,
		async: true
	})
	.done(function (msg) {
		window.location = window.location.href;
	})
	.fail(function (jqXHR, msg) {
		alert('Error: Could not delete transaction.');
	});
}

function myCallBack(){
	if (myReq.readyState == 4){
		window.location = window.location.href;		
	}
}

/**
 * Sets a transaction as reconciled in the database with no page reload.
 * @param transID
 * @param amount
 */
function setReconciled(transID,amount){
	
	// Set the checkbox back to the way it was if the user is editing a transaction
	if (editing) {
		$('#cb'+transID).prop('checked',!$('#cb'+transID).is(':checked'));
		return;
	}
	
	// Update the bank balance
	var bankBalance = Number($('#bankBalance').html().replace(/[^0-9\.\-]+/g,""));
	var newBalance;	
	if ($('#cb'+transID).is(':checked')){
		newBalance = bankBalance + amount;				
	}
	else {
		newBalance = bankBalance - amount;
	}
	$('#bankBalance').html(newBalance.toFixed(2));
	
	// Send the ajax request to reconcile the transaction
	var params = "transId=" + transID ;
	$.ajax({
		type: 'POST',
		url: './ops/reconcile.php',
		data: params,
		async: true
	})
	.fail(function (jqXHR, msg) {
		alert('Error: Could not reconcile transaction.');
	});	
}

/**
 * Adds the user entered transaction to the database
 */
function addTransaction(){
	// do nothing if the user is editing a transaction
	if (editing) {return;}	
	
	//validate the transaction date
	var transDate = $('#transDate').val();
	if (!transDate.match(/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}/) || !isDate(transDate)){
		alert("Invalid Date");
		$('#Add_Button').prop('disabled',false);
		return;
	}
	
	// Validate the amount
	var transAmount = $('#transAmount').val().replace('$','').replace(',','');
	if(!validateAmount(transAmount)){
		alert("Invalid Amount ([+/-]$9,999.99)");
		$('#Add_Button').prop('disabled',false);
		return;
	}
	
	// Initialize the parameter string
	var params = "action=insert";
	params += "&transDate=" + encodeURIComponent(transDate);
	
	// Set the description parameter and strip out any ampersands
	params += "&transDescription=" + encodeURIComponent($('#transDescription').val());
	
	// Set he Budget Category parameter and strip out any ampersands
	var budgetCategory = encodeURIComponent($('#budgetCategory').val());
	if(budgetCategory == "Budget Category"){
		params += "&budgetCategory=";
	}
	else{
		params += "&budgetCategory=" + budgetCategory;
	}
	
	// Set the Amount parameter		
	var floatAmount = parseFloat(transAmount);
	params += "&transAmount=" + floatAmount;
	
	// Set the Deposit parameter
	if($('#deposit').is(':checked')){
		params += "&deposit=1";
	}
	else{
		params += "&deposit=0";
	}
	
	// Send the ajax to add the transaction and reload the page on success.
	$.ajax({
		type: 'POST',
		url: './ops/transactions_CRUD.php',
		data: params,
		async: true
	})
	.done(function () {
		location.reload(true);
	})
	.fail(function (jqXHR, msg) {
		alert('Error: Could not add transaction.');
	});	
}

/**
 * Verifies that amountString is a valid parsable transaction amount.
 * @param amountString string
 * @returns {Boolean}
 */
function validateAmount (amountString){
	var result = false;
	if(amountString.match(/^[\+\-]?[0-9]*(\.{1}[0-9]{1,2})?$/) && !isNaN(parseFloat(amountString))){
		result = true;
	}
	
	return result;
}

/**
 * Updates the UI to allow the user to edit the transaction
 * @param transID
 */
function editTransaction (transID) {
	// Do nothing if the user is editing the transaction
	if (editing) {return;}
	
	//Change the Amount Field to a text box
	var elAmount = $('#amount' + transID);	
	var amount = parseFloat(elAmount.html().replace("$","").replace(",",""));	
	var amountBox = "<input id='amountText' type='text' value='$" + amount.toFixed(2) + "' ";
	amountBox += "style='text-align:right;' maxlength='20' size=13 />";	
	elAmount.html(amountBox);
	
	//get the category list based on the transaction date
	var transDate = $('#TransDate' + transID).html();
	var elCategory = $('#category' + transID);
	var transCategory = elCategory.html();
	var params = "action=retrieve&transDate=" + transDate;
	
	$.ajax({
		type: 'POST',
		url: './ops/budgetCategory_CRUD.php',
		data: params,
		async: true
	}).done (function (data){
		displayBudgetCategories(transCategory, elCategory, data)
	});
	
	//Update the EditSave Onclick event and image
	var elEditSave = $('#EditSave' + transID);
	elEditSave.prop('src', './img/b_save.png');
	elEditSave.off('click');
	elEditSave.click(function () { 
		saveTransaction(transID); 
	});
	
	editing = true;
}

/**
 * Replaces the html of jquery DOM element categoryElement with a select list
 * defined by responseXML.  Automatically selects the option that is equal to
 * transCategory
 * 
 * @param transCategory String
 * @param categoryElement jQueryElement
 * @param responseXML XMLDocument
 */
function displayBudgetCategories(transCategory, categoryElement, responseXML){
	
	// Build up the select list
	var xmlroot = responseXML.getElementsByTagName("option");
	
	// If the budget category of the current transaction has not been selected
	// include the '-' no budget category marker so that it can be left as not
	// selected.
	var selCategory = "<select id='selCategory'>";
	if (transCategory == "-") {
		selCategory += "<option selected='true' value='-' >-</option>";
	}
	else {
		selCategory += "<option value='-' >-</option>";
	}
	
	// Add each of the budget categories making sure the selected budget category
	// remains selected.
	for (var i = 0; i < xmlroot.length; i++){
		var optionNode = xmlroot[i].firstChild.data;
		var optionValue = xmlroot[i].getAttribute("categoryID");
		if (optionNode.replace(/&/g,"&amp;") == transCategory) {
			selCategory += "<option selected='true' value='" + 
				optionValue.replace(/'/g,"%27") + "' >" + optionNode + "</option>\n";
		}
		else {
			selCategory += "<option value='" + 
				optionValue.replace(/'/g,"%27") + "' >" + optionNode + "</option>\n";
		}		
	}
	selCategory += "</option>";
	
	//Change the category field to a select list
	categoryElement.html(selCategory);
}

/**
 * Saves a transaction that the user is currently editing inline
 * @param transID
 */
function saveTransaction(transID) {
	// If not editing a transaction, do nothing
	if (!editing) {return;}	
	
	// Validate the New Amount
	var amountText = $('#amountText').val().replace('$','').replace(',','');
	if(!validateAmount(amountText)){
		alert("Invalid Amount ($9,999.99)");
		return;
	}
	
	// Setup the ajax parameters
	var params = "action=update";
	params += "&transID=" + transID;
	params += "&amount=" + amountText;
	
	//Get the New Category
	var selCategory = $('#selCategory').val();
	if (typeof selCategory !== 'undefined'){
		selCategory = encodeURIComponent(selCategory)
		
		if (selCategory == "-") {
			params += "&budgetCategory=";
		}
		else {
			params += "&budgetCategory=" + selCategory;
		}
	}	
	
	// Send the ajax request to update the transaction
	$.ajax({
		type: 'POST',
		url: './ops/transactions_CRUD.php',
		data: params,
		async: true
	}).done (function (data){
		location.reload(true);
	})
	.fail(function (jqXHR, msg) {
		alert('Error: Could not update transaction.');
	});
	
}

/**
 * Determines if a value is a valid date
 * @param value
 * @returns
 */
function isDate (value)
{
	return (!isNaN (new Date (value).getYear () ) ) ;
}








