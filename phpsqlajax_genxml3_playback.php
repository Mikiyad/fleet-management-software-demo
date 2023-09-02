<?php
require("phpsqlajax_dbinfo.php");
$PlayBackDeviceId=$_GET['PlayBackDeviceId'];
$FromDate=$_GET['FromDate'];
$ToDate=$_GET['ToDate'];

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

// Select all the rows in the markers table
$query = "SELECT * FROM positions JOIN devices ON devices.id=positions.device_id JOIN device_type ON device_type.dt_id=devices.device_type 
WHERE device_id='$PlayBackDeviceId' AND time>='$FromDate' AND time<='$ToDate' AND speed>1 ORDER BY positions.time ASC ";
$result = mysql_query($query);
$count=mysql_num_rows($result);

if (!$result) {
  die('Invalid query: ' . mysql_error());
}

header("Content-type: text/xml");

// Start XML file, echo parent node
echo '<markers>';

// Iterate through the rows, printing XML nodes for each
while ($row = @mysql_fetch_assoc($result)){
  // ADD TO XML DOCUMENT NODE
  $LUtime=strtotime($row['time']);
  $now=strtotime(date("Y-m-d H:i:s"));
  $tdiff=round(abs($now - $LUtime) / 60,2);
  echo '<marker ';
  echo 'nrows="' . parseToXML($count) . '" ';
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
}

// End XML file
echo '</markers>';

?>
