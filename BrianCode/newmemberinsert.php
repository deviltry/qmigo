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

	// Message:
	/*
$messageName = "m";
	$messageValue = $_GET[$messageName];
*/
	// Phone Number:
	//$phone = $_GET['phone'];
	
	/*
$memberNumberName = "ptest";
	$memberNumberValue = $_GET[$memberNumberName];
*/
	
    //split the string into seperate name phone pairs 
	$namephone = explode(':', urldecode($messageValue));
	


   /*
	qmigo_members 
 	id 
   firstname
   lastname
   phone
   email
   */
	
	for($i=1; $i< sizeof($namephone); $i++)
	{     
	echo $namephone[$i] . "<br>";
		$currentnamephone = explode('.',$namephone[$i]);
		echo $currentnamephone[0] . " " . $currentnamephone[1] ."<br>";
		
		// name is $currentnamephone[0]
		// phone is $currentnamephone[1]
		$firstnamelastname = explode(' ',$currentnamephone[0]);
		
		
		// firstname is $firstnamelastname[0]
		// lastname is $firstnamelastname[1]
		
		echo $firstnamelastname[0] . " " . $firstnamelastname[1] ."<br>";
		
		
		$query = "insert into qmigo_members (firstname, lastname, phone) values ('" . $firstnamelastname[0] . "', '" . $firstnamelastname[1] . "','" .$currentnamephone[1] ."')";
		
		echo $query;

// Run the query to do the actual insert
	$result = mysql_query($query, $mySql);
	echo $result;
	}
	

	// Construct the SQL
	//$query = "insert into mobile_media_messages (message_text, phone_number) values ('" . $message . "', '" . $phone . "')";		
	// Disconnect from the database
	mysql_close($mySql);
?>

	
