<html> 
<head> 
<title><?=$pageTitle?></title> 
<link rel="stylesheet" href="qmigo.css" type="text/css" media="all" /> 
<link type="text/css" rel="stylesheet" media="only screen and (max-device-width: 480px)" href="mobile.css" />
<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-23190638-1']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

</script>
</head>  
<body>
	<div id="wrapper">
<div class="titlebox" style="margin-left: 50px"> 
	<img src="images/qmigo_logo2.png" />
</div>      

<div class="navbox" style="margin-left: 60px"> 

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

?> 

</div> 
<div id="container" style="margin-left: 60px";> 
<br />
<br />


