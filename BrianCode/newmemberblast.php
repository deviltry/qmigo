<?php

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
// You could also log them:
//ini_set('log_errors', true);
//ini_set('error_log', '/home/~bej223/php_errors.log');


error_reporting(E_ALL);

// This script will accept being run from the web with two query string variables:
	// $_GET['phone']
	// $_GET['message']

	// Database connect function
	$mySql = null;
	
	function sqlConnect() 
	{
		# Configuration Variables
		/*
        $hostname = "localhost";
		$dbname = "bej223";
		$username = "bej223";
		$password = "oT7b-cPo9";
		*/
		$mySqlHostname = "mysql.qmigo.com"; # DreamHost MySQL 
		$mySqlDatabase = "qmigo"; #  DreamHost SQL Table Name
		$mySqlUsername = "qmigo"; #  Log IN
		$mySqlPassword = "itp10003"; # Your MySQL password 

		
		$mySql = mysql_connect($hostname, $username, $password) or die (mysql_error());
		mysql_select_db($dbname, $mySql) or die(mysql_error());
		
		return $mySql;
	}

	
	
	// Connect to the database
	$mySql = sqlConnect();

	// Message:
	$message = $_GET['message'];
	// Phone Number:
	$phone = $_GET['phone'];

	
    
    //split the string into seperate name phone pairs 
	$namephone = explode(':', urldecode($message));
	


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
		// Is name in database?
		// Do a search query in SQL to check if its logged?
		// if QUERY is TRUE, DO THIS BELOW...
		$query = "SELECT id, phone FROM qmigo_members";
			$result = mysql_query($query, $mySql);
	 
		
		 	if ($result == 0) {
			// 0 Results returned from SQL means NO RECORD OF PHONE NUMBER/USER 
			
			$query = "insert into qmigo_members (firstname, lastname, phone) values ('" . $firstnamelastname[0] . "', '" . $firstnamelastname[1] . "','" .$currentnamephone[1] ."')";
			//echo $query;
			
			   /*
			   socialdrinkster_status table layout
				socialdrinkster_status
 				id 
				member_id
				offer_id
				QR_status (0=active, 1=redeemed, 2=expired)
				*/
			
			$query = "INSERT into qmigo_status (member_id, offer_id, QR_status) VALUES ($member_id, $offer_id, 0);";
			$result = mysql_query($query, $mySql);
			echo $result;
			}

			}
				 else if (!$result) {
				 $redeemed = FALSE; //  Results returned. There is a record of this phone number.
				
	       		 $query = "INSERT into qmigo_status (member_id, offer_id, QR_status) VALUES ($member_id, $offer_id, 0);";
				 $result = mysql_query($query, $mySql);
				// echo $result;		
				 }
				 
		$qmigo_url = "http://www.qmigo.com/qmigo-mobileoffer.php?id=". $member_id . "&o=" . $offer_id;
		sendTextToMe($offer_name, $member_firstname, $venue, $start_time, $phone, $qmigo_url);
	
				 
	}
	
	
function sendTextToMe($member_firstname, $offerName, $venueName, $startTime, $phoneNumber, $qmigo_url)
{
	//TEST FOR PARSING PURPOSES...
	echo "TEST BEFORE SMS BLAST. Latest Offer: " . $offerName ." at " . $venueName . ".Starts: " . $startTime ;
	
	global $offer_id;
	global $connection;
	global $qmigo_url;
	// TextMarks API Key: Request from: http://www.textmarks.com/dev/api/reg/?ref=devapi
	$sMyApiKey='itp_nyu_edu_bej2_b28c545b'; // brian's api key
	
	// TextMarks Username or Phone Number
	$sMyTextMarksUser = 'bklynjones'; //(or my TextMarks phone)
	$sMyTextMarksPass = 'sweetmeat';
	
	// TextMarks Keyword
	$sKeyword = 'Drinkster';
	
	// The message to send
	$sMessage = "Redeem: " . $member_firstname ."! ". $offerName ." at " . $venueName . " ". $startTime . ". ". $qmigo_url ;
	
	// Create the TextMarks Object with the above parameters
	$tmapi = new TextMarksAPIClient_Messaging($sMyApiKey, $sMyTextMarksUser, $sMyTextMarksPass);
	
	// Send the message! this must be sent to each individual....
	//get all members in member table. then put that stuff inside the FORLOOP. 
	$tmapi->sendText($sKeyword, $phoneNumber, $sMessage);
	
	// For debugging, dump out the results
	//var_dump($tmapi);
	
	echo "Repeating SMS Message: Redeem Offer: " . $member_firstname ."! " . $offerName ." at " . $venueName . ".  ";
			
	// Change qmigo_offers -> sms_sent status to 1 (sent). 	
	//$SqlStatement = "Update qmigo_offers  SET sms_sent = 1 WHERE id = $offer_id";
	/* SQL COMMAND  TO UPDATE qmigo_offers SET SMS_SENT = 1 where offer_id = $offer_id
	   for sms_sent --> 1 (true) sent.
	   echo $SqlStatement; used for troubleshooting
	*/
	//$result = mysql_query($SqlStatement,$connection);
}
		
	// Disconnect from the database
	mysql_close($mySql);
?>

	
