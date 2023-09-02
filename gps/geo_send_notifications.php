<?php
$username="root";
$password="";
$database="traccar";

// Opens a connection to a MySQL server
$connection=mysql_connect (localhost, $username, $password);
if (!$connection) {
  die('Not connected : ' . mysql_error());
}

// Set the active MySQL database
$db_selected = mysql_select_db($database, $connection);
if (!$db_selected) {
  die ('Can\'t use db : ' . mysql_error());
}

$sql=mysql_query("SELECT gf_alarm.f_id,gf_alarm.f_time,gf_alarm.f_gfname,gf_alarm.f_gftype,gf_alarm.f_status,users.email,users.notify,
devices.name,devices.licensepn FROM gf_alarm LEFT JOIN geo_fence ON geo_fence.gfid=gf_alarm.f_gfid JOIN users ON users.id = geo_fence.gfuser_id 
JOIN devices ON devices.id=geo_fence.gfdevice_id WHERE gf_alarm.f_status='0' AND users.notify='1' ");

while($row=mysql_fetch_assoc($sql)){	
////////////////////////////////////////////////////////
	$to = $row['email'];
	$subject = $row['name'] . " - Geo Fence Notifications !";
	$message = $row['name'] ." ". $row['f_gftype'] ." ". $row['f_gfname'] . " at ". $row['f_time'];
	$from = "info@yourdomain.com";
	$headers ="From: " . $from . "\r\n";
	$headers .="Cc: lahirutm@gmail.com"."\r\n";
	mail($to,$subject,$message,$headers);
///////////////////////////////////////////////////////	
mysql_query("UPDATE gf_alarm SET f_status='1' WHERE f_id='".$row['f_id']."'");
}

?>
