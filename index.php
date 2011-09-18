<?php 

######################################################### 
# QMIGO Template
######################################################### 
$scriptName = $_SERVER['PHP_SELF']; 
$pageTitle = "Welcome to QMIGO";

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
# Write the header 
######################################################### 
include "introheader.php"; 
?> 

<div id="leftside">
		<div id="smartphone">
			
		<img src="images/home_smartphone.png" />
		</div>
</div>


<div id="content">
	<p class="headline"> People like deals. People like to socialize. </p>
	<p class="blurb"> Qmigo makes it easy for you to keep track of special offers. 
We  alert you to exclusive offers on your smartphone from local businesses 
that you can than share with interested friends on-the-go. </p>
	
</div>



<div id="videocontainer">
<!-- vimeo video -->
<iframe src="http://player.vimeo.com/video/17788300" width="601" height="338" frameborder="0"></iframe>
 </div>

<br />

<div style="clear:both;"></div>
<? 

######################################################### 
# Write end HTML here 
######################################################### 
include "footer.php"; 

?>