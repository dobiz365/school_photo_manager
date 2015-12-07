<?php
	include('../config.php');
	$s='mysql:host='.DB_HOST.';dbname='.DB_NAME;
	$conn= new PDO($s,DB_USER,DB_PWD);
	$conn->exec("set names utf8");
?>