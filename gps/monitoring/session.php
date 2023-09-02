<?php
session_start();
if(!empty($_SESSION['GPSUSERID']) and !empty($_SESSION['GPSPRIVILEGE']))
{
	$GPSUSERID=$_SESSION['GPSUSERID'];
	$GPSBRANCHID=$_SESSION['GPSBRANCHID'];
	$GPSPRIVILEGE=$_SESSION['GPSPRIVILEGE'];
}
else
{
//header("location:logout.php");	
echo '<script type="text/javascript"> window.parent.location = "login.php";</script>';
}
?>