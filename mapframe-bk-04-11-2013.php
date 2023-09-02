<?php
include('session.php');
include('phpsqlajax_dbinfo.php');

$default=1;
if(isset($_GET['num']))
$num=$_GET['num'];
else
$num=1;

if(isset($_GET['cLat']) and isset($_GET['cLong']))
{
$cLat=$_GET['cLat'];
$cLong=$_GET['cLong'];

$zvalue=18;
$default=0;
}
else
{
$cLat=-15.785894;
$cLong=35.006425;
$zvalue=12;
}
 ?>

<!DOCTYPE html>
<html>
  <head>
    <title>Remove Markers</title>
    <link href="/maps/documentation/javascript/examples/default.css" rel="stylesheet">
    <style type="text/css">
	#map-canvas {position:fixed !important; position:absolute; top:0; left:200px; right:0; bottom:0; }
	.ITitle { font:Georgia, "Times New Roman", Times, serif; font-size:15px; }
	
.vname {
	background:url(images/bg/u_online.gif) left no-repeat; 
	width:100%; 
	margin-bottom:3px; 
	font-size:12px; 
	font-family:Arial, Helvetica, sans-serif; 
	float:left; 
}
.vname-offline {
	background:url(images/bg/u_offline.gif) left no-repeat; 
	width:100%; 
	margin-bottom:3px; 
	font-size:12px; 
	font-family:Arial, Helvetica, sans-serif; 
	float:left; 
}

.vname a { text-decoration:none; color:#22cc22; margin:1px 1px 2px 18px; }
.vname-offline a { text-decoration:none; color:#cccccc; margin:1px 1px 2px 18px; }

.vname a:hover { text-decoration:underline; color:#22cc22; margin:1px 1px 2px 18px; }
   .labels {
		color: red;
		background-color: white;
		font-family: "Lucida Grande", "Arial", sans-serif;
		font-size: 10px;
		padding:1px;
		text-align: center;
		height: 13px;     
		border: 1px solid black;
		-moz-border-radius: 3px;
		-webkit-border-radius: 3px;
		border-radius: 3px;
		white-space: nowrap;
   }

	</style>
    
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
    <script type="text/javascript" src="js/markerwithlabel.js"></script>
    <script>
// COUNTER 
var k=1;
function myFunction()
{
setInterval(function(){
document.getElementById('spanSecond').innerHTML=10-k;
k++;
if(k>10){ k=1; timeout(); }
},1000);
}
myFunction();

var num=<?php echo $num;?>;	
// In the following example, markers appear when the user clicks on the map.
// The markers are stored in an array.
// The user can then click an option to hide, show or delete the markers.
var map;
var markers = [];

function initialize() {
  var haightAshbury = new google.maps.LatLng(-15.785894, 35.006425);
  var mapOptions = {
    zoom: <?php echo $zvalue; ?>,
    center: haightAshbury,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
	  
  
timeout();	  
}


function timeout() { 

deleteMarkers();
 downloadUrl("phpsqlajax_genxml3.php?UID=<?php echo $GPSUSERID;?>", function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");
		
	
		 for (var i = 0; i < markers.length; i++) {	
			  var name = markers[i].getAttribute("name");
			  var course = markers[i].getAttribute("course");
			  var dt_image = markers[i].getAttribute("dt_image");
			  var time = markers[i].getAttribute("time");
			  var speed = markers[i].getAttribute("speed");
              var label = markers[i].getAttribute("device_id");
			  var tdiff = markers[i].getAttribute("tdiff");
			var Lat=parseFloat(markers[i].getAttribute("lat"));
			var Lng=parseFloat(markers[i].getAttribute("lng")); 
			var point = new google.maps.LatLng(Lat,Lng);
		
  if(tdiff>=2)
  { 
  var Acc="Off";
  speed=0; 
  }
  else var Acc="On"; 
  
  // data sending stop before 2 min => Acc off
  if(speed>1) var status=Math.round(speed*1.852) + " moving"; else var status="stop"; // moving status according to speed
  
  var html ='<div style="margin:1px !important; font-size:12px;">' + "<b>" + name + "</b> <br/>Angle : " + course + "<br/> Last Updated : " + time + "<br/> Acc : " + Acc + "<br/> Speed : " + Math.round(speed*1.852) + " kmph<br/>Lat : " + Lat + "<br/>Lng : " + Lng + "<br/>" + "<a href=tracking.php?DID=" + label + ">Tracking</a>" + " " + "<a href=playback.php?PlayBackDeviceId=" + label + "&Reset=1>Play Back</a>" + '</div>' ;
  
 document.getElementById("status"+label).innerHTML=status; // show status in div tag
 
  if(num==(i+1))
	{ 
		map.panTo(new google.maps.LatLng(markers[i].getAttribute("lat"),markers[i].getAttribute("lng")));
	}
			
 
			
// Add a marker when click on the map
    addMarker(point,dt_image,course,html,name,i);
	if(num==i+1)
	  {
		 GetAddressByGoogle('divMarkerAddress', Lat, Lng);
	  }
	
	        }// loop end here	  
		
		
   });   // downloadUrl end here 
}

// Add a marker to the map and push to the array.
function addMarker(location,image,course,html,name,i) {
	if(course<=22.5)
		{
			image = "images/"+image+"0.png"; // 0 angle img
		}
		else if (course<=67.5)
		{
			image = "images/"+image+"45.png"; // 45 angle img	
		}
		else if (course<=112.5)
		{
			image = "images/"+image+"90.png"; // 90 angle img	
		}
		else if (course<=157.5)
		{
			image = "images/"+image+"135.png"; // 135 angle img	
		}
		else if (course<=202.5)
		{
			image = "images/"+image+"180.png"; // 180 angle img	
		}		
		else if (course<=247.5)
		{
			image = "images/"+image+"225.png"; // 225 angle img	
		}
		else if (course<=292.5)
		{
			image = "images/"+image+"270.png"; // 270 angle img
		}		
		else
		{
			image = "images/"+image+"315.png"; // 315 angle img	
		}
	
		
	
  var marker = new MarkerWithLabel({
    position: location,
	icon:image,
	labelContent: name,
	labelAnchor: new google.maps.Point(22, 0),
	labelClass: "labels", // the CSS class for the label
	labelStyle: {opacity: 0.75},
    map: map	
  });
  
var infoWindow = new google.maps.InfoWindow({maxWidth:300});
google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
/*
  if(num==i+1)
  {
	  infoWindow.setContent(html);
        infoWindow.open(map, marker);
  }
*/

  markers.push(marker); 
 
}

// Sets the map on all markers in the array.
function setAllMap(map) {
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(map);
  }
}

// Deletes all markers in the array by removing references to them.
function deleteMarkers() {
   setAllMap(null);
  markers = [];
}
google.maps.event.addDomListener(window, 'load', initialize);






    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request, request.status);
        }
      };


      request.open('GET', url, true);
      request.send(null);
	  
    }
    function doNothing() {}



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
                    document.getElementById(t).innerHTML=results[1].formatted_address;
                } else {

                }
            } else {

            }
        });
    }
}

</script>


  </head>
  <body>
<div id="disSecond" style="position:absolute; width:800px; height:20px; background-color:White; margin-left:195px; font-size:13px; margin-top:-10px; line-height:150%; z-index:999;">
Refresh after <span id="spanSecond" style="color:Red;">#</span> seconds! &nbsp; 
<span id="divMarkerAddress"></span>
</div>
  
  <div style="width:200px; margin:10px 2px 2px 0px; float:left;">

<?php
		$sql=mysql_query("SELECT * FROM users_devices JOIN devices ON devices.id=users_devices.devices_id JOIN positions ON 
		positions.id=devices.latestPosition_id WHERE users_id='$GPSUSERID' ORDER BY GroupId,devices.id ASC");
			$i=1;
			
			while($result=mysql_fetch_array($sql))
			{
				$LUtime=strtotime($result['time']);
				$now=strtotime(date("Y-m-d H:i:s"));
				$tdiff=abs($now - $LUtime) / 60;
				$daydiff=($tdiff/60)/24;
				if($daydiff>2) 
				{
				echo '<div class="vname-offline">
					<a href="mapframe.php?cLat='.$result['latitude'].'&cLong='.$result['longitude'].'&num='.$i++.'">'.$result['name'].'</a>
					<div id="status'.$result['device_id'].'" style="float:right; width:80px; color:#cccccc;"></div></div>';
				
				}
				else 
				
				{
					echo '<div class="vname" style="background:url(images/bg/u_online.gif) left no-repeat;">
					<a href="mapframe.php?cLat='.$result['latitude'].'&cLong='.$result['longitude'].'&num='.$i++.'">'.$result['name'].'</a>
					<div id="status'.$result['device_id'].'" style="float:right; width:80px; color:#22cc22;"></div></div>';
				}				
			}
        ?>

  </div>
  
  
  
    <div id="map-canvas"></div>
  </body>
</html>

