<?php

// here's the logout function:



//    logout();


######################################################### 
# Social Drinkster Template
######################################################### 
$scriptName = $_SERVER['PHP_SELF']; 
$pageTitle = "QMIGO: Log Out"; 

# Get our DB info 
require "info.php"; 

# Get our site info 
require "siteinfo.php"; 

# Make sure we display errors to the browser 
error_reporting(E_ALL ^ E_NOTICE); 
ini_set('display_errors', 1); 


######################################################### 
# Initialize for Log-In to See if Log Out Can be Done
######################################################### 
require "info_session.php"; 

session_name($mySessionName); 
session_start(); 



function logout(){
        
        unset($_SESSION['login_trials']);
        unset($_SESSION['user']);
        unset($_SESSION['login_status']);
        session_write_close();
        header("Location: login.php?err=notloggedin"); 

    }


######################################################### 
# Go to login page if not logged in 
######################################################### 
if ($_SESSION["logged-in"]!=1 || $_SESSION["userid"]<1) 
{    header("Location: login.php?err=notloggedin"); 
    exit; 
} 

###########
# LOG OUT
###########
else if ($_SESSION["logged-in"]=1 && $_SESSION["userid"]>1) 
{    header("Location: loggedout.php"); 
    exit; 
     //logout();
} 

######################################################### 
# Write the header 
######################################################### 
include "header.php"; 
?>


<h1>You are now logged out.</h1>
<p> 
Thank you for visiting QMIGO!
<? 

######################################################### 
# Write end HTML here 
######################################################### 
include "footer.php"; 

?>









