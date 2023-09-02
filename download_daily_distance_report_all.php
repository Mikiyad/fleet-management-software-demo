<?php
include('session.php');
include('phpsqlajax_dbinfo.php');
include("includes/savelog.php");

header("Content-Type: application/doc");
header("Content-Disposition: attachment; filename=".$_SESSION['TempFrom']." To ".$_SESSION['TempTo'].".xls");
header("Pragma: no-cache");
header("Expires: 0");

$FromDate=$_SESSION['TempFrom'];
$ToDate=$_SESSION['TempTo'];
$FConsumption=10;

class test {
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Daily Distance Report All</title>
<style type="text/css">
	#map-canvas {position:fixed !important; position:absolute; top:0; left:200px; right:0; bottom:0; }
	.ITitle { font:Georgia, "Times New Roman", Times, serif; font-size:15px; }
	
</style>
</head>

<body style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">

<div id="map-canvas">
  <div style="width:100%; padding:5px; background:#D7DAFB;">
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="5" bgcolor="#000000"><div style="color:#FFF; font-weight:bold; background:url(images/top_bg.gif) repeat-x;">:: Mileage Report By Date | <?php if(isset($FromDate) and isset($ToDate)) echo "From : ".$FromDate." To : ".$ToDate;?></div></td>
  </tr>
    <tr>
    <td colspan="5" height="10"></td>
  </tr>
  <tr style="background:#999; font-weight:bold;">
    <td width="150">Vehicle</td>
    <td width="150" align="right">Mileage</td>
    <td width="150" align="right">Fuel Usage (Ltrs)</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5" height="3"></td>
  </tr>
  </table>
<div style="width: 100%; font-size:12px; background:#D7DAFB;">
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php
if(isset($FromDate) and isset($ToDate)){
$sqldevices=mysql_query("SELECT * FROM users_devices JOIN devices ON devices.id=users_devices.devices_id WHERE users_id='$GPSUSERID' ");
$k=1;
while($rowdevices=mysql_fetch_array($sqldevices)){
$j=0;
$TotalDis=0;	
$lat1=0; $lat2=0; $lon1=0; $lon2=0;
$distance=0;
$sql1=mysql_query("SELECT * FROM positions WHERE device_id='".$rowdevices['id']."' AND time>='".$FromDate." 00:00:00"."' AND time <='".$ToDate." 23:59:59"."' AND speed >'1' ORDER BY id ASC "); 
while($row1=mysql_fetch_array($sql1))
{
	if($j==0)
	{
		$lat1=$row1['latitude'];
		$lon1=$row1['longitude'];
	}
	if($j>=1)
	{
		$lat2=$row1['latitude'];
		$lon2=$row1['longitude'];		
	
		$obj=new test();
		$distance=$obj->GetDistance($lat1,$lon1,$lat2,$lon2);
		if($distance>1000) $distance=0;		
		$TotalDis+=$distance;	
		
		$lat1=$lat2;
		$lon1=$lon2;	
	}		
$j++;
}
if($k%2==1) $color="#ddd"; else $color="#eee";
echo '<tr style="background:'.$color.';">
    <td width="150"  align="right">'.$rowdevices['name'].'</td>
    <td width="150"  align="right">';
	if($TotalDis>1000) 
	echo number_format(round($TotalDis/1000,3),3)." km"; else echo number_format(round($TotalDis,3),3)." m";
	 
	echo '</td>
    <td width="150"  align="right">';
	echo round((($TotalDis/100000)*$FConsumption),1);
	echo '</td>
    <td></td>
    <td></td>
  </tr>';
  $k++;
}
}
?>
<td colspan="5">Assumed FConsumption 10L/100KM</td>
</table>
  
</div>
</div>

</body>
</html>


