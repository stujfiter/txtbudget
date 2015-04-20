<?php

/*
include_once '/inc/autoloader.php';


$data = new MyDB;
$trans = $data->getTransactions('May 2013',1);

echo json_encode($trans)."<br />";

*/
$to = 'kearl@weirspm.com';
$from = 'accountactivation@txtbudget.net';
$subject = 'php mail test';
$message = 'this is a test of e-mail from php';
$smtp = 'txtbudget.net.smtp.noip.com:587';
$smtpAuthPassword = 'changeit';

$shellCommand = "echo \"$message\" | mailx -s \"$subject\""
	." -S smtp=".$smtp
	." -S from=".$from
	." -S smtp-auth=login"
	." -S smtp-auth-user=".$from
	." -S smtp-auth-password=".$smtpAuthPassword
	." ".$to;
echo $shellCommand;
$output = shell_exec($shellCommand);

echo $output;
?>
