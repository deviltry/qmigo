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
//$timeUnix = strtotime($time); // server time in Unix Time


# 1. Generate All the Offers in the SQL Query that are not past current time, haven't been sent
#####################################################################
$SqlStatement = "SELECT * FROM `qmigo_offers` WHERE `offer_time`  >= '". $time ."'  AND sms_sent = FALSE";
# Get the LATEST OFFER INFO query on the database through the connection
$result = mysql_query($SqlStatement,$connection);
if (!$result)
	{
    die("Error " . mysql_errno() . " : " . mysql_error());
    }
    
if ($row = mysql_fetch_array($result,MYSQL_ASSOC))
	{    
	$offer_expire = $row['offer_expire']; //offer expire time
	}	
else
	{	
	print "sorry, no offers";
	exit;
	}

# Only Offers Not SMS-d come up in SQL search , don't pass old offers
#######################################################################

$offer_name = ""; //  SELECTS LATEST OFFER BASED ON WHAT's IN DATABASE 

echo "Server Time: " . $time;
$expiredTimeUnix = strtotime($offer_expire);
echo "$offer_expire: Expire " . $expiredTimeUnix;


# Only Offers Not SMS-d come up in SQL search 
# Cron Job runs script 1x a minute
#########################################################

$SqlStatement1 = "SELECT * FROM `qmigo_offers` WHERE `offer_time`  >= '". $time ."'  AND `offer_expire` >= '". $expiredTimeUnix ."' AND sms_sent = FALSE";
# Get the LATEST OFFER INFO query on the database through the connection
$result1 = mysql_query($SqlStatement1,$connection);
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

	// load all users in database // sql query
	$SqlStatement3 = "SELECT id, firstname, phone, hasguests FROM qmigo_members";
	
	$result3 = mysql_query($SqlStatement3,$connection);
	
	print "what's up?";
	
	while ($row3 = mysql_fetch_array($result3, MYSQL_ASSOC))
		{
		$member_id = $row3['id'];
		$member_firstname = $row3['firstname'];
		$phone = $row3['phone'];
		$hasguests = $row3['hasguests'];
		
		// Troubleshooting Code Printouts
		//	print("START NEW LOOP: <br />");
		//	print($member_id);
		//	print("<br />");
		//	print($offer_id);
		//	print("<br />");
		
		$SqlStatement4 = "INSERT into qmigo_status (member_id, offer_id, QR_status) VALUES ($member_id, $offer_id, 0);";
		mysql_query($SqlStatement4,$connection) or die(mysql_error());
		//set QR status to 0.
		
		$status_id = mysql_insert_id(); // passing this value into guest interaction
		
		$qmigo_url = "http://www.qmigo.com/mobileoffer.php?id=". $member_id . "&o=" . $offer_id;
		
		// Call the Twilio function to get the members blasted.
		sendTextToMe($offer_name, $member_firstname, $venue, $start_time, $phone, $qmigo_url);
		
		//troubleshoot printout
		echo "check for guests";
		
		if($hasguests == 1) 
			{
			echo "do the guest stuff";
			// Select all the guests that a member will add.
			// for each guest, send them the sms.
		
			// interaction only happens with the guests.
		
			// Brian needs to make sure all his contacts are going into the correct tables when offer is created.  
			// When brian's guest info is added in. 
			// Need to do SELECT statement and get them updated...
			
			$SqlStatement5 = "SELECT id, firstname, guest_phone, offer_id FROM qmigo_guests WHERE offer_id = " . $offer_id;
			$result5 = mysql_query($SqlStatement5,$connection);
							
//troubleshoot printout
print "running this sql, sqlstatement5: " . $SqlStatement5 . "\n\n";
							
			while ($row5 = mysql_fetch_array($result5, MYSQL_ASSOC))
				{
//troubleshoot printout
print "in loop:" . print_r($row5) . "\n\n";

				$guest_id = $row5['id'];
				$guest_firstname = $row5['firstname'];
				$guest_phone = $row5['guest_phone'];
				$offer_id = $row5['offer_id'];
				$member_id;
				$member_firstname;

//				mysql_query($SqlStatement5,$connection) or die(mysql_error());
//				//set QR status to 0.
				
				$qmigo_guest_url = "http://www.qmigo.com/mobileoffer.php?gid=". $guest_id . "&o=" . $offer_id;
				// 
						
				
				sendTextToGuests($offer_name, $guest_firstname, $member_firstname, $venue, $start_time, $guest_phone, 			
				$qmigo_guest_url);
				//$qmigo_guest_url

				// Call the Twilio function to get the associated guests blasted.
				}
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
SEND TEXT TO MEMBERS
---------------------
*/
function sendTextToMe($member_firstname, $offer_name, $venue, $startTime, $phone, $drinkURL)
	{
	global $offer_id;
	global $connection;		
			
	// The message to send // twilio limits to The text of the message you want to send, limited to 160 characters.//
	$sMessage = "Redeem: " . $member_firstname ."! A". $offer_name . " " ." at " . $venue  . " " . " Visit: ". $drinkURL ;
	
	// Twilio REST API version	
	$ApiVersion = "2010-04-01";
		
	// Set our AccountSid and AuthToken
	$AccountSid = "ACb26211f0fbf7301895430c95d3be5598";
	$AuthToken = "46194a715d2737d415646d168c90fc3c";

	// Instantiate a new Twilio Rest Client
	$client = new TwilioRestClient($AccountSid, $AuthToken);
		
	// make an associative array of all members linked to their phone #s
	$people = array(
	//	"2128675309"=>"Cindy",
	// somehow output the database phone numbers => name	
	$phone => $member_firstname  	
	);
		
	// Iterate over all our server admins
	foreach ($people as $number => $name) 
		{
		// Send a new outgoinging SMS by POST'ing to the SMS resource */
		// YYY-YYY-YYYY must be a Twilio validated phone number
		$response = $client->request("/$ApiVersion/Accounts/$AccountSid/SMS/Messages", 
			"POST", array(
			"To" => $number, 
			"From" => "646-580-6934",
			"Body" => $sMessage
			//$sMessage = "Redeem: " . $member_firstname ."! ". $offerName ." at " . $venueName . " Visit: ". $drinkURL ;
			));

		if($response->IsError)
			{
			echo "Error: {$response->ErrorMessage}";
			}
		//  if no errors
		else
			{
			echo  "Repeating SMS Message: Redeem Offer: " . $member_firstname ."! " . $offer_name." at " . $venue . ".  ";
					//print $offer_id;
			}			
					
		// Change socialdrinkster_offers -> sms_sent status to 1 (sent). 	
		$SqlStatement = "Update qmigo_offers  SET sms_sent = 1 WHERE id = $offer_id";
		$result = mysql_query($SqlStatement,$connection);	
		}
	}
	
	
/* 
---------------------
SEND TEXT TO GUESTS
---------------------
*/



?>