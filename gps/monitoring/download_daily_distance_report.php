<?php
include('session.php');
include('phpsqlajax_dbinfo.php');
include("includes/savelog.php");

header("Content-Type: application/doc");
header("Content-Disposition: attachment; filename=".$_SESSION['TempVehicle']."-".$_SESSION['TempFrom']." To ".$_SESSION['TempTo'].".xls");
header("Pragma: no-cache");
header("Expires: 0");

$Vehilce=$_SESSION['TempVehicle'];
$FromDate=$_SESSION['TempFrom'];
$ToDate=$_SESSION['TempTo'];

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
<title>Daily Distance Report</title>
<style type="text/css">
	#map-canvas {position:fixed !important; position:absolute; top:0; left:200px; right:0; bottom:0; }
	.ITitle { font:Georgia, "Times New Roman", Times, serif; font-size:15px; }
	
</style>
</head>

<body style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
  <div style="width:100%; padding:5px; background:#D7DAFB;">
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="7" bgcolor="#000000"><div style="color:#FFF; font-weight:bold; background:url(images/top_bg.gif) repeat-x;">:: Mileage Report By Date </div></td>
  </tr>
    <tr>
    <td colspan="7" height="10"></td>
  </tr>
  <tr style="background:#999; font-weight:bold;">
    <td width="50">No.</td>
    <td width="150" align="right">Date</td>
    <td width="150" align="right">From (Time)</td>
    <td width="150" align="right">To (Time)</td>
    <td width="150" align="right">Mileage</td>
    <td align="right">Fuel Usage (Ltrs)</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="7" height="3"></td>
  </tr>
  </table>
<div style="width: 100%; font-size:12px; background:#D7DAFB;">
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php
$j=1;
$TotalDis=0;
if($Error==0) {
	while($FromDate<=$ToDate)
	{
		$GetFDT=new DateTime($FromDate);
		$FDate=$GetFDT->format("Y-m-d");
		
		$NxtDate=date("Y-m-d H:i:s",strtotime( '+1 day' , strtotime($FDate)));
		if($j==1) {
			if($NxtDate>$ToDate) {
				$CalFromDate=$FromDate; 
				$CalToDate=$ToDate; // To date for calculation
			}
			else {
				$CalFromDate=$FromDate;
				$CalToDate=$FDate." 23:59:59";
			}
		}
		else {
			if($NxtDate>$ToDate){
				$CalFromDate=$FDate." 00:00:00";
				$CalToDate=$ToDate;
			}
			else{
				$CalFromDate=$FDate." 00:00:00";
				$CalToDate=$FDate." 23:59:59";
			}
		}		
		
		$lat1=0; $lat2=0; $lon1=0; $lon2=0;
		$i=0;
		$distance=0;
		$sql=mysql_query("SELECT * FROM positions WHERE device_id='$Vehilce' AND time >='".$CalFromDate.
		"' AND time<='".$CalToDate."' AND speed >'1'  ORDER BY id ASC ");
		while($row=mysql_fetch_array($sql))
		{
			if($i==0)
			{
				$lat1=$row['latitude'];
				$lon1=$row['longitude'];
			}
			if($i>=1)
			{
				$lat2=$row['latitude'];
				$lon2=$row['longitude'];		
			
				$obj=new test();
				$dis=$obj->GetDistance($lat1,$lon1,$lat2,$lon2);
				if($dis>1000) $dis=0;
				$distance+=$dis;
					
				$lat1=$lat2;
				$lon1=$lon2;	
			}		
			$i++;		
		}
		if($j%2==1) $color="#ddd"; else $color="#eee";
		
		echo '
		<tr height="25" style="background:'.$color.'; margin-top:2px; margin-bottom:2px;">
		<td width="50">'.$j.'</td>
		<td width="150">'.substr($FromDate,0,10).'</td>
		<td width="150">'.substr($CalFromDate,0,16).'</td>
		<td width="150">'.substr($CalToDate,0,16).'</td>
		<td width="150" align="right">';
		if($distance>1000) 
		echo number_format(round($distance/1000,3),3)." km"; else echo number_format(round($distance,3),3)." m";
		
		echo '</td>
		<td align="right">'.round(($distance/100000)*10,1).'</td>
		<td>&nbsp;</td>
	  </tr>';
	  $TotalDis+=$distance;
	$j++;
	$FromDate=date("Y-m-d",strtotime( '+1 day' , strtotime($FromDate)));
	}
}
?>
  <tr>
    <td width="50"></td>
    <td width="150" align="right"><b>Total KM : </b></td>
    <td width="150" align="left"><b><?php if($TotalDis>1000) echo number_format(round($TotalDis/1000,3),3)." km"; else echo number_format(round($TotalDis,3),3)." m"; ?></b></td>
    <td colspan="3"><b>Total Ltrs : (Assumed FConsumption: 10L/100KM)</b></td>
    <td align="right"><b><?php echo round((($TotalDis/100000)*10),1); ?></b></td>
  </tr>
</table>
  
</div>

</body>
</html>



