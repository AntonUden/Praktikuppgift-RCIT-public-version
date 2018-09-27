<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

	use PHPAuth\Config as PHPAuthConfig;
	use PHPAuth\Auth as PHPAuth;

	$dbh = new PDO("mysql:host=localhost;dbname=artikelsida", "root", "root");
	$config = new PHPAuthConfig($dbh);
	$auth = new PHPAuth($dbh, $config);
?>
