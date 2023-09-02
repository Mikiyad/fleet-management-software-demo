<?php
include('phpsqlajax_dbinfo.php');

$DID=$_GET['DID'];

function parseToXML($htmlStr) 
{ 
$xmlStr=str_replace('<','&lt;',$htmlStr); 
$xmlStr=str_replace('>','&gt;',$xmlStr); 
$xmlStr=str_replace('"','&quot;',$xmlStr); 
$xmlStr=str_replace("'",'&#39;',$xmlStr); 
$xmlStr=str_replace("&",'&amp;',$xmlStr); 
return $xmlStr; 
} 

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




header("Content-type: text/xml");

// Start XML file, echo parent node
echo '<markers>';


// Select all the rows in the markers table
$sql = mysql_query("SELECT * FROM devices JOIN positions ON positions.id=devices.latestPosition_id JOIN device_type ON device_type.dt_id=devices.device_type WHERE devices.id='$DID'");
$row = @mysql_fetch_assoc($sql);  
// ADD TO XML DOCUMENT NODE
  $LUtime=strtotime($row['time']);
  $now=strtotime(date("Y-m-d H:i:s"));
  $tdiff=round(abs($now - $LUtime) / 60,2);
  echo '<marker ';
  echo 'id="' . parseToXML($row['id']) . '" ';
  echo 'name="' . parseToXML($row['name']) . '" ';
  echo 'course="' . parseToXML($row['course']) . '" ';
  echo 'dt_image="' . parseToXML($row['dt_image']) . '" ';
  echo 'device_id="' . parseToXML($row['device_id']) . '" ';
  echo 'time="' . parseToXML($row['time']) . '" ';
  echo 'speed="' . parseToXML($row['speed']) . '" ';
  echo 'tdiff="' . parseToXML($tdiff) . '" ';
  echo 'lat="' . $row['latitude'] . '" ';
  echo 'lng="' . $row['longitude'] . '" ';
  echo '/>';


// End XML file
echo '</markers>';

?>
