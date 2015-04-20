<?php
	// Initialize the session.
	session_start();
	
	// Unset all of the session variables.
	$_SESSION = array();
	
	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (ini_get("session.use_cookies")) {
	    $params = session_get_cookie_params();
	    setcookie(session_name(), '', time() - 42000,
	        $params["path"], $params["domain"],
	        $params["secure"], $params["httponly"]
	    );
	}
	
	// Finally, destroy the session.
	session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="./javascript/jquery-2.0.0.min.js" ></script>
	<script type="text/javascript" src="./javascript/index.js" ></script>
	
	<link rel="stylesheet" type="text/css" href="./css/resetcss_2.0.min.css" />
	<link rel="stylesheet" type="text/css" href="./css/index.css" />

	<title>domoroboto</title>
</head>

<body>
	
	
	<h1>domoroboto</h1>
	
	<div class="login center" >
		<div>
			<span class="inputLabel" >
				User Name:
			</span>		
		
			<span class="inputField" >
				<input type=text name=uname id="userName" size=20 maxlength=20 />
			</span>		
		</div>
		
		<div>
			<span class="inputLabel" >
				Password:
			</span>
			
			<span class="inputField" >
				<input type=password name=passwd id="passWord" size=20 maxlength=20>
			</span>
		</div>
		
		<input type="button" id="login" value="Login" />
		<input type="hidden" id="accountid" name="accountid" value="" />
	</div>

	<span id="invalidLoginMessage">
		Login Failed
	</span>
	
	
</body>
</html>
