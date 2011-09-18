<?php 

######################################################### 
# Member Home Page 
######################################################### 
$scriptName = $_SERVER['PHP_SELF']; 
$pageTitle = "QMIGO - Vendor Home Page"; 

# Make sure we display errors to the browser 
error_reporting(E_ALL ^ E_NOTICE); 
ini_set('display_errors', 1); 

# Get our DB info 
require "info.php"; 

# Get our site info 
require "siteinfo.php"; 

######################################################### 
# Initialize a new session or obtain old one if possible 
######################################################### 
require "info_session.php"; 

session_name($mySessionName); 
session_start(); 

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

$vendor_email = $_SESSION['vendor_email'];
 #Create the SQL query // 

$SqlStatement = "SELECT * FROM qmigo_vendors WHERE vendor_email = '" . $_SESSION['vendor_email'] . "'";


$result = mysql_query($SqlStatement,$connection);
if (!$result) die("Error " . mysql_errno() . " : " . mysql_error());


        # Run the query on the database through the connection
         $result = mysql_query($SqlStatement, $connection);
        if (!$result)
        die("Error " . mysql_errno() . " : " . mysql_error());
        

/*
Grab all the offers that have been input by the vendor
*/

/*
$SqlOffers = "SELECT * FROM qmigo_offers WHERE vendor_email = '" . $_SESSION['vendor_email'] . "'";
*/


      
######################################################### 
# Write the header 
######################################################### 
include "header.php"; 
?>

<h1>Hello, <?=$_SESSION["firstname"] . " " . $_SESSION["lastname"] . "!"?></h1><br /> 
<p>You are logged in as: <?=$_SESSION["vendor_email"] ?><br /><br /> 


<?
echo "<h2> Vendor Info </h2>";

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
{

   $vendor_id = $row['id'];	 // grab the vendor id from the qmigo_vendors table
   echo "Vendor ID: ". $vendor_id . "<br />";
   echo  "Your Venue: " .  "<br />".  $row['venue'] . "<br />";
   echo  "Address: " .   "<br />".  $row['venue_streetaddress'] . "<br />" . $row['venue_city'] ."," .$row['venue_state'] . " " .$row['venue_zipcode'] . "<br />" . "Phone: " . $row['venue_phone']  ;
   
}

$SqlOffers = "SELECT * FROM qmigo_offers WHERE vendor_id = " . $vendor_id;
//print_r($SqlOffers);
$SqlOffersCount = "SELECT COUNT(*) FROM qmigo_offers WHERE vendor_id = " . $vendor_id;
//print_r($SqlOffersCount);

$offers_result = mysql_query($SqlOffers,$connection);
$offers_total = mysql_query($SqlOffersCount,$connection);


if (!$offers_result) die("Error " . mysql_errno() . " : " . mysql_error());

        # Run the query on the database through the connection
         $offers_result = mysql_query($SqlOffers, $connection);
        if (!$offers_result)
        die("Error " . mysql_errno() . " : " . mysql_error());
        

		echo "<h2> Offers </h2>";
		//echo "<ol>";


		// returning the total number of entered offeres
		while ($row = mysql_fetch_array($offers_total, MYSQL_ASSOC)) 
				{
				echo "<h3> Total Submitted: ". $row['COUNT(*)'] . "</h3><br />";
				}


		echo "<ol>";
		while ($row = mysql_fetch_array($offers_result, MYSQL_ASSOC)) 
		{
		//print_r($row);
		   
		   echo "<li><strong>" . $row['offer'] . "</strong>". "<br />" . 
		   "Quantity: ". $row['offer_quantity'] . "<br />" . 
		   "Time: " . $row['offer_time'] . "</li><br /><br />" ;
		}
		echo "</ol>";



?>




<p>Your last login was <?=$_SESSION["last_login"]?> <br /> 

<p> 
Thanks for joining QMIGO!
<? 

######################################################### 
# Write end HTML here 
######################################################### 
include "footer.php"; 

?>