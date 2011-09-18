<?php 

######################################################### 
# QMIGO Template
######################################################### 
$scriptName = $_SERVER['PHP_SELF']; 
$pageTitle = "About QMIGO";

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
# Write the header 
######################################################### 
include "header.php"; 

?> 

<h1> Thanks for visiting QMIGO! </h1>
<p>
QMIGO is about free alerts for free things. 
On The Go.<br /> In Your Pocket.
A friendly mobile app developed in New York City<br />  at <a href="http://itp.nyu.edu" target="_blank">NYU ITP</a> by Brian E. Jones + <a href="http://www.pixelpunchout.com/about">Cindy Wong<a>. It was previously known as SocialDrinkster.

<div class="float">
	<img src="images/app_android_front.png" width="300"/>	<img src="images/app_iphone_front.png" width="300"/>
	</div>
<br style="clear:both" />

<h1>Why  QMIGO?</h1> <br />
People like free things. People like to do social things. People especially like passing freebies to friends.
Businesses can attract flocks of people to test products or fill the room up. <br />
As a mobile app, QMIGO alerts you quickly. You can check your email, surf the news, and still get an alert clueing you in to a bar's free drink offer, restaurant's free tapas offer, etc.
<br />
<h1>How Does  QMIGO Work?</h1> <br />
QMIGO works by offering users a unique freebie invitation.<br /> 
Businesses submit their special offers to our platform.
Our website keeps track of offeres and buzzes users within the QMIGO network.
Quickly, users are alerted about the offer. 
If they like it, they can accept the offer invite and get a unique freebie invitation.
<br />
Users see their freebie invite info and the time they have to arrive at the location.
<br />
They show their unique QMIGO QR Code Screen to the business. The business scans their QMIGO QR Code with any QR Code reader on their smartphone (we recommend <a href="http://www.neoreader.com/">NeoReader</a>). Ding!
Their offer gets redeemed and shows up in the business's scan. Enjoy the freebie! See below.
<br style="clear:both" />

	<div class="float">
	<img src="images/app_iphone_redeem.png" width="300"/>	<img src="images/android_front_redeem.png" width="300"/>
	</div>
	<br style="clear:both" />

<h1>Who's Behind QMIGO?</h1> <br />
<img src="images/Cindy_Brian_SocialDrinkster.jpg" width="300"/>
<br style="clear:both" />

<b>Brian E Jones:</b><br />
Brian Jones is a co-founder of QMIGO. He is a second-year graduate student at NYU's ITP program. 
Brian helps in Android app development, SMS integration, user development/experience, and marketing.<br />
<b> Cindy Wong: </b><br />
<a href="http://www.pixelpunchout.com/about" target="_blank">Cindy Wong </a> is a  is a co-founder of QMIGO. She is a first-year graduate student at NYU's ITP program. Cindy helps in web programming, web design, user development/experience, SMS messaging, mobile web, database management
and visual branding.<br />
See our contact info below! <br />


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