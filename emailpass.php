<?php 

######################################################### 
# E-mail member login info 
######################################################### 
$scriptName = $_SERVER['PHP_SELF']; 
$pageTitle = "QMIGO: E-mail Me My Login Info"; 

# Make sure we display errors to the browser 
error_reporting(E_ALL ^ E_NOTICE); 
ini_set('display_errors', 1); 

# Get our DB info 
require "info.php"; 

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
# Get our firstname value 
######################################################### 
$fromEmailLabel = "Your Email"; 
$fromEmailName = "from_email"; 
$fromEmailValue = $_POST[$fromEmailName]; 

$submitName = "dataAction"; 
$submitValue = "Send It"; 

### Status variables ### 
$statusMsg = "";    # Gives response back to user (i.e. "Thank you for your ...") 
$hasErrors = 0;    # Keeps track of whether there are input errors 

######################################################### 
# Check our inputs and perform any DB actions 
######################################################### 
if ($_POST[$submitName]==$submitValue) 
{    # Someone wants to login 

    # Error Checking 
    $noFromEmail = 0; 

    $fromEmailValue = trim($fromEmailValue); 
     
    if (empty($fromEmailValue)) 
    {    $hasErrors = 1; $noFromEmail = 1; $statusMsg = "There were errors in your e-mail submission."; 
    } 

     
    if (!$hasErrors) 
    {    # This is a good submission 
        $fromEmailValueDB = str_replace("'", "''", $fromEmailValue); 
         
        # Look for this information in the DB 
        $SqlStatement = "SELECT firstname, lastname, vendor_email, password FROM qmigo_vendors 
            WHERE vendor_email='$fromEmailValueDB' "; 
             
        # Run the query on the database through the connection 
        $result = mysql_query($SqlStatement,$connection); 
        if (!$result) 
            die("Error " . mysql_errno() . " : " . mysql_error()); 
             
        if ($row = mysql_fetch_array($result,MYSQL_NUM)) 
        {    # Found the user's info 
            $firstname = $row[0]; 
            $lastname = $row[1]; 
            $vendor_email = $row[2]; 
            $password = $row[3]; 
             
        /*    $firstname = $row[0]; 
            $lastname = $row[1]; 
            $username = $row[2]; 
            $password = $row[3];  
        */     
            $subject = "QMIGO: Account Info"; 
            $message = "Dear $firstname, 


Looks like you had some trouble logging into QMIGO.

Here's some help!
Your login info is: 

Email: $vendor_email 
PW: $password 

Sincerely, 
QMIGO
"; 

            # Send the mail 
            mail("$firstname $lastname <$fromEmailValue>",  
        $subject, $message, 
            "From: QMIGO <cindy.wong@nyu.edu>\nX-Mailer: PHP 4.x"); 
             
            $statusMsg = "Your information has been sent to $fromEmailValue!"; 
         
            # Reset the text widgets to accept input once again 
            $fromEmailValue = ""; 
        } 
        else 
        {    $statusMsg = "Sorry. No account was found for $fromEmailValue"; 
        } 
    } 
} 

######################################################### 
# Write the header 
######################################################### 
include "header.php"; 


######################################################### 
# Visual Info 
######################################################### 
$table_header_color = "#99cccc"; 
$table_row_color = "#e8e8e8"; 

?> 

<p> 
Use the form below to have your login information e-mailed to you. 
<p> 

<?php 

# If we put anything in the general status message, then print it 
if (!empty($statusMsg)) 
{     print '<font color="#990000"><b>'.$statusMsg.'</b></font> <p>'; 
} 

?> 

<table border=0 cellpadding=3 cellspacing=1 class="arial13"> 
<tr bgcolor="<?=$table_row_color?>"> 
    <td align="left"> 
    <form action="<?=$scriptName?>" method="POST" enctype="application/x-www-form-urlencoded"> 
    <nobr><b><?=$fromEmailLabel?>:</b></nobr> 
    </td> 
    <td align="left"> 
    <input type="text" name="<?=$fromEmailName?>" value="<?=$fromEmailValue?>" size="32" maxlength="255"> 
     
<?php 

# If we had a problem with the name, show error message here 
if ($hasErrors && $noFromEmail) 
{    print '<br><font color="#ff0000"><b>Please provide a valid email address</b></font>'; 
} 

?> 

    </td> 
</tr> 
<tr><td height="5" colspan="2"></td></tr> 
<tr> 
    <td></td> 
    <td align="left" valign="top"> 
    <input type="submit" name="<?=$submitName?>" value="<?=$submitValue?>"></td></tr> 
</table></form> 

<?php 

######################################################### 
# Write end HTML here 
######################################################### 
include "footer.php"; 

######################################################### 
# Disconnect from the database. 
######################################################### 
mysql_close($connection); 

?>