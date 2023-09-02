<?php
include('session.php');
include('phpsqlajax_dbinfo.php');

$VID=$_GET['deviceid'];
$mapLat=$_GET['mapLat'];
$mapLong=$_GET['mapLong'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
    <style type="text/css">
	#map-canvas {position:fixed !important; position:absolute; top:0; left:250px; right:0; bottom:0; }

	.style1 {
	color: #FF0000;
	font-weight: bold;
	text-decoration:blink;
	font-size:11px;
}
    </style>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=drawing"></script>
<script src="js/jquery.js"></script>
<script type="text/javascript">
var map; var drawingManager;

function initialize() {
    var mapOptions = {
        center: new google.maps.LatLng(<?php echo $mapLat;?>, <?php echo $mapLong;?>),
        zoom: 14,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	
    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.CIRCLE,
        drawingControl: false,

        circleOptions: {
			fillColor: '#ffff00',
			fillOpacity: 0.35,
			strokeOpacity: 0.6,
			strokeWeight: 3,
			clickable: false,
			editable: true,
			draggable:true,
			zIndex: 1

        }
    });

    drawingManager.setMap(map);

    google.maps.event.addListener(drawingManager, 'circlecomplete', function(circle) {
		
		showCDet();
		google.maps.event.addListener(circle, 'radius_changed', function () {
			showCDet();
		});
		google.maps.event.addListener(circle, 'center_changed', function () {
			showCDet();
		});
		
		function showCDet(){
			radius=circle.getRadius();
			cpoint=circle.getCenter();
			$('#save').show();
		}
		
		drawingManager.setDrawingMode(null);
	 });
}
google.maps.event.addDomListener(window, 'load', initialize);

function showData(){
 var geoname=prompt("Please enter GEO-FENCE name here !","");
	 if(geoname!="" && geoname!=null){
			$.ajax({
				type: "POST",
				data: { vid:<?php echo $VID; ?>, center:cpoint, radius:radius, fname:geoname },
				url: "add_geofence.php",
				success: function (data) {  
					if(data=="Success"){
						location.reload();
					}
					else{
						document.getElementById("msg").innerHTML=data;
					}         					
				},
				error: function () {
					alert("Error occured. Try again !");
				}
			});
	 }
	 else{
		 if(geoname!=null){
		 	alert("Geo-Fence Name required !");
		 }
	 } 
}

function deleteFence(did){
	$.ajax({
		type: "POST",
		data: { del:"yes", did:did },
		url: "add_geofence.php",
		success: function (data) {  
			if(data=="Success"){
				location.reload();
			}
			else{
				document.getElementById("msg").innerHTML=data;
			}         					
		},
		error: function () {
			alert("Error occured. Try again !");
		}
	});
}
</script>
</head>

<body>
  <div style="width:245px; height:700px; overflow:scroll; margin:0px 2px 2px 0px; float:left;">
  <div style="font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#00CC33;">
  <b>Geo Fence Service</b><br />(Virtual Boundary)
  </div>
  <div>
  <table width="100%" border="0" style="font-family:Arial, Helvetica, sans-serif; font-size:10px; color:#333333;">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5"><strong>Your current Geo Fences </strong></td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="25"><div align="left"><b>No.</b></div></td>
    <td><div align="left"><b>Geo Name</b></div></td>
    <td width="50"><div align="center"><b>Center</b></div></td>
    <td width="30"><div align="right"><b>Radius</b></div></td>
    <td width="20"><div align="center" class="style1">&nbsp;</div></td>
  </tr>
  <tr>
    <td bgcolor="#999999" colspan="5" height="1"></td>
  </tr>
  <?php
  $sql=mysql_query("SELECT * FROM geo_fence WHERE gfuser_id='$GPSUSERID' AND gfdevice_id='$VID' AND gfstatus=''");
  $i=1;
  while($row=mysql_fetch_assoc($sql)){
  echo '<tr>
    <td><div align="left">'.$i.'</div></td>
    <td><div align="left">'.$row['gfname'].'</div></td>
    <td>
		<div align="center" id="gf'.$i.'"><img src="images/eye.png" alt="view place" width="15" height="15" /></div>
		<input type="hidden" id="ct'.$i.'" value="new google.maps.LatLng('.$row['gfcenter_lat'].",".$row['gfcenter_long'].')">
		<input type="hidden" id="rad'.$i++.'" value="'.$row['gfradius'].'">
	</td>
    <td><div align="right">'.round($row['gfradius'],0).'m</div></td>
    <td><div align="center" style="cursor:pointer; color:#ff0000;" onClick="deleteFence('.$row['gfid'].')">X</div></td>
  </tr>';
  }
  ?>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><div align="right"></div></td>
    <td><div align="center"></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5"><div class="style1" id="msg"></div></td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5">Note : To add a new Geo-Fence area, please draw a circle on the map and save it.</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>

  </div>
  <input type="button" id="save" value="Save Geo-Fence" onclick="showData();" style="display:none;" />
  </div>
    
    <div id="map-canvas"></div>	
</body>
</html>
