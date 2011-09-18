<?PHP
// Require/Import the TextMarksAPIClient.class.php file
// Download: http://www.textmarks.com/dev/docs/apiclient/php/TextMarksAPIClient.class.php
// Put in directory with sms.php file


# Get our DB info 
require "info.php"; 
require "TextMarksAPIClient.class.php";

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);


######################################################### 
# Connect to the database. 
######################################################### 
$connection = mysql_connect($mySqlHostname, $mySqlUsername, $mySqlPassword); 
if (!$connection) 
    die("Error " . mysql_errno() . " : " . mysql_error()); 

# Select the DB 
$db_selected = mysql_select_db($mySqlDatabase, $connection); 
if (!$db_selected) 
    die("Error " . mysql_errno() . " : " . mysql_error()); 
     
  
#########################################################
# Only Offers Not SMS-d come up in SQL search 
#########################################################
// DON't POST IF OFFER IS STILL THE SAME. - CRON JOB NEEDED

$offer_name = ""; //  SELECTS LATEST OFFER BASED ON WHAT's IN DATABASE 
//++++ Pulls up all the offers within time-frame and where SMS_SEnt is false (not sent)
// $SqlStatement = "SELECT * FROM `socialdrinkster_offers` WHERE `offer_time` = current_timestamp AND offer_expire >= current_timestamp AND sms_sent = FALSE";

 $SqlStatement = "SELECT * FROM `qmigo_offers` WHERE `offer_time`  >= current_timestamp AND offer_expire >= current_timestamp AND sms_sent = FALSE";
$result = mysql_query($SqlStatement,$connection);
if (!$result) die("Error " . mysql_errno() . " : " . mysql_error());
if ($row = mysql_fetch_array($result,MYSQL_NUM))
{   
	 $offer_id = $row[0]; // 
	 $offer_name = $row[1]; // offer name row
	 $offer_expire = $row[5]; //offer expire row
	 $start_time = $row[3]; //offer start-time row
	 
	 
	 # Run the LATEST VENDOR INFO + OFFER INFO query on the database through the connection
	 $SqlStatement = "Select o.id, o.offer, unix_timestamp(o.offer_expire) AS offer_expire, v.venue, v.venue_streetaddress, v.venue_city, v.venue_state, v.venue_zipcode, 
 	 v.venue_phone FROM qmigo_offers o, qmigo_vendors v WHERE v.id = o.vendor_id AND o.id = $offer_id LIMIT 1";	
	// PICK SPECIFIC OFFER AND SPICK SPECIFIC VENDOR FROM THAT OFFER

	$result = mysql_query($SqlStatement,$connection);
	if ($row = mysql_fetch_array($result, MYSQL_ASSOC)) // {}'s used like '', ""s to recognize as VARIABLE.
	{
		$venue = $row['venue'];
		
	}

/*
	$setexpire= mysql_query($SqlStatement,$connection);
	if (!$setexpire) 	
    die("Error " . mysql_errno() . " : " . mysql_error());

*/

	
	//  GRABBING ALL MEMBER INFO (id, phone) WITHIN socialdrinkster_members table
/*
	$member_id = "";
	$SqlStatement = "SELECT id, phone FROM socialdrinkster_members WHERE id=$member_id ";
	$result = mysql_query($SqlStatement,$connection);
	if (!$result) die("Error " . mysql_errno() . " : " . mysql_error());
	if ($row = mysql_fetch_array($result,MYSQL_ASSOC))
	{    $member_id = $row['id']; //member id
		 $phone = $row['phone']; // member phone
	}
   */
	// create connection btw. member + offer --> INSERT into socialdrinkster_status + setup. 0 = active offers
	// creates working links for qmigo.com/qmigo-offer.php?id=XX&o=XX
	


	//sendTextToMe();
	
	// load all users in database // sql query
	$SqlStatement = "SELECT id, firstname, phone FROM qmigo_members";
	$result = mysql_query($SqlStatement,$connection);
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$member_id = $row['id'];
		$member_firstname = $row['firstname'];
		$phone = $row['phone'];
		// do stuff here....
		//	print("START NEW LOOP: <br />");
		//	print($member_id);
		//	print("<br />");
		//	print($offer_id);
		//	print("<br />");
		
		$SqlStatement = "INSERT into qmigo_status (member_id, offer_id, QR_status) VALUES ($member_id, $offer_id, 0);";
		mysql_query($SqlStatement,$connection) or die(mysql_error());

		$qmigo_url = "http://www.qmigo.com/mobileoffer.php?id=". $member_id . "&o=" . $offer_id;
		
		sendTextToMe($offer_name, $member_firstname, $venue, $start_time, $phone, $qmigo_url);
		// function sendTextToMe2($offerName, $venueName, $startTime, $phoneNumber, $drinkURL)
		// need the member_id, offer_id to create the $drinkURL
		
		
	}


}
else {
echo "No new QMIGO offers! Nothing to see, move on!";
}






function sendTextToMe($member_firstname, $offerName, $venueName, $startTime, $phoneNumber, $drinkURL)
{
	//TEST FOR PARSING PURPOSES...
	echo "TEST BEFORE SMS BLAST. Latest Offer: " . $offerName ." at " . $venueName . ".Starts: " . $startTime ;
	
	global $offer_id;
	global $connection;
	
	// TextMarks API Key: Request from: http://www.textmarks.com/dev/api/reg/?ref=devapi
	$sMyApiKey='itp_nyu_edu_bej2_b28c545b'; // brian's api key
	
	// TextMarks Username or Phone Number
	$sMyTextMarksUser = 'bklynjones'; //(or my TextMarks phone)
	$sMyTextMarksPass = 'sweetmeat';
	
	// TextMarks Keyword
	$sKeyword = 'Drinkster';
	
	// Who are you going to send the text to?
	//$phoneNumber = ''; // create a FOR LOOP that goes through each member row in table.
	
	//$qmigo_url = "http://www.qmigo.com/qmigo-offer.php?id=". $member_id . "&o=" . $offer_id;

	// The message to send
	$sMessage = "QMIGO Offer: " . $member_firstname ."! ". $offerName ." at " . $venueName . " Redeem/Visit: ". $drinkURL ;
	
	// Create the TextMarks Object with the above parameters
	$tmapi = new TextMarksAPIClient_Messaging($sMyApiKey, $sMyTextMarksUser, $sMyTextMarksPass);
	
	// Send the message! this must be sent to each individual....
	//get all members in member table. then put that stuff inside the FORLOOP. 
	$tmapi->sendText($sKeyword, $phoneNumber, $sMessage);
	
	// For debugging, dump out the results
	//var_dump($tmapi);
	
	echo "Repeating SMS Message: QMIGO Redeem Offer: " . $member_firstname ."! " . $offerName ." at " . $venueName . ".  ";
		
		
	// Change socialdrinkster_offers -> sms_sent status to 1 (sent). 	
	$SqlStatement = "Update qmigo_offers  SET sms_sent = 1 WHERE id = $offer_id";
	/* SQL COMMAND  TO UPDATE socialdrinkster_offers SET SMS_SENT = 1 where offer_id = $offer_id
	   for sms_sent --> 1 (true) sent.
	   echo $SqlStatement; used for troubleshooting
	*/
	$result = mysql_query($SqlStatement,$connection);
}






?>
