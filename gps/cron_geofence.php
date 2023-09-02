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


class disCal {
public function GetDistance($lat1, $lng1, $lat2, $lng2) {
		$radLat1 = $lat1*3.1415926535898/180.0;
		$radLat2 = $lat2*3.1415926535898/180.0;
		$a = $radLat1 - $radLat2;
		$b = ($lng1*3.1415926535898/180.0) - ($lng2*3.1415926535898/180.0);
		$s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
		$s = $s * 6378.137; // EARTH_RADIUS;
		$s = round($s * 1000,3); 
		return $s;
	}		
}


$sql=mysql_query("SELECT * FROM geo_fence WHERE gfstatus=''");
while($row=mysql_fetch_assoc($sql)){
	$DeviceId=$row['gfdevice_id'];
	$GFID=$row['gfid'];
	$Radius=$row['gfradius'];
	$GFName=$row['gfname'];
	$sql1=mysql_query("SELECT * FROM devices JOIN positions ON positions.id=devices.latestPosition_id 
	WHERE devices.id='$DeviceId'");
	$result=mysql_fetch_assoc($sql1);
	$GFPosition=$result['latestPosition_id'];
	$GFTime=$result['time']; 
	$lat1=$row['gfcenter_lat']; $lon1=$row['gfcenter_long'];
	$lat2=$result['latitude']; $lon2=$result['longitude'];
	$obj=new disCal();
	$dis=$obj->GetDistance($lat1,$lon1,$lat2,$lon2);
	$TracID=date("Y-m-d H",strtotime($GFTime));
	if($dis>=$Radius){
		if(($dis-$Radius)<=1000 and ($dis-$Radius)>10){
			$query=mysql_query("SELECT * FROM gf_alarm WHERE f_gfid='$GFID' AND f_gfname='$GFName' AND f_gftype='fence-out' 
			AND f_tracid = '$TracID' ");
			$res=mysql_fetch_assoc($query);
			$count=mysql_num_rows($query);
			$Positionid=$res['f_positionid'];
			if($count==0 and $Positionid<>$GFPosition){
				mysql_query("INSERT INTO gf_alarm (f_gfid,f_time,f_gfname,f_positionid,f_tracid,f_gftype) VALUES 
		        ('$GFID','$GFTime','$GFName','$GFPosition','$TracID','fence-out')");
			}
		}
	}
	
	else{
		if(($Radius-$dis)<=1000 and ($Radius-$dis)>10){
			$query=mysql_query("SELECT * FROM gf_alarm WHERE f_gfid='$GFID' AND f_gfname='$GFName' AND f_gftype='fence-in' 
			AND f_tracid = '$TracID' ");
			$res=mysql_fetch_assoc($query);
			$count=mysql_num_rows($query);
			$Positionid=$res['f_positionid'];
			if($count==0 and $Positionid<>$GFPosition){
				mysql_query("INSERT INTO gf_alarm (f_gfid,f_time,f_gfname,f_positionid,f_tracid,f_gftype) VALUES 
				('$GFID','$GFTime','$GFName','$GFPosition','$TracID','fence-in')");
			}
		}
	}

}

?>

