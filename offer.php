<?
	# Make sure we display errors to the browser
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('display_errors', 1);


######################################################### 
# Initialize a new session or obtain old one if possible 
######################################################### 
//session_save_path(); // see if dreamhost can do something...
require "info_session.php"; 
session_name($mySessionName); 
session_start(); 

$pageTitle = "QMIGO: Your Offer"; 

# Get our DB info 
require "info.php"; 



# Get our site info 
require "offersiteinfo.php"; 

# Make sure we display errors to the browser 
error_reporting(E_ALL ^ E_NOTICE); 
ini_set('display_errors', 1); 

##     TIME VARIABLES    ##

$time = time(); //server time
$timeUnix = strtotime($time); // server time in Unix Time


#########################################################
# Check that we have MEMBER ID
#########################################################
$member_id = $_GET["id"]; // POSTED MEMBER ID
$offer_id = $_GET["o"]; // POSTED OFFER ID IN THE BROWSER
if (empty($member_id))
{    header("Location: member404.php"); //steer them to an ERROR page...design one
    exit;
} 
	
	
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
     
     


#########################################################################
# Check if MEMBER ID AND OFFER ID are available and related to each other
#########################################################################
$member_name = "";
$SqlStatement = "SELECT * FROM qmigo_status WHERE member_id = $member_id AND offer_id = $offer_id";
//echo $SqlStatement;

$result = mysql_query($SqlStatement,$connection);
if (!$result) die("Error " . mysql_errno() . " : " . mysql_error());

if (mysql_num_rows($result) == 0)
{    # There is no member with this ID
    mysql_close($connection);
    header("Location: member404.php"); // change for member specific
    exit;
} 
#########################################################
# Check if MEMBER ID exists 
#########################################################
//Have to figure out how to simultaneously check for WHERE id=$member_id AND id=$offer_id | id=X&o=XX
$member_name = "";
$SqlStatement = "SELECT firstname, lastname FROM qmigo_members WHERE id=$member_id ";
$result = mysql_query($SqlStatement,$connection);
if (!$result) die("Error " . mysql_errno() . " : " . mysql_error());
if ($row = mysql_fetch_array($result,MYSQL_NUM))
{    $member_name = "$row[0] ";
}
else
{    # There is no member with this ID
    mysql_close($connection);
    header("Location: member404.php"); // change for member specific
    exit;
} 

#########################################################
# Check if OFFER HAS NOT EXPIRED - IF SO, DISPLAY LOLCAT
#########################################################
$offer_name = "";
$SqlStatement = "SELECT * FROM qmigo_offers WHERE id=$offer_id ";
$result = mysql_query($SqlStatement,$connection);
if (!$result) die("Error " . mysql_errno() . " : " . mysql_error());
if ($row = mysql_fetch_array($result,MYSQL_ASSOC))
{    $offer_name = $row['offer']; // offer name row
	 $offer_expire = $row['offer_expire']; //offer expire row
	 
	 $expiredTimeUnix = strtotime($offer_expire);
	/*
	$time = time(); //server time
	$timeUnix = strtotime($time); // server time in Unix Time
	
	*/

	 if ($expiredTimeUnix >= $time) 
		 {
		 $expired = FALSE ; // False = TIME LEFT / Still Valid
		 echo "YO" . "<br />";
		 echo $expiredTimeUnix;
		 }
		 else 
		 {
		 $expired = TRUE; // True = Time Expired  
		 echo "NOT SO BRO";
		 echo "time:" . $time;
		 echo "expiredTimeUnix: " . $expiredTimeUnix;
		 echo "OFFER EXPIRE: " . $offer_expire;
		 }


// print out other stuff
}
else
{    # There is no member with this ID
    mysql_close($connection);
    header("Location: member404.php"); // change for member specific
    exit;
} 

#########################################################
# Check if OFFER HAS BEEN REDEEMED ALREADY - MAKE SURE TO PREVENT CHEATING. 
#########################################################
//$redeemed = ""; //???
// CHECKS TO MAKE SURE OFFER IS ACTIVE AND NOT REDEEMED

$SqlStatement = "SELECT  QR_status from qmigo_status  WHERE member_id = ". $member_id ." AND offer_id = " . $offer_id ;
$result = mysql_query($SqlStatement,$connection);
if (!$result) die("Error " . mysql_errno() . " : " . mysql_error());

	if ($row = mysql_fetch_assoc($result))
	{    $redeemed = $row["QR_status"]; // Row for QR_status value
	
		 if ($redeemed == 1) {
		 $redeemed = TRUE ; // QR_status = 1, OFFER HAS ALREADY BEEN REDEEMED
		 }
		 else if ($redeemed == 0) {
		 $redeemed = FALSE; // QR_status = 0, OFFER IS STILL ACTIVE/valid. 
		 //DO SOMETHING IN THE HTML BELOW
		 }
	
	}
	


#########################################################
# Use SELECT to show VENDOR INFO + OFFER INFO
#########################################################
# $SqlStatement = "SELECT offer, unix_timestamp(offer_expire) as offer_expire FROM socialdrinkster_offers    ORDER BY offer_expire desc LIMIT 1"; 
// VENDOR X OFFER ONE - TO - MANY
$SqlStatement = "Select o.id, o.offer, unix_timestamp(o.offer_expire) AS offer_expire, v.venue, v.venue_streetaddress, v.venue_city, v.venue_state, v.venue_zipcode, 
 v.venue_phone FROM qmigo_offers o, qmigo_vendors v WHERE v.id = o.vendor_id AND o.id = $offer_id LIMIT 1";
// PICK SPECIFIC OFFER AND SPICK SPECIFIC VENDOR FROM THAT OFFER

# Run the LATEST VENDOR INFO + OFFER INFO query on the database through the connection
$result = mysql_query($SqlStatement,$connection);

if (!$result)
    die("Error " . mysql_errno() . " : " . mysql_error());
     

########################################################         
# Get our site info -> menu bar
require "offersiteinfo.php"; 	
# Write the Mobile-Friendly header 
include "qrheader.php"; 


$qmigo_url = "http://www.qmigo.com/qmigo-offercheck.php?id=". $member_id . "&o=" . $offer_id;

if ($expired == TRUE) {
//	$qrcode = "http://cyn.ical.us/media/blogs/mymedia/prophet_lol_cat.jpg";
	
	// OFFER HAS EXPIRED
	$msg = "<h1>Offer already expired.</h1>";
//	$SqlStatement = "UPDATE  qmigo_status SET QR_status = 2 WHERE member_id = ". $member_id ." AND offer_id = " . $offer_id ;
	# 0 = active, 1 = redeemed, 2 = expired	
}

else if ($redeemed) {
//	$qrcode = "images/socialdrinkster-nodice.gif";  
	$msg = "No dice. You've already redeemed your offer.";

}

else {

 $qrcode = "http://chart.apis.google.com/chart?cht=qr&chs=400x400&chl=" .  urlencode($qmigo_url);
//  $SqlStatement = "UPDATE  socialdrinkster_status SET QR_status = 1 WHERE member_id = ". $member_id ." AND offer_id = " . $offer_id ;
	# 0 = active, 1 = redeemed, 2 = expired
	$msg = "Redeem offer, " . $member_name ;

}
/*
}
*/

	$setexpire= mysql_query($SqlStatement,$connection);
	if (!$setexpire) 
	
    die("Error " . mysql_errno() . " : " . mysql_error());




?> 
<!-- QR CODE PLACED HERE -->
<!--  <img src="<?PHP echo $qrcode ?>" id="qrid"  /> -->
 <h1><?PHP echo $msg ?>  </h1>

<?PHP //echo $member_name ?> 
<?
//echo  $SqlStatement ;
// NOT RUNNING THE PROPER VENDOR ID WITH OFFER.
$fmt = "%m/%d/%Y %I:%M %p";
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) // {}'s used like '', ""s to recognize as VARIABLE.
{
	$t = strftime($fmt,$row['offer_expire']);
	$offer_id =  $row['id'];

   echo <<<END
    <br />
     <b> $offer_name </b> @ {$row['venue']} <br /> 
         Ends @ <b>  $t </b> <br /> 

    {$row['venue_streetaddress']}, {$row['venue_city']},{$row['venue_state']},{$row['venue_zipcode']}  <br /> 
   <a class="call" href="tel:1{$row['venue_phone']}">  {$row['venue_phone']} </a><br /> 


END;

}
?>

<script language="JavaScript">
// formatting for this javascript applet - TargetDate = "04/15/2010 11:10 PM";
TargetDate = "<?PHP echo $t ?>";

BackColor = "white";
ForeColor = "red";
CountActive = true;
CountStepper = -1;
LeadingZero = true;
//DisplayFormat = "%%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds.";
DisplayFormat = "%%H%%H: %%M%%M: %%S%%S";
FinishMessage = "Offer Time is Expired!";

</script>
<script language="JavaScript" src="http://scripts.hashemian.com/js/countdown.js"></script>
<br /> 
Cheers, 	<br /> 
QMIGO
<? 

######################################################### 
# Write end HTML here 
######################################################### 
include "mobilefooter.php"; 

?>


