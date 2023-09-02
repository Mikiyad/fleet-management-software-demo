<?php
include('session.php');
include('phpsqlajax_dbinfo.php');

$did=$_POST['id'];

if($did=="All" and $GPSPRIVILEGE=='admin'){
mysql_query("UPDATE alarm_info SET aAdminAlarm=1");
}
elseif($did=="All" and $GPSPRIVILEGE<>'admin'){
	$sql=mysql_query("SELECT * FROM users_devices WHERE users_id='$GPSUSERID'");
	while($row=mysql_fetch_assoc($sql)){
		mysql_query("UPDATE alarm_info SET aUserAlarm=1 WHERE aDeviceId='".$row['devices_id']."'");
	}
}
else {
mysql_query("UPDATE alarm_info SET aAdminAlarm=1 WHERE aId='$did'");
}



?>

