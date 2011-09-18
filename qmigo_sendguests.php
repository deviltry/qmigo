<?php

// Get our DB info 
require "info.php"; 

// Include the PHP TwilioRest library
require "twilio.php";
	
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);

# Connect to the database. 
######################################################### 
$connection = mysql_connect($mySqlHostname, $mySqlUsername, $mySqlPassword); 
if (!$connection)
	{
    die("Error " . mysql_errno() . " : " . mysql_error()); 
    }

# Select the DB 
$db_selected = mysql_select_db($mySqlDatabase, $connection); 
if (!$db_selected)
	{
    die("Error " . mysql_errno() . " : " . mysql_error()); 
	}
	
$time = time(); //server time

/*
Coding Notes:
	1. Check for the last most recent offer (Cron Job runs ever 1x Min)
	2. Check if qmigo_guests has offer logged
	If so:
	Generate the related offer information via queries (offer, vendor tables)
	BLAST THE TWILIO function!

*/

# 1. Check most recent offer in qmigo_offers table (not past current time).
# Get the Offer_Expired variable from table 
# Want to get only the most recent offer in qmigo_offers table -- NOT WORKING
############################################################################# 

/*
SELECT * 
FROM  `qmigo_offers` 
WHERE offer_expire >= 
CURRENT_TIMESTAMP 
ORDER BY offer_time DESC 
LIMIT 1
*/

/*
$SqlStatement = "SELECT * FROM `qmigo_offers` WHERE `offer_time`  >= '". $time . "' ORDER BY  `offer_time` DESC";
*/
echo "HELLO " ;
$SqlStatement = "SELECT * FROM `qmigo_offers` WHERE `offer_expire`  >= FROM_UNIXTIME(". $time . ") ORDER BY  `offer_time` DESC LIMIT 1";

echo $SqlStatement;

# Get the LATEST OFFER INFO query on the database through the connection
$result = mysql_query($SqlStatement,$connection);
if (!$result)
	{
    die("Error " . mysql_errno() . " : " . mysql_error());
    }
    
if ($row = mysql_fetch_array($result,MYSQL_ASSOC))
	{    
	print_r ($row);
	$offer_expire = $row['offer_expire']; //offer expire time
	}	
else
	{	
	print "sorry, no offers";
	exit;
	}

#######################################################################

$offer_name = ""; //  SELECTS LATEST OFFER BASED ON WHAT's IN DATABASE 

//echo "$timeUnix Time: " . $timeUnix;
$expiredTimeUnix = strtotime($offer_expire);

echo " Expired offer Variable Printout!!! " .  $offer_expire . "HAY";


# Check the Offers that are Current in Time and in the Future
# Cron Job runs script 1x a minute
#########################################################

//$SqlStatement1 = "SELECT * FROM `qmigo_offers` WHERE `offer_time`  >= '". $time . "'  AND `offer_expire` >= '". $expiredTimeUnix ."' ";
$SqlStatement1 = "SELECT * FROM `qmigo_offers` WHERE `offer_expire`  >= FROM_UNIXTIME(". $time . ") ORDER BY  `offer_time` DESC LIMIT 1";

echo $SqlStatement1;

# Get the LATEST OFFER INFO query on the database through the connection
$result1 = mysql_query($SqlStatement1,$connection);
//var_dump($result1);

if (!$result1)
	{
    die("Error " . mysql_errno() . " : " . mysql_error());
    }
    
if ($row1 = mysql_fetch_array($result1,MYSQL_ASSOC))
	{    
	
	$offer_id = $row1['id']; 
	$offer_name = $row1['offer']; //  name of offer
	$offer_expire = $row1['offer_expire']; //offer expire row
	$start_time = $row1['offer_time']; //offer start-time row
	 
	 
	# Run the LATEST VENDOR INFO + OFFER INFO query on the database connection
	$SqlStatement2 = "Select o.id, o.offer, unix_timestamp(o.offer_expire) AS offer_expire, v.venue, v.venue_streetaddress, 						v.venue_city, v.venue_state, v.venue_zipcode, 
 	v.venue_phone FROM qmigo_offers o, qmigo_vendors v WHERE v.id = o.vendor_id AND o.id = $offer_id LIMIT 1";	
	// PICK SPECIFIC OFFER AND PICK SPECIFIC VENDOR FROM THAT OFFER

	$result2 = mysql_query($SqlStatement2,$connection);
	if ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) 
		{
		$venue = $row2['venue'];	
		}

	// Grab Member info in database // sql query
	$SqlStatement3 = "SELECT id, firstname, phone, hasguests FROM qmigo_members";
	//
	$result3 = mysql_query($SqlStatement3,$connection);
	print "what's up?";
	
	while ($row3 = mysql_fetch_array($result3, MYSQL_ASSOC))
		{
		$member_id = $row3['id'];
		$member_firstname = $row3['firstname'];
		$phone = $row3['phone'];
		$hasguests = $row3['hasguests'];
		
		//$SqlStatement4 = "INSERT into qmigo_status (member_id, offer_id, QR_status) VALUES ($member_id, $offer_id, 0);";
		//mysql_query($SqlStatement4,$connection) or die(mysql_error());
		//set QR status to 0.
		
		//troubleshoot printout
		//echo "check for guests";
		//if($hasguests == 1) {
		//  if (offerid matches offerid in guest table...)	
		
		//	echo "do the guest stuff";
			// grab only the guests where sms have not been sent and the offer id matches.
			$SqlHasGuests = "SELECT id, firstname, guest_phone, offer_id FROM qmigo_guests WHERE offer_id = " . $offer_id . "  AND sms_sent = FALSE";
			//$SqlStatement = "SELECT * FROM `qmigo_offers` WHERE `offer_time`  >= '". $time ."'  AND sms_sent = FALSE";
			$resultHasGuests = mysql_query($SqlHasGuests,$connection);
			// not printing out the right string - offer id messed up
			echo "offer id " .$offer_id;
			echo $SqlHasGuests;
			
			
			//If records get pulled up
				if($resultHasGuests) 
				{
					// CRAIG:
					echo "check yo guests array";	
					while ($rowGuests = mysql_fetch_array($resultHasGuests, MYSQL_ASSOC))
						{
						echo $rowGuests;
						$guest_id = $rowGuests['id'];
						$guest_firstname = $rowGuests['firstname'];
						$guest_phone = $rowGuests['guest_phone'];
						$offer_id = $rowGuests['offer_id'];
						$member_id;
						$member_firstname;
						
						$qmigo_guest_url = "http://www.qmigo.com/mobileoffer.php?gid=". $guest_id . "&o=" . $offer_id; 
										
				// Call the Twilio function to get the associated guests blasted.
				
						sendTextToGuests($offer_name, $guest_firstname, $member_firstname, $venue, $start_time, $guest_phone, 			
						$qmigo_guest_url);
						}
			
			// CRAIG: CONDITIONAL ELSE DOES NOT WORK
			//elseif (!$resultHasGuests) 
			//{
			//echo "Sorry, no guests";
			//
				}
			
		}
	}
		
// otherwise no offers available
else 
	{
	echo "No new QMIGO offers! Nothing to see, move on!";
	}







	/* TWILIO INFO */
	/*
	SMS PHONENUMBER: 646-580-6934
	call or sms this number to test
	this url will be requested when somebody sends an SMS to this phone number	
	*/
	
	
	
/* 
---------------------
SEND TEXT TO GUESTS
---------------------
*/

function sendTextToGuests($guest_firstname, $member_firstname, $offer_name, $venue, $startTime, $guest_phone, $drinkURL)
	{
print "sending text to guests with" . $guest_firstname .",". $offer_name .",". $venue.",". $startTime.",". $guest_phone.",". $drinkURL . "\n\n";
	
	
	$sMessageGuest = "Hi " . $guest_firstname  ."! " . " " . $member_firstname . "invited you for a ". $offer_name . "at " . $venue  . "Visit the offer @ Qmigo:" ." ". $drinkURL ;

	global $offer_id;
	global $connection;
					
	// The message to send // twilio limits to The text of the message you want to send, limited to 160 characters.//
		
	// Twilio REST API version
		
	$ApiVersion = "2010-04-01";

	// Set our AccountSid and AuthToken
	$AccountSid = "ACb26211f0fbf7301895430c95d3be5598";
	$AuthToken = "46194a715d2737d415646d168c90fc3c";

	// Instantiate a new Twilio Rest Client
	$client = new TwilioRestClient($AccountSid, $AuthToken);

	// make an associative array of all members linked to their phone #s
	$people = array(
	//	"9547015099"=>"Cindy",
	// somehow output the database phone numbers => name	
	$guest_phone => $guest_firstname  	
	);
		
	// Iterate over all our server 
	foreach ($people as $number => $name) 
		{		
		// Send a new outgoinging SMS by POST'ing to the SMS resource */
		// YYY-YYY-YYYY must be a Twilio validated phone number
		$response = $client->request("/$ApiVersion/Accounts/$AccountSid/SMS/Messages", 
					"POST", array(
					"To" => $number, 
					"From" => "646-580-6934",
					"Body" => $sMessageGuest
				));
		if($response->IsError)
			{
			echo "Error: {$response->ErrorMessage}";
			}
		else
			{
			//  if no errors
			echo  "Repeating SMS Guest Message: Redeem Offer: " . $guest_firstname ."! " . $offer_name." at " . $venue . ".  ";
			//print $offer_id;						
			}	
			
			// Change qmigo_guests -> sms_sent status to 1 (sent). 	
		$SqlStatement = "Update qmigo_guests  SET sms_sent = 1 WHERE offer_id = " . $offer_id;
		$result = mysql_query($SqlStatement,$connection);
			
		}
	}


?>2