<br /><br />
<div class="footerbox" > 
<!-- style="margin-left: 60px" -->
<? 

# Write the site section navigation here 
$totalSections = count($siteSections); $currentCount = 1; 
foreach($siteSections as $name => $file) 
{    if ($scriptName!=$file) 
    { print '<a href="'.$file.'">'; 
    } 
    else 
    {    print '<b>'; 
    } 
    print $name; 
    if ($scriptName!=$file) 
    { print '</a>'; 
    } 
    else 
    {    print '</b>'; 
    } 
    if ($currentCount!=$totalSections) 
    {    print ' &nbsp;&#183;&nbsp; '; 
    } 
    $currentCount++; 
} 

$year = date("Y");                       


?> 

<br> QMIGO by Brian E. Jones and Cindy Wong.<br />
<?=$year?>  All Rights.  <br>

</div>
</div>

</body>  
</html> 