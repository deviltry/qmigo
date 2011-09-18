<?php

include('mysql_to_json.class.php');

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
	
	
	$passedMemberIdName = 'idpass';
	$passedMemberIdValue = $_GET[$passedMemberIdName];
	
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
	
	// Assign the query
/*
	$select = ' SELECT ';
	$column = ' * ';
	$from = ' FROM ';
	$tables = 'qmigo_members';
	$where = 'id=';
	
	
*/

	
	$query = " SELECT * FROM `qmigo_members` WHERE id=".$passedMemberIdValue;

	
	//Execute
	$result = mysql_query($query);
	
	if (!$result){
	die ("false". mysql_error( ));
	}
	
	/////////////////////////////////////
// METHOD 1 - Using constructor   //
///////////////////////////////////

//create a new instance of mysql_to_json
$mId = new mysql_to_json($query);

//show the json output
echo $mId->get_json();
	
	//echo $result;
	
	//array mysql_fetch_row(resource $result);
	
	/*
while ($result_row = mysql_fetch_row(($result))){
	echo 'id: '.$result_row[0] . '<br />';
	echo 'first name: '.$result_row[1] . '<br /> ';
	echo 'last name: '.$result_row[2] . '<br /><br />';
	}
*/
	?>