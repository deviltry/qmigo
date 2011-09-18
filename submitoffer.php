<?php 
//var_dump($_POST); // troubleshoot what gets submitted to browser


      
######################################################### 
# QMIGO Template
######################################################### 
$scriptName = $_SERVER['PHP_SELF']; 
$pageTitle = "QMIGO: Make An Offer"; 

# Get our DB info 
require "info.php"; 

# Get our site info 
require "siteinfo.php"; 

# Make sure we display errors to the browser 
error_reporting(E_ALL ^ E_NOTICE); 
ini_set('display_errors', 1); 


######################################################### 
# Initialize a new session or obtain old one if possible 
######################################################### 
require "info_session.php"; 

session_name($mySessionName); 
session_start(); 

// echo 		 $_SESSION["userid"]  ; 
// echo            $_SESSION["firstname"] ; 
// echo            $_SESSION["lastname"] ; 



######################################################### 
# Go to login page if not logged in 
######################################################### 
if ($_SESSION["logged-in"]!=1 || $_SESSION["userid"]<1) 
{    header("Location: login.php?err=notloggedin"); 
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


# Submit Button ID
$submitOffer = "DataAction"; 

# Offer Type - Text Field
$textOfferName = "offer";     
$textOfferNameValue = $_POST[$textOfferName];     

# Offer Quantity - Text Field
$textOfferQuantityName = "offer_quantity"; //input-name
$textOfferQuantityValue = $_POST[$textOfferQuantityName]; 

# Offer Start Date - Text Field (JAVASCRIPT pop-up cal)
$offerStartDateName = "date1";
$offerStartDateValue = $_POST[$offerStartDateName];


# Offer Start Time - Select Menu
$offerStartTimeName = "time1";
$offerStartTimeValue = $_POST[$offerStartTimeName];

# Offer Duration Time - Select Menu
$offerDurationName = "duration";
$offerDurationValue = $_POST[$offerDurationName];

# Time Zone - Select Menu
$offerTimeZoneName = "timezone";
$offerTimeZoneValue = $_POST[$offerTimeZoneName];

# Submit Button Name
$submitNewOfferValue = "Submit New Offer";    
# This is what it will say on our submit site button

#################### Status variables ################
$statusMsg = " ";    # Gives response back to user (i.e. "Thank you for your ...")
$hasErrors = 0;    # Keeps track of whether there are input errors

#### ERROR CHECKING IF NECESSARY ###

if ($_POST[$submitOffer]==$submitNewOfferValue)
{    # Someone submitted new offer

    # Error Checking
    $noOffer = 0;    # Flag that is set if Offer Field was blank
    $noOfferQuantity = 0;    # Flag that is set if quantity field was blank
    $noDate = 0; 
    $oldOffer = 0; # Flag set if offer time is past current time
    
    // Offer Named
    $offer = trim($textOfferNameValue);
    if (empty($offer))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noOffer = 1;
        $statusMsg = "Please name your offer.";
    }
    
    // Offer Quantity
  	$offer_quantity= trim($textOfferQuantityValue);
    if (empty($offer_quantity))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noOfferQuantity= 1;
        $statusMsg = "How many offers do you want to post? 1? 2? ";
    }
    
    // Offer Date
    /// BRIAN CHECK THIS OUT LINES - 123-134 ///

  	$offer_date = trim($offerStartDateValue).' '.trim($offerStartTimeValue);
  	$offer_timeUnix = strtotime($offer_date); // Offer Date Time in Unix
  	echo  "Offer-date:". $offer_date . "| ";
  	echo "offer time in unix". $offer_timeUnix . "|  ";
  	
  	$time = time(); //current server time
  	echo "time: " . $time . "|  "; // server time in Unix Time

	if ($time > $offer_timeUnix) 
	{ 
	// current time is past the date offer
	$hasErrors = 1;
	$oldOffer = 1 ; // Time has expired
	$message = "Please select a current date/time 30 minutes from now.";
	}
	/// BRIAN CHECK THIS OUT LINES - 123-134 ///
	
	/*
    if (empty($offerStartDateValue))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noOfferDate= 1;
        $statusMsg = "When does your offer start? ";
    }
    */
    
    // Time Duration
    $duration= trim($offerDurationValue);
    if (empty($duration))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noOfferDuration= 1;
        $statusMsg = "Check your duration time";
    }
    
   //Time Zone  
    $timezone= trim($offerTimeZoneValue);
    if (empty($timezone))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noOfferTimezone= 1;
        $statusMsg = "Check your time zone";
    }

	//var_dump($_POST); // TROUBLE-SHOOTING THE OUTPUT OF THE WEBSITE INPUT VALUES
	//	print " *** Has errors: $hasErrors  **** <br/>";

   # NO ERRORS? OK! Put it in the database
    if (!$hasErrors)
    {    # Replace any single quotes in our firstname
        $offerDB =  str_replace("'", "''", $offer);
        $offer_quantityDB =   $offer_quantity;
 //       $date1DB =   $date1;
//        $starttimeDB =  $starttime;
        $durationDB =   $duration;
        //$timezoneDB =   $timezone;
        
        // echo $textOfferQuantityValue;
		// echo $_SESSION["vendor_email"]  ; 
           
         #Create the SQL query // 
         $SqlStatement = "INSERT INTO qmigo_offers(offer,offer_quantity,offer_time, duration, timezone, offer_expire,  vendor_id)
         VALUES ('$offerDB','$offer_quantityDB','$offer_date', '$durationDB', ' $timezone', DATE_ADD('$offer_date', INTERVAL '$durationDB' HOUR),  ". $_SESSION['userid'] . ")";
    
  		# Ex: DATE_ADD('2000-12-31 23:59:59', INTERVAL 1 SECOND);   
		print "$SqlStatement <br/>"; // troubleshooting

	
        # Run the query on the database through the connection
         $result = mysql_query($SqlStatement, $connection);
        if (!$result)
        die("Error " . mysql_errno() . " : " . mysql_error());
        
        
        
        $statusMsg = "<span class='highlight'>  Hola! Thank you for submitting your offer at QMIGO!</span>";
        
        # Reset the text widgets to accept input once again for the next submission
        $textOfferValue = "";
        $textQuantityValue = "";
        
    }

}



?> 
<html>
<head>
<link rel="stylesheet" href="qmigo.css" type="text/css" media="all" /> 
 <SCRIPT LANGUAGE="JavaScript" SRC="CalendarPopup.js"></SCRIPT> 
    <SCRIPT LANGUAGE="JavaScript"> 
  	function checkdate(){
	var time = Date.getTime();
	var formTime = Date.parse(document.entry.<?= $offerStartDateName ?>.value);
	formTime = Date.setTime(formTime);
	if(formTime < Date.setTime(Date.getTime()+ 1800000){
		document.entry.<?= $offerStartDateName ?>.value = "";
		alert("please enter a time a least 30 minutes from now");
		}
	 } 
  	 var cal = new CalendarPopup();
    </SCRIPT> 
</head>

	<body>
	<div id="wrapper">
	<div class="titlebox" style="margin-left: 50px"> 
	<img src="images/qmigo_logo2.png" />
	</div>   
<div id="container" style="margin-left: 30px";> 
<div class="navbox" style="margin-left: 30px"> 
<br />



<?
if ($statusMsg!="")
{
	echo "<b>$statusMsg</b> <br/><br/>";
}
?>

<h1> Make a Special Offer </h1>
<hr size=1 color="#000000">

	<!-- SUBMIT FORM STARTS HERE-->
    <form action="<?PHP echo $scriptName ?>" method="POST" name="entry" enctype="application/x-www-form-urlencoded" onsubmit = 'checkdate();'>
		 What do you want to offer? 
		<br />Drinks? Tapas? Cupcakes?
		<br />
		<input type="text" name="<?= $textOfferName ?>">
		<br />
		
		Quantity (numbers only): 
		<br />
		<input type="text" name="<?= $textOfferQuantityName ?>">
		<br />
		
	
	Date: YYYY/MM/DD<br />
	<!-- javascript calendar goes into popup window -->
	<INPUT TYPE="text" NAME="<?= $offerStartDateName ?>" VALUE="" SIZE=25> 
	<A HREF="#"
 	  onClick="cal.select(document.forms['entry'].<?= $offerStartDateName ?>,'anchor1','yyyy-MM-dd'); return false;"
  	 NAME="anchor1" ID="anchor1">Calendar Pop-Up</A> 
  	

		<br />
		Start Time:<br />
<!-- MYSQL offer_time = date + time fields -->
		<select name="<?= $offerStartTimeName ?>"> <!--create time drop-down with php -->
			<option value="00:00:00">12:00 AM</option>
			<option value="00:15:00">12:15 AM</option>
			<option value="00:30:00">12:30 AM</option>
			<option value="00:45:00">12:45 AM</option>
			<option value="01:00:00">01:00 AM</option>
			<option value="01:15:00">01:15 AM</option>
			<option value="01:30:00">01:30 AM</option>
			<option value="01:45:00">01:45 AM</option>
			<option value="02:00:00">02:00 AM</option>
			<option value="02:15:00">02:15 AM</option>
			<option value="02:30:00">02:30 AM</option>
			<option value="02:45:00">02:45 AM</option>
			<option value="03:00:00">03:00 AM</option>
			<option value="03:15:00">03:15 AM</option>
			<option value="03:30:00">03:30 AM</option>
			<option value="03:45:00">03:45 AM</option>
			<option value="04:00:00">04:00 AM</option>
			<option value="04:15:00">04:15 AM</option>
			<option value="04:30:00">04:30 AM</option>
			<option value="04:45:00">04:45 AM</option>
			<option value="05:00:00">05:00 AM</option>
			<option value="05:15:00">05:15 AM</option>
			<option value="05:30:00">05:30 AM</option>
			<option value="05:45:00">05:45 AM</option>
			<option value="06:00:00">06:00 AM</option>
			<option value="06:15:00">06:15 AM</option>
			<option value="06:30:00">06:30 AM</option>
			<option value="06:45:00">06:45 AM</option>
			<option value="07:00:00">07:00 AM</option>
			<option value="07:15:00">07:15 AM</option>
			<option value="07:30:00">07:30 AM</option>
			<option value="07:45:00">07:45 AM</option>
			<option value="08:00:00">08:00 AM</option>
			<option value="08:15:00">08:15 AM</option>
			<option value="08:30:00">08:30 AM</option>
			<option value="08:45:00">08:45 AM</option>
			<option value="09:00:00">09:00 AM</option>
			<option value="09:15:00">09:15 AM</option>
			<option value="09:30:00">09:30 AM</option>
			<option value="09:45:00">09:45 AM</option>
			<option value="10:00:00">10:00 AM</option>
			<option value="10:15:00">10:15 AM</option>
			<option value="10:30:00">10:30 AM</option>
			<option value="10:45:00">10:45 AM</option>
			<option value="11:00:00">11:00 AM</option>
			<option value="11:15:00">11:15 AM</option>
			<option value="11:30:00">11:30 AM</option>
			<option value="11:45:00">11:45 AM</option>
			<option value="12:00:00">12:00 PM</option>
			<option value="12:15:00">12:15 PM</option>
			<option value="12:30:00">12:30 PM</option>
			<option value="12:45:00">12:45 PM</option>
			<option value="13:00:00">13:00 PM</option>
			<option value="13:15:00">13:15 PM</option>
			<option value="13:30:00">13:30 PM</option>
			<option value="13:45:00">13:45 PM</option>
			<option value="14:00:00">14:00 PM</option>
			<option value="14:15:00">14:15 PM</option>
			<option value="14:30:00">14:30 PM</option>
			<option value="14:45:00">14:45 PM</option>
			<option value="15:00:00">15:00 PM</option>
			<option value="15:15:00">15:15 PM</option>
			<option value="15:30:00">15:30 PM</option>
			<option value="15:45:00">15:45 PM</option>
			<option value="16:00:00">16:00 PM</option>
			<option value="16:15:00">16:15 PM</option>
			<option value="16:30:00">16:30 PM</option>
			<option value="16:45:00">16:45 PM</option>
			<option value="17:00:00">17:00 PM</option>
			<option value="17:15:00">17:15 PM</option>
			<option value="17:30:00">17:30 PM</option>
			<option value="17:45:00">17:45 PM</option>
			<option value="18:00:00">18:00 PM</option>
			<option value="18:15:00">18:15 PM</option>
			<option value="18:30:00">18:30 PM</option>
			<option value="18:45:00">18:45 PM</option>
			<option value="19:00:00">19:00 PM</option>
			<option value="19:15:00">19:15 PM</option>
			<option value="19:30:00">19:30 PM</option>
			<option value="19:45:00">19:45 PM</option>
			<option value="20:00:00">20:00 PM</option>
			<option value="20:15:00">20:15 PM</option>
			<option value="20:30:00">20:30 PM</option>
			<option value="20:45:00">20:45 PM</option>
			<option value="21:00:00">21:00 PM</option>
			<option value="21:15:00">21:15 PM</option>
			<option value="21:30:00">21:30 PM</option>
			<option value="21:45:00">21:45 PM</option>
			<option value="22:00:00">22:00 PM</option>
			<option value="22:15:00">22:15 PM</option>
			<option value="22:30:00">22:30 PM</option>
			<option value="22:45:00">22:45 PM</option>
			<option value="23:00:00">23:00 PM</option>
			<option value="23:15:00">23:15 PM</option>
			<option value="23:30:00">23:30 PM</option>
			<option value="23:45:00">23:45 PM</option>		
			</select>
		<br />
		
			Duration of Offer:<br />

	<select name="<?PHP echo $offerDurationName ?>"> <!-- duration availability drop-down with php -->
			<option value="1">1 HR</option>
			<option value="2">2 HR </option>
			<option value="3">3 HR </option>
			<option value="4">4 HR </option>
			<option value="5">5 HR </option>
			<option value="6">6 HR </option>
			<option value="7">7 HR </option>
			<option value="8">8 HR </option>
			<option value="9">9 HR </option>
			<option value="10">10 HR </option>
			<option value="11">11 HR </option>
			<option value="12">12 HR </option>
			</select>

		<br />
		
		Time Zone:<br />

		<select name="<?PHP echo $offerTimeZoneName ?>"> <!-- Time Zone -->
			<option value="3">EST</option>
			<option value="2">CST</option>
			<option value="1">MST</option>
			<option value="0">PST </option>
					</select>
	
		<br /><br />
		
	   		<input type="submit" name="<?PHP echo $submitOffer ?>" value="<?PHP echo $submitNewOfferValue ?>">		

	</form>
	<br />

<? 

######################################################### 
# Write end HTML here 
######################################################### 
include "footer.php"; 

######################################################### 
# Disconnect from the database. 
######################################################### 
mysql_close($connection); 

?>