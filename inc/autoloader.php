<?php 

function my_autoloader($class)
{
	include './inc/'.$class.'.php';
}

spl_autoload_register('my_autoloader');

?>