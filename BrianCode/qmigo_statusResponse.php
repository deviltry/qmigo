<?php

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
// You could also log them:
//ini_set('log_errors', true);


error_reporting(E_ALL);

// This script will accept being run from the web with two query string variables:
	// $_GET['phone']
	// $_GET['message']

	// Database connect function
	$mySql = null;
	
	function sqlConnect() 
	{
		 //info.php
		# Define the connection parameters
		$hostname = "mysql.qmigo.com"; # DreamHost MySQL 
		$dbname = "qmigo"; #  DreamHost SQL Table Name
		$username = "qmigo"; #  Log IN
		$password = "itp10003"; # Your MySQL password 
 

		
		$mySql = mysql_connect($hostname, $username, $password) or die (mysql_error());
		mysql_select_db($dbname, $mySql) or die(mysql_error());
		
		return $mySql;
	}

	
	// Connect to the database
	$mySql = sqlConnect();

	//Member ID of inviting quest
	$memberIdName = "id"; 
	$memberIdValue = $_GET[$memberIdName];
	
	//Offer number of event
	$offerIdName = "o";
	$offerIdValue = $_GET[$offerIdName];
	
	// Decision:
	$offerDecisionName = "s";
	$offerDecisionValue = $_GET[$offerDecisionName];

		
		$query = "insert into qmigo_status (member_id, offer_id, offer_decision) values ('" .  $memberIdValue ."','". $offerIdValue ."','". $offerDecisionValue ."')";
		
		echo $query;

// Run the query to do the actual insert
	$result = mysql_query($query, $mySql);
	echo $result;
	
		
	// Disconnect from the database
	mysql_close($mySql);
?>

	
