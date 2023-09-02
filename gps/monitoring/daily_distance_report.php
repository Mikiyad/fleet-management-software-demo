<?php
include('session.php');
include('phpsqlajax_dbinfo.php');
include("includes/savelog.php");

if(isset($_SESSION['TempVehicle'])) unset($_SESSION['TempVehicle']);
if(isset($_SESSION['TempFrom'])) unset($_SESSION['TempFrom']);
if(isset($_SESSION['TempTo'])) unset($_SESSION['TempTo']);

$Vehilce=$_GET['vehicle'];
$FromDate=$_GET['FromDate'];
$ToDate=$_GET['ToDate'];
$FConsumption=$_GET['fconsumption'];
$Submit=$_GET['Submit'];
$Error=1;

if(isset($FromDate)) $FromDate=$FromDate; else $FromDate=date("Y-m-d H:i:s");
if(isset($ToDate)) $ToDate=$ToDate; else $ToDate=date("Y-m-d H:i:s");

if(isset($Submit)){
	if($Vehilce<>0 and ($FromDate<=$ToDate)){
		$_SESSION['TempVehicle']=$Vehilce;
		$_SESSION['TempFrom']=$FromDate;
		$_SESSION['TempTo']=$ToDate;
		$Error=0;
	}
	else {
		if($Vehilce==0){
			$ermsg="Please select vehicle !";
			$Error=1;
		}
		elseif($FromDate>$ToDate){
			$ermsg="Date selection invalid !";
			$Error=1;	
		}
	}	
}

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
<link rel="stylesheet" href="css/smoothness/jquery-ui-1.8.2.custom.css" />
<link href="js/kendo/kendo.common.min.css" rel="stylesheet" />
<link href="js/kendo/kendo.default.min.css" rel="stylesheet" />
<script src="js/kendo/jquery-1.9.1.min.js"></script>
<script src="js/kendo/kendo.all.min.js"></script>

<title>Untitled Document</title>
<style type="text/css">
#draggable { display:none; }
</style>
 
<script type="text/javascript">
$(document).ready(function () {
// create DateTimePicker from input HTML element
	$("#FromDate").kendoDateTimePicker({
		value:new Date(),
		format: "yyyy-MM-dd HH:mm"
	});

		// create DateTimePicker from input HTML element
	$("#ToDate").kendoDateTimePicker({
		value:new Date(),
		format: "yyyy-MM-dd HH:mm"
	});
	
var dateTimePicker = $("#FromDate").data("kendoDateTimePicker");
dateTimePicker.value("<?php echo $FromDate;?>");

var dateTimePicker = $("#ToDate").data("kendoDateTimePicker");
dateTimePicker.value("<?php echo $ToDate;?>");
});

function Download(){
window.open("download_daily_distance_report.php");		
}
</script>


<style type="text/css">
	#map-canvas {position:fixed !important; position:absolute; top:0; left:200px; right:0; bottom:0; }
	.ITitle { font:Georgia, "Times New Roman", Times, serif; font-size:15px; }
	
</style>
</head>

<body style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">

<div style="width:200px; margin:10px 2px 2px 0px; float:left;">
	<?php include('reports_left.php');?>
</div>

<div id="map-canvas">
<div style="width:100%; padding:5px; background:#D7DAFB;">
<table width="100%" border="0">
  <tr>
    <td bgcolor="#000000"><div style="color:#FFF; font-weight:bold; background:url(images/top_bg.gif) repeat-x;">:: GET MILEAGE REPORT</div></td>
  </tr>
  <tr>
    <td><div style="font-size:12px; color:#F00; font-weight:bold;"><?php echo $ermsg;?></div></td>
  </tr>
  <tr>
    <td>
    <form name="frmset" method="get" action="daily_distance_report.php">
    <table width="100%" border="0">
      <tr>
        <td align="left">&nbsp;</td>
        <td align="left">&nbsp;</td>
      </tr>
      <tr>
        <td align="left"><strong>Select Vehicle Name</strong></td>
        <td align="left">
        
        <select name="vehicle">
          <?php
		if(isset($Vehilce) and $Vehilce<>'0')
		{
			$sql=mysql_query("SELECT * FROM devices WHERE id='$Vehilce'");
			$result=mysql_fetch_array($sql);
			echo '<option value="'.$result['id'].'">'.$result['name'].'</option>';
		}
		else echo '<option value="0">- Select Vehicle -</option>';
		   
		if($GPSPRIVILEGE=='admin')
		{
			$sql=mysql_query("SELECT * FROM devices");
			while($row=mysql_fetch_array($sql))
			{
				echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';	
			}
		}
		else
		{
			$sql=mysql_query("SELECT * FROM users_devices JOIN devices ON devices.id=users_devices.devices_id WHERE users_id='$GPSUSERID' ");
			while($row=mysql_fetch_array($sql))
			{
				echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';	
			}
		}			
		?>
        </select>
        
          <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td width="200" align="left"><strong>Select Start Date</strong></td>
        <td align="left">
          <input id="FromDate" name="FromDate" style="width:155px;" required="required" />        
          <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td align="left"><strong>Select End Date</strong></td>
        <td align="left"><input id="ToDate" name="ToDate" style="width:155px;" required="required" />
          <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td align="left"><strong>Fuel Consumption Per 100 km's</strong></td>
        <td align="left"><label for="fconsumption"></label>
          <input name="fconsumption" type="text" id="fconsumption" size="4" 
		  <?php if(isset($FConsumption)) echo 'value="'.$FConsumption.'"'; else echo 'value="10"'; ?> required="required" />
          <font color="#FF0000"> Ltrs *</font></td>
      </tr>
      <tr>
        <td align="left">&nbsp;</td>
        <td align="left">&nbsp;</td>
      </tr>
      <tr>
        <td align="left">&nbsp;</td>
        <td align="left">
		<input type="submit" name="Submit" id="Submit" value="Submit" />
        </td>
      </tr>
    </table></form>
    </td>
  </tr>
</table>

</div>
<br />

<div style="width:100%; padding:5px; background:#D7DAFB;">
  
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="6" bgcolor="#000000"><div style="color:#FFF; font-weight:bold; background:url(images/top_bg.gif) repeat-x;">:: Mileage Report By Date </div></td>
    <td bgcolor="#000000" width="100" align="right">
    <?php
	if(isset($_SESSION['TempVehicle'])){
    echo '<input type="button" id="download" value="Download" onclick="Download();" />';
	}
	?>
    </td>
  </tr>
    <tr>
    <td colspan="7" height="10"></td>
  </tr>
  <tr style="background:#999; font-weight:bold;">
    <td width="50">No.</td>
    <td width="150">Date</td>
    <td width="150">From (Time)</td>
    <td width="150">To (Time)</td>
    <td width="150">Mileage</td>
    <td>Fuel Usage (Ltrs)</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="7" height="3"></td>
  </tr>
  </table>
<div style="overflow: scroll; height:370px; width: 100%; font-size:12px; background:#D7DAFB;">
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
		<td width="150">';
		if($distance>1000) 
		echo number_format(round($distance/1000,3),3)." km"; else echo number_format(round($distance,3),3)." m";
		
		echo '</td>
		<td>'.round(($distance/100000)*$FConsumption,1).'</td>
		<td>&nbsp;</td>
	  </tr>';
	  $TotalDis+=$distance;
	$j++;
	$FromDate=date("Y-m-d",strtotime( '+1 day' , strtotime($FromDate)));
	}
}
?>
</div>
  <tr>
    <td width="50"></td>
    <td width="150" align="right"><b>Total KM : </b></td>
    <td width="150" align="left"><b><?php if($TotalDis>1000) echo number_format(round($TotalDis/1000,3),3)." km"; else echo number_format(round($TotalDis,3),3)." m"; ?></b></td>
    <td width="150"></td>
    <td width="150"></td>
    <td width="150" align="right"><b>Total Ltrs : </b></td>
    <td align="left"><b><?php echo round((($TotalDis/100000)*$FConsumption),1); ?></b></td>
  </tr>
</table>
  
</div>
</div>

</body>
</html>



