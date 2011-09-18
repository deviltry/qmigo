
<?PHP
//member404.php

$pageTitle = "QMIGO: ERROR. No Member Found"; 


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


########################################################         
# Get our site info -> menu bar
require "siteinfo.php"; 
	
######################################################### 
# Write the header 
######################################################### 
include "header.php"; 
	
?> 

SORRY! You don't exist.

<br /> 
<? 

######################################################### 
# Write end HTML here 
######################################################### 
include "footer.php"; 

?>


