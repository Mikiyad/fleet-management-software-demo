<?php
require("phpsqlajax_dbinfo.php");
session_start();

$DEVICEID=$_GET['DID'];    // Selected device id
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Remove Markers</title>
<link href="/maps/documentation/javascript/examples/default.css" rel="stylesheet">
<style type="text/css">
#map-canvas {position:fixed !important; position:absolute; top:0; left:0; right:0; bottom:0; }

.ITitle { font:Georgia, "Times New Roman", Times, serif; font-size:15px; }  

#btn-div { width:180px; float:left; margin:2px 2px 2px 5px; }

#FromDate-div { width:220px; float:left; font-size:12px; }

#ToDate-div { width:200px; float:left; font-size:12px; }

#playspeed { width:300px; float:left; margin:2px; font-size:12px;}

#btn-load { width:100px; float:left; margin:2px; }
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
if(k>10) { k=1; timeout(); }
},1000);
}
myFunction();	
		
// In the following example, markers appear when the user clicks on the map.
// The markers are stored in an array.
// The user can then click an option to hide, show or delete the markers.
var map;
var markers = [];
var poly=[]; // for drawing path

function initialize() {
  var haightAshbury = new google.maps.LatLng(-15.785894, 35.006425);
  var mapOptions = {
    zoom: 18,
    center: haightAshbury,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
// for poly lines for making paths
  poly = new google.maps.Polyline({ strokeColor: '#22DD22', strokeOpacity: 0.6, strokeWeight: 4 });
  poly.setMap(map);
	  
  
timeout();	  
}


function timeout() { 
 deleteMarkers();
 downloadUrl("phpsqlajax_genxml3_tracking.php?DID=<?php echo $DEVICEID;?>", function(data) {
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
  if(speed>0) var status=Math.round(speed*1.852) + " moving"; else var status="stop"; // moving status according to speed
  
  var html ='<div style="margin:1px !important; font-size:12px;">' + "<b>" + name + "</b> <br/>Angle : " + course + "<br/> Last Updated : " + time + "<br/> Acc : " + Acc + "<br/> Speed : " + Math.round(speed*1.852) + " kmph<br/>Lat : " + Lat + "<br/>Lng : " + Lng + "<br/>" + '</div>' ;
  
 
	map.panTo(new google.maps.LatLng(markers[i].getAttribute("lat"),markers[i].getAttribute("lng")));

			
 
			
// Add a marker when click on the map
    addMarker(point,dt_image,course,html,name,i);
	GetAddressByGoogle('divMarkerAddress', Lat, Lng);
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
	
//////////////////////////////////////////////////////////
  var path = poly.getPath();
  path.push(location);			
	
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


	  infoWindow.setContent(html);
        infoWindow.open(map, marker);


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
    <div id="disSecond" style="position:absolute; width:800px; height:20px; background-color:White; margin-left:0; font-size:13px; margin-top:-10px; 
    line-height:150%; z-index:999;">
    Refresh after <span id="spanSecond" style="color:Red;">#</span> seconds! &nbsp; 
    <span id="divMarkerAddress"></span>
    </div>         
		<div id="map-canvas"></div>
  </body>
</html>

