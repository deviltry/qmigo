<?php 

######################################################### 
# Vendor Registration - Signup
######################################################### 
$scriptName = $_SERVER['PHP_SELF']; 
$pageTitle = "QMIGO - Vendor Registration"; 

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

######################################################### 
# Initialize a new session or obtain old one if possible 
######################################################### 
require "info_session.php"; 

session_name($mySessionName); 
session_start(); 



######################################################### 
# Go to login page if not logged in 
######################################################### 
#if ($_SESSION["logged-in"]!=1 || $_SESSION["userid"]<1) 
#{    header("Location: socialdrinkster-login.php?err=notloggedin"); 
#    exit; 
#} 
# Get our site info 
require "siteinfo.php"; 

######################################################### 
# Write the header 
######################################################### 
include "header.php"; 



# Submit widget
$submitVenue = "DataAction";

### New site suhmission widgets ###
$textFirstName = "firstname";     
$textFirstNameValue = $_POST[$textFirstName];    # The value of f.name widget 

$textLastName = "lastname";    
$textLastNameValue = $_POST[$textLastName];    # The value of l.name widget 

$textEmailName = "email";    
$textEmailValue = $_POST[$textEmailName];    
if (empty($textEmailValue)) $textEmailValue = "email address";    # Initialize if blank

// Saves stuff into session 
//$_SESSION['email'] = $textEmailValue;

$textPasswordName = "password";    
$textPasswordValue = $_POST[$textPasswordName];    # The value of password widget 

$textVenueName = "venue";    
$textVenueValue= $_POST[$textVenueName];    # The value of venue name widget 

$textVenueStreetAddressName = "streetaddress";    
$textVenueStreetAddressValue= $_POST[$textVenueStreetAddressName];    # The value of venue street widget 

$textVenueCityName = "city";    
$textVenueCityValue= $_POST[$textVenueCityName];    # The value of venue city name widget 

$textVenueStateName = "state";    
$textVenueStateValue= $_POST[$textVenueCityName];    # The value of venue state  widget 

$textVenueZipcodeName = "zipcode";    
$textVenueZipCodeValue = $_POST[$textVenueZipcodeName];    # The value of venue zipcode widget 

$textVenuePhoneName = "phone";    
$textVenuePhoneValue= $_POST[$textVenuePhoneName];    # The value of phone widget 

$submitNewVenueValue = "Submit New Venue";    # This is what it will say on our submit site button


### Status variables ###
$statusMsg = " ";    # Gives response back to user (i.e. "Thank you for your ...")
$hasErrors = 0;    # Keeps track of whether there are input errors

#########################################################
# New Member Submit
#########################################################

if ($_POST[$submitVenue]==$submitNewVenueValue)
{    # Someone submitted new member

    # Error Checking
    $noFirstName = 0;    # Flag that is set if title widget was blank
    $noLastName = 0;    # Flag that is set if title widget was blank
    $noEmail = 0;    # Flag that is set if url widget was blank
    
    // First Name
    $firstname = trim($textFirstNameValue);
    if (empty($firstname))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noFirstName = 1;
        $statusMsg = "There were errors in your site submission - fname.";
    }
    
    // Last Name
  	$lastname= trim($textLastNameValue);
    if (empty($lastname))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noLastName= 1;
        $statusMsg = "There were errors in your site submission - lastname";
    }
    
    // Email
    $vendor_email = trim($textEmailValue);
    if (empty($vendor_email))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noEmail = 1;
        $statusMsg = "There were errors in your site submission - emailvalue";
    }
    
    
  
    // Password
    $password = trim($textPasswordValue);
    if (empty($password))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noPassword = 1;
        $statusMsg = "There were errors in your site submission - pw";
    }
    
    // Venue
    $venue = trim($textVenueValue);
    if (empty($venue))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noVenue = 1;
        $statusMsg = "There were errors in your site submission-venue";
    }
    
      // venue_streetaddress
    $venue_streetaddress = trim($textVenueStreetAddressValue);
    if (empty($venue_streetaddress))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noVenueStreetAddress = 1;
        $statusMsg = "There were errors in your site submission-street";
    }
    
       // venue_city
    $venue_city = trim($textVenueCityValue);
    if (empty($venue_city))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noVenueCity = 1;
        $statusMsg = "There were errors in your site submission-city";
    }
    
     // venue_state
    $venue_state = trim($textVenueStateValue);
    if (empty($venue_state))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noVenueState = 1;
        $statusMsg = "There were errors in your site submission-venue";
    }
    
    // venue_zipcode
    $venue_zipcode = trim($textVenueZipCodeValue);
    if (empty($venue_zipcode))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noVenueZipcode = 1;
        $statusMsg = "There were errors in your site submission-zip";
    }
    
    // venue_phone
    $venue_phone = trim($textVenuePhoneValue);
    if (empty($venue_phone))
    {    # If blank, set general error flag, name error flag, and general error message
        $hasErrors = 1;
        $noVenuePhone = 1;
        $statusMsg = "There were errors in your site submission-phone";
    }
   
        # If we had no errors, we can put it in the database
    if (!$hasErrors)
    {    # Replace any single quotes in our firstname
        $firstnameDB =  str_replace("'", "''", $firstname);
        $lastnameDB = str_replace("'", "''", $lastname); 
        $vendor_emailDB =  str_replace("'", "''", $vendor_email);
        $passwordDB =  str_replace("'", "''", $password);
        $venueDB =  str_replace("'", "''", $venue);
        $venue_streetaddressDB =  str_replace("'", "''", $venue_streetaddress);
        $venue_cityDB =  str_replace("'", "''", $venue_city);
        $venue_stateDB =  str_replace("'", "''", $venue_state);
        $venue_zipcodeDB =  str_replace("'", "''", $venue_zipcode);
        $venue_phoneDB =  str_replace("'", "''", $venue_phone);


        
        
  	
 
        # Create the SQL query // 
        $SqlStatement = "INSERT INTO qmigo_vendors (firstname,lastname, vendor_email, password, venue, venue_streetaddress, venue_city, venue_state, venue_zipcode, venue_phone)
         VALUES ('$firstnameDB','$lastnameDB','$vendor_emailDB','$passwordDB','$venueDB','$venue_streetaddressDB','$venue_cityDB','$venue_stateDB','$venue_zipcodeDB','$venue_phoneDB') ";

		//print "HELLO"; // troubleshooting
		
        # Run the query on the database through the connection
        $result = mysql_query($SqlStatement, $connection);
        if (!$result)
        die("Error " . mysql_errno() . " : " . mysql_error());
        
        $statusMsg = "Thank you for registration at QMIGO!";
        
        # Reset the text widgets to accept input once again for the next submission
        $textTitleValue = "";
        $textEmbedValue = "";
        $textEmailValue = "";
        $textPassWordValue = "";
        $textVenueValue = "";
        $textVenueStreetAddressValue = "";
        $textVenueCityValue = "";
        $textVenueStateValue = "";
        $textVenueZipCodeValue = "";
        $textVenuePhoneValue = "";

    }
}



?> 

<?
#########################################################
# Submission Form: First Name
#########################################################
echo <<<END
<p>
<h1>
Owner? Register below. </h1>
<hr size=1 color="#000000">


<table border=0 cellpadding=3 cellspacing=1">
<tr bgcolor="$table_row_color">
    <td align="left">
    <form action="$scriptName" method="POST" enctype="application/x-www-form-urlencoded">
    <nobr>First Name:</nobr>
    </td>
    <td align="left">
    <input type="text" name="firstname" value="$textFirstNameValue" size="32" maxlength="255">
END;

# If we had a problem with the title, show error message here
if ($hasErrors && $noFirstName)
{    print '<br><font color="#ff0000"><b>Please provide a first name</b></font>';
}

#########################################################
# Submission Form: Last Name
#########################################################
echo <<<END
    </td>
</tr>
<tr bgcolor="$table_row_color">
    <td align="left">
    <nobr>
    Last Name:</nobr>
    </td>
    <td align="left">
    <input type="text" name="lastname" value="$textLastNameValue" size="32" maxlength="255">
END;

# If we had a problem with the title, show error message here
if ($hasErrors && $noLastName)
{    print '<br><font color="#ff0000"><b>Please provide a last name</b></font>';
}


#########################################################
# Submission Form: Email
#########################################################

echo <<<END
    </td>
</tr>
<tr bgcolor="$table_row_color">
    <td align="left">
    <nobr>
    Email:</nobr>
    </td>
    <td align="left">
    <input type="text" name="email" value="$textEmailValue" size="32" maxlength="255">
END;

# If we had a problem with the email, show error message here

if ($hasErrors && $noEmail)
{    print '<br><font color="#ff0000"><b>Please provide an email</b></font>';
}


#########################################################
# Submission Form: Password
#########################################################
echo <<<END
    </td>
</tr>
<tr bgcolor="$table_row_color">
    <td align="left">
    <nobr>
    Password:</nobr>
    </td>
    <td align="left">
    <input type="text" name="password" value="$textPasswordValue" size="32" maxlength="255">
END;

# If we had a problem with the title, show error message here
if ($hasErrors && $noLastName)
{    print '<br><font color="#ff0000"><b>Please provide a password</b></font>';
}


#########################################################
# Submission Form: Venue
#########################################################
echo <<<END
    </td>
</tr>
<tr bgcolor="$table_row_color">
    <td align="left">
    <nobr>
    Venue Name:</nobr>
    </td>
    <td align="left">
    <input type="text" name="venue" value="$textVenueValue" size="32" maxlength="255">
END;

# If we had a problem with the title, show error message here
if ($hasErrors && $noVenue)
{    print '<br><font color="#ff0000"><b>Please provide a venue</b></font>';
}

#########################################################
# Submission Form: Venue Street Address
#########################################################
echo <<<END
    </td>
</tr>
<tr bgcolor="$table_row_color">
    <td align="left">
    <nobr>
    Street Address:</nobr>
    </td>
    <td align="left">
    <input type="text" name="streetaddress" value="$textVenueStreetAddressValue" size="32" maxlength="255">
END;

# If we had a problem with the title, show error message here
if ($hasErrors && $noVenue)
{    print '<br><font color="#ff0000"><b>Please provide a venue street address</b></font>';
}
 

#########################################################
# Submission Form: Venue City
#########################################################
echo <<<END
    </td>
</tr>
<tr bgcolor="$table_row_color">
    <td align="left">
    <nobr>
    City:</nobr>
    </td>
    <td align="left">
    <input type="text" name="city" value="$textVenueCityValue" size="32" maxlength="255">
END;

# If we had a problem with the city, show error message here
if ($hasErrors && $noVenueCity)
{    print '<br><font color="#ff0000"><b>Please provide a city</b></font>';
}

#########################################################
# Submission Form: Venue State
#########################################################
echo <<<END
    </td>
</tr>
<tr bgcolor="$table_row_color">
    <td align="left">
    <nobr>
    State:</nobr>
    </td>
    <td align="left">
    <input type="text" name="state" value="$textVenueCityValue" size="32" maxlength="2">
END;

# If we had a problem with the city, show error message here
if ($hasErrors && $noVenueState)
{    print '<br><font color="#ff0000"><b>Please provide a state</b></font>';
}

#########################################################
# Submission Form: Venue Zipcode
#########################################################
echo <<<END
    </td>
</tr>
<tr bgcolor="$table_row_color">
    <td align="left">
    <nobr>
    Zipcode:</nobr>
    </td>
    <td align="left">
    <input type="text" name="zipcode" value="$textVenueZipCodeValue" size="32" maxlength="5">
END;

# If we had a problem with the city, show error message here
if ($hasErrors && $noVenueState)
{    print '<br><font color="#ff0000"><b>Please provide a zipcode</b></font>';
}

#########################################################
# Submission Form: Venue Phone
#########################################################
echo <<<END
    </td>
</tr>
<tr bgcolor="$table_row_color">
    <td align="left">
    <nobr>
    Phone:</nobr>
    </td>
    <td align="left">
    <input type="text" name="phone" value="$textVenuePhoneValue" size="32" maxlength="10">
END;

# If we had a problem with the city, show error message here
if ($hasErrors && $noVenuePhone)
{    print '<br><font color="#ff0000"><b>Please provide a telephone number with no dashes</b></font>';
}

# Now finish our table, put in our submit widget and end the form
echo <<<END
    </td>
</tr>
<tr><td height="5" colspan="2"></td></tr>
<tr>
    <td></td>
    <td align="left" valign="top">
    <input type="submit" name="$submitVenue" value="$submitNewVenueValue"></td></tr>
</table></form>
END;

# If we put anything in the general status message, then print it
if (!empty($statusMsg))
{     print '<font color="#990000"><b>'.$statusMsg.'</b></font> <p>';
}




######################################################### 
# Write end HTML here 
######################################################### 
include "footer.php"; 



?>