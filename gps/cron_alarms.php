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


$sql=mysql_query("SELECT positions.id,power,speed,time,device_id,name,uniqueId,gname FROM positions JOIN devices ON devices.id=positions.device_id JOIN device_groups ON device_groups.gid=devices.GroupId WHERE other LIKE '%alarm%' AND AlarmStatus='0' LIMIT 50");
while($row=mysql_fetch_array($sql)){

if($row['power']==0) $Alarm="Cut Off Alarm";
else if($row['power']==1) $Alarm="Low Power Alarm";
else if($row['speed']>=32.4) $Alarm="Over Speed Alarm";
else $Alarm="Vibration Alarm";

mysql_query("INSERT INTO alarm_info (`aDeviceID`,`aName`,`aGroup`,`aImei`,`aTime`,`aAlarm`) VALUES 
('".$row['device_id']."','".$row['name']."','".$row['gname']."','".$row['uniqueId']."','".$row['time']."','$Alarm')");
mysql_query("UPDATE positions SET AlarmStatus=1 WHERE id='".$row['id']."'");
}

?>
