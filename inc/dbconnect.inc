<?php
	$connection=mysql_connect("localhost","txtbudgetadmin","changeit") 
		or die ("Could not Connect to server"); 
	$db = mysql_select_db("txtbudget",$connection) or die ("Could not find database");
?>
