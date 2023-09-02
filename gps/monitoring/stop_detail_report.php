<?php
include('session.php');
include('phpsqlajax_dbinfo.php');
include("includes/savelog.php");

$Vehilce=$_GET['vehicle'];
$FromDate=$_GET['FromDate'];
$ToDate=$_GET['ToDate'];

if(isset($FromDate)) $FromDate=$FromDate; else $FromDate=date("Y-m-d H:i:s");
if(isset($ToDate)) $ToDate=$ToDate; else $ToDate=date("Y-m-d H:i:s");

if(isset($Vehilce) and $Vehilce=='0') $ermsg="Please select vehicle and try again !";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="js/kendo/kendo.common.min.css" rel="stylesheet" />
<link href="js/kendo/kendo.default.min.css" rel="stylesheet" />
<script src="js/kendo/jquery-1.9.1.min.js"></script>
<script src="js/kendo/kendo.all.min.js"></script>

<title>Untitled Document</title>
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

function PopupCenter(Lat,Lng) {
	var w=500;
	var h=350;
    // Fixes dual-screen position                       Most browsers      Firefox
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    var left = ((screen.width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((screen.height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open('map_position.php?Lat='+Lat+'&Lng='+Lng, "Map Position", 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }
}

 

///////////////////////////////////////////////////////////////

var geocoder = new google.maps.Geocoder();
//获取地址信息
function GetAddressByGoogle(t, lat, lng) {
    if (!geocoder) {
        geocoder = new google.maps.Geocoder();
    }
    if (lat != 0) {
        var latlng = new google.maps.LatLng(lat, lng);
        geocoder.geocode({ 'latLng': latlng }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    $(t).parent().html(results[1].formatted_address);
                } else {

                }
            } else {

            }
        });
    }
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
    <td bgcolor="#000000"><div style="color:#FFF; font-weight:bold; background:url(images/top_bg.gif) repeat-x;">:: STOP DETAIL REPORT</div></td>
  </tr>
  <tr>
    <td><div style="font-size:12px; color:#F00; font-weight:bold;"><?php echo $ermsg;?></div></td>
  </tr>
  <tr>
    <td>
    <form name="frmset" method="get" action="stop_detail_report.php">
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
        <td width="200" align="left"><strong>Select Start Date &amp; Time</strong></td>
        <td align="left">
          <input id="FromDate" name="FromDate" style="width:155px;" required="required" />        
          <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td align="left"><strong>Select End Date &amp; Time</strong></td>
        <td align="left"><input id="ToDate" name="ToDate" style="width:155px;" required="required" />
          <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td align="left">&nbsp;</td>
        <td align="left">&nbsp;</td>
      </tr>
      <tr>
        <td align="left">&nbsp;</td>
        <td align="left">
		<input type="submit" name="Save" id="Save" value="Submit" />
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
    <td colspan="6" bgcolor="#000000"><div style="color:#FFF; font-weight:bold; background:url(images/top_bg.gif) repeat-x;">:: Stop Detail Report</div></td>
  </tr>
    <tr>
    <td colspan="6" height="10"></td>
  </tr>
  <tr style="background:#999; font-weight:bold;">
    <td width="50">No.</td>
    <td width="150">Date</td>
    <td width="250" align="right">Stoped Duration</td>
    <td width="200" align="right">Latitude , Longitude</td>
    <td align="right">Position Address</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="6" height="3"></td>
  </tr>
</table>
<div style="overflow: auto; height:370px; width: 100%; font-size:12px; background:#D7DAFB;">
<table border="0" width="100%" cellpadding="0" cellspacing="0">
<?php
$j=1;
$i=1;
$sql=mysql_query("SELECT device_id,latitude,longitude,time FROM positions WHERE device_id='$Vehilce' AND time>='$FromDate' AND time <='$ToDate' 
ORDER BY id ASC ");
while($row=mysql_fetch_array($sql))
{
	if($j==1) $time1=strtotime($row['time']); 
	else 
	{
		$time2=strtotime($row['time']);
		$timediff=round(($time2-$time1)/60,0);
		
		if($timediff>=2)
		{
			if($timediff<60) $timediff=round($timediff,0)." mins";
			else $timediff=round(($timediff/60),0)." hours";
			if($i%2==1) $color="#eee"; else $color="#ddd";
			echo '
			<tr height="25" style="background:'.$color.'; margin-top:2px; margin-bottom:2px;">
			<td width="50">'.$i++.'</td>
			<td width="250">'.date("Y-m-d H:i",$time1)." < - > ".date("Y-m-d H:i",$time2).'</td>
			<td width="150" align="right">'.$timediff.'</td>
			<td width="200" align="right"><a onclick="PopupCenter('.$row['latitude'].",".$row['longitude'].');" href="javascript:void(0);">'.round($row['latitude'],5)." , ".round($row['longitude'],5).'</a></td>
			<td align="right"><a onclick="GetAddressByGoogle(this, '.$row['latitude'].', '.$row['longitude'].');" href="javascript:void(0);">Rosolve Address</a></td>
			<td>&nbsp;</td>
			</tr>
			';			
		}
		$time1=$time2;
	}
	
	
$j++;
}

?>

  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</div> 

</div>
</body>
</html>