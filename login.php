<?php 

######################################################### 
# login.php - Login to protected area 
######################################################### 
$scriptName = $_SERVER['PHP_SELF']; 
$pageTitle = "qmigo"; 

# Make sure we display errors to the browser 
error_reporting(E_ALL ^ E_NOTICE); 
ini_set('display_errors', 1); 

# Get our DB info 
require "info.php"; 

######################################################### 
# Initialize a new session or obtain old one if possible 
######################################################### 
require "info_session.php"; 


session_name($mySessionName); 
session_start(); 

######################################################### 
# Go to home page if already logged in 
######################################################### 
if ($_SESSION["logged-in"] && $_SESSION["userid"]>0) 
{    header("Location: home.php"); 
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
     
# Get our site info -> menu bar
require "siteinfo.php"; 
     
######################################################### 
# Get our email value 
######################################################### 

$vendoremailLabel = "Email:";
$vendoremailName = "email";
$vendoremailValue = $_POST[$vendoremailName];

$passwordLabel = "Password:"; 
$passwordName = "password"; 
$passwordValue = $_POST[$passwordName]; 

$submitEmail = "dataAction"; 
$submitValue = "Submit"; 

### Status variables ### 
$statusMsg = "";    # Gives response back to user (i.e. "Thank you for your ...") 
$hasErrors = 0;    # Keeps track of whether there are input errors 

######################################################### 
# Check if we were bounced here 
######################################################### 
$errMsg1 = "notloggedin"; 
if ($_GET["err"]==$errMsg1) 
{    $statusMsg = "Please login to access this page."; 
} 

######################################################### 
# Check our inputs and perform any DB actions 
######################################################### 
if ($_POST[$submitEmail]==$submitValue) 
{    # Someone wants to login 

    # Error Checking 
    $noPassword = $noUseremail = 0; 
    $userid = 0; 
    $vendoremailValue = trim($vendoremailValue); 
    $passwordValue = trim($passwordValue); 
     
    if (empty($vendoremailValue)) 
    {    $hasErrors = 1; $noUsername = 1; $statusMsg = "There were errors in your login info."; 
    } 

    if (empty($passwordValue)) 
    {    $hasErrors = 1; $noPassword = 1; $statusMsg = "There were errors in your login info."; 
    } 
     
    if (!$hasErrors) 
    {    # This is a good submission 
        $vendoremailValueDB = str_replace("'", "''", $vendoremailValue); 
        $passwordValueDB = str_replace("'", "''", $passwordValue); 
         
        # Look for this information in the DB 
        $SqlStatement = "SELECT id, firstname, lastname, vendor_email, last_login FROM qmigo_vendors 
            WHERE vendor_email='$vendoremailValueDB' AND password='$passwordValueDB' "; 
        # print $SqlStatement . "\n"; 
             
        # Run the query on the database through the connection 
        $result = mysql_query($SqlStatement,$connection); 
        if (!$result) 
            die("Error " . mysql_errno() . " : " . mysql_error()); 
             
        if ($row = mysql_fetch_array($result,MYSQL_NUM)) 
        # For Successful login: 
        {    
            $userid = $row[0]; 
            $firstname = $row[1]; 
            $lastname = $row[2]; 
            $vendor_email = $row[3]; 
            $last_login = $row[4]; 
             
            ##############################################################
            # SESSION PARAMETERS - Set our logged in session parameters 
            ##############################################################
            $_SESSION["userid"] = $userid; 
            $_SESSION["firstname"] = $firstname; 
            $_SESSION["lastname"] = $lastname; 
            $_SESSION["vendor_email"] = $vendor_email; 
            $_SESSION["last_login"] = $last_login; 
            $_SESSION["logged-in"] = 1; 
            unset($_SESSION["login-trials"]); 
             
          	
          	echo $vendor_email ; 
          	
            # Update the last_login field 
            $SqlStatement = "UPDATE qmigo_vendors SET last_login=NOW() WHERE id=$userid "; 
            $result = mysql_query($SqlStatement,$connection); 
            if (!$result) die("Error " . mysql_errno() . " : " . mysql_error()); 
             
            # Always the last thing you do before exiting your script 
            mysql_close($connection); 
             
            # Make sure the session data gets written 
            session_write_close(); 
             
            # Now go to the member home page 
           header("Location: home.php"); 
            exit; 
        } 
        else 
        {    # Bad login 
            if (empty($_SESSION["login-trials"])) 
                $_SESSION["login-trials"] = 1; 
            else 
                $_SESSION["login-trials"]++; 
             
            # Send to "e-mail me my password" page if too many bad logins 
            if ($_SESSION["login-trials"]>=3) 
            {    unset($_SESSION["login-trials"]); 
             
                # Always the last thing you do before exiting your script 
                mysql_close($connection); 
                 
                # Make sure the session data gets written 
                session_write_close(); 
                 
                # Now go to the email me my password page 
               header("Location: emailpass.php"); 

                exit; 
            } 
             
            $statusMsg = "Your login information was incorrect.  Please try again. Attempt:  ".$_SESSION["login-trials"] . " of 3"; 
        } 
    } 
} 

######################################################### 
# Visual Info 
######################################################### 
$table_header_color = "#99cccc"; 
$table_row_color = "#e8e8e8"; 

######################################################### 
# Write the header 
######################################################### 
include "header.php"; 

?> 



<?php 
# If we put anything in the general status message, then print it 
if (!empty($statusMsg)) 
{     print '<font color="#990000"><b>'.$statusMsg.'</b></font> <p>'; 
} 

?> 

<table border=0 cellpadding=3 cellspacing=1 class="arial13"> 
<form action="<?=$scriptName?>" method="POST" enctype="application/x-www-form-urlencoded"> 
<tr bgcolor="<?=$table_row_color?>"> 
    <td align="left"> 
    <nobr><b>Email:</b></nobr> 
    </td> 
    <td align="left"> 
    <input type="text" name="<?=$vendoremailName?>" value="<?=$vendoremailValue?>" size="32" maxlength="32"> 
     
<?php 

# If we had a problem with the name, show error message here 
if ($hasErrors && $noUsername) 
{    print '<br><font color="#ff0000"><b>Please provide an email address</b></font>'; 
} 

?> 

    </td> 
</tr> 
<tr bgcolor="<?=$table_row_color?>"> 
    <td align="left"> 
    <nobr><b>Password:</b></nobr> 
    </td> 
    <td align="left"> 
    <input type="password" name="<?=$passwordName?>" value="<?=$passwordValue?>" size="32" maxlength="32"> 
     
<?php 

# If we had a problem with the name, show error message here 
if ($hasErrors && $noPassword) 
{    print '<br><font color="#ff0000"><b>Please provide a password</b></font>'; 
} 

?> 

    </td> 
</tr> 
<tr><td height="5" colspan="2"></td></tr> 
<tr> 
    <td></td> 
    <td align="left" valign="top"> 
    <input type="submit" name="<?=$submitEmail?>" value="<?=$submitValue?>"></td></tr> 
</table></form> 

<br />
Need an account? <a href="registration.php">Register at QMIGO!</a>
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