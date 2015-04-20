// Initialize the page events
$().ready( function () {
	
	// Move the mouse to the password field if enter is pressed in the
	// userName field
	$('#userName').keydown(function (event) {		
		if(event.which == 13) 
			$('#passWord').focus();
	});
	
	// Check the Login Credentials when the enter key is pressed while
	// in the password field
	$('#passWord').keydown(function (event) {
		if (event.which == 13)
			checkLogin();
	});
	
	// Check the Login Credentials when the Login button is clicked
	$('#login').click(function () {
		checkLogin();
	});
	
	// Set the focus to the UserName Field when the page is loaded
	$('#userName').focus();
});

// Send an AJAX request to check the credentials entered by the user
function checkLogin(){
	var uname = $('#userName').val();
	var pass = $('#passWord').val();
	var params = "userName=" + uname + "&passWord=" + pass;
	
	$.ajax({
		type: 'POST',
		url: './ops/checklogin.php',
		data: params,
		async: false
	}).done(myCallBack);
	
}

// Handle the response recieved from the login AJAX request
function myCallBack(msg){
	var myMsg = $('#invalidLoginMessage');	
		
	if (msg == 'Invalid Login') {
		myMsg.html(msg);
		myMsg.css('display','block');
	}
	else {
		//Here I set the action of the form to the transactions page 
		//becuase of the Safari/Webkit/Chrome auto submit on enter
		window.location.href = 'transactions.php';
	}
		
}