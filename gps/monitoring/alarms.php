<?php
include('session.php');
include('phpsqlajax_dbinfo.php');

if($GPSPRIVILEGE=='admin'){
$sql=mysql_query("SELECT * FROM alarm_info WHERE aAdminAlarm=0 ORDER BY aTime");
	
	while($row=mysql_fetch_assoc($sql)){
		if($row['power']==0) $Alarm="Cut Off Alarm";
		else if($row['power']==1) $Alarm="Low Power Alarm";
		else if($row['speed']>=32.4) $Alarm="Over Speed Alarm";
		else $Alarm="Vibration Alarm";
	
		echo '<div id="main'.$row['aId'].'" class="mainid" style="border:1px solid #dddddd; height:20px;">
			<div id="DName">'.$row['aName'].'</div>
			<div id="DName">'.$row['aGroup'].'</div>
			<div id="DIMEI">'.$row['aImei'].'</div>
			<div id="DAlarm"><i>'.$row['aAlarm'].'</i></div>
			<div id="DTime">'.$row['aTime'].'</div>		
			<div id="'.$row['aId'].'" class="ClearAlarm" onClick="deleteOne('.$row['aId'].');"></div>
		</div>';
	}
}
else {
$sql=mysql_query("SELECT * FROM users_devices WHERE users_id='$GPSUSERID'");
	while($row=mysql_fetch_assoc($sql)){
		$sqlA=mysql_query("SELECT * FROM alarm_info WHERE aDeviceId='".$row['devices_id']."' AND aUserAlarm=0 ORDER BY aTime");
		while($rowA=mysql_fetch_assoc($sqlA)){
			if($rowA['power']==0) $Alarm="Cut Off Alarm";
			else if($rowA['power']==1) $Alarm="Low Power Alarm";
			else if($rowA['speed']>=32.4) $Alarm="Over Speed Alarm";
			else $Alarm="Vibration Alarm";
		
			echo '<div id="main'.$rowA['aId'].'" class="mainid" style="border:1px solid #dddddd; height:20px;">
				<div id="DName">'.$rowA['aName'].'</div>
				<div id="DName">'.$rowA['aGroup'].'</div>
				<div id="DIMEI">'.$rowA['aImei'].'</div>
				<div id="DAlarm"><i>'.$rowA['aAlarm'].'</i></div>
				<div id="DTime">'.$rowA['aTime'].'</div>		
				<div id="'.$rowA['aId'].'" class="ClearAlarm" onClick="deleteOne('.$rowA['aId'].');"></div>
			</div>';
		}
	}
}
?>
