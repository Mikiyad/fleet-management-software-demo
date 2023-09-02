<?php
require("phpsqlajax_dbinfo.php");
session_start();
$Reset=$_GET['Reset'];
$Load=$_GET['LoadData'];
$ShowPoints=$_GET['ShowPoints'];

$sql=mysql_query("SELECT * FROM users_devices JOIN devices ON devices.id=users_devices.devices_id JOIN positions ON positions.id=devices.latestPosition_id WHERE users_id='$GPSUSERID' LIMIT 1");
$result=mysql_fetch_array($sql);

$cLat=$result['latitude'];        // Default latitude
$cLong=$result['longitude'];      // Default longitude
$zvalue=15;                       // Default zoom value


$PlayBackDeviceId=$_GET['PlayBackDeviceId'];    // Selected device id

if(isset($Load))
{
	$FromDate=$_GET['FromDate'];                // Selected playback start date/time
	$ToDate=$_GET['ToDate'];                    // Selected playback stop date/time
	$PlayBackSpeed=$_GET['PlayBackSpeed'];      // Selected palyback speed
}
else
{
	$FromDate=date("Y-m-d H:i");   // Default playback start date/time
	$ToDate=date("Y-m-d H:i");     // Default playback stop date/time	
	$PlayBackSpeed=300;            // Default palyback speed
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Remove Markers</title>
<link href="/maps/documentation/javascript/examples/default.css" rel="stylesheet">
<style type="text/css">
#map-canvas {position:fixed !important; position:absolute; top:50px; left:0; right:0; bottom:0; }

.ITitle { font:Georgia, "Times New Roman", Times, serif; font-size:15px; }  

#btn-div { width:180px; float:left; margin:2px 2px 2px 5px; }

#FromDate-div { width:220px; float:left; font-size:12px; }

#ToDate-div { width:200px; float:left; font-size:12px; }

#playspeed { width:300px; float:left; margin:2px; font-size:12px;}

#btn-load { width:100px; float:left; margin:2px; }

</style>   
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<link href="js/kendo/kendo.common.min.css" rel="stylesheet" />
<link href="js/kendo/kendo.default.min.css" rel="stylesheet" />
<script src="js/kendo/jquery-1.9.1.min.js"></script>
<script src="js/kendo/kendo.all.min.js"></script> 
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



function Timer(callback, delay) {
    var timerId, start, remaining = delay;

    this.pause = function() {
        window.clearTimeout(timerId);
        remaining -= new Date() - start;
    };

    this.resume = function() {
        start = new Date();
        timerId = window.setTimeout(callback, remaining);
    };

    this.resume();
}

////////////////////////////////////////////////////
function parseMyDate(dateString) {
    var parts = dateString.split(/[ \-:\.]/g);

    parts[1] -= 1;
    return new Date(Date.UTC.apply(undefined, parts)).getTime();
}
	
// In the following example, markers appear when the user clicks on the map.
// The markers are stored in an array.
// The user can then click an option to hide, show or delete the markers.
var map;
var markers = [];
var poly=[]; // for drawing path

function initialize() {
  var haightAshbury = new google.maps.LatLng(-15.785894, 35.006425);
  var mapOptions = {
    zoom: <?php echo $zvalue; ?>,
    center: haightAshbury,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
	  
// for poly lines for making paths
  poly = new google.maps.Polyline({ strokeColor: '#22DD22', strokeOpacity: 0.6, strokeWeight: 4 });
  poly.setMap(map);		  
}
var i=0;
function initialize_loop(){
 downloadUrl("phpsqlajax_genxml3_playback.php?PlayBackDeviceId=<?php echo $PlayBackDeviceId;?>&FromDate=<?php echo $FromDate.":00";?>&ToDate=<?php echo $ToDate.":00";?>", function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");
		    // define new array as a counter
			var arrsize = markers.length;
			if(arrsize<=(i+1)) timer.pause();
			<?php if($ShowPoints<>'1')
			{
			echo 'if(i < arrsize) deleteMarkers();';
			}
			?>
			var name = markers[i].getAttribute("name");
			var course = markers[i].getAttribute("course");
			var dt_image = markers[i].getAttribute("dt_image");
			var time = markers[i].getAttribute("time");
			// calculate stoped time (Whether it stoped or not)
			var pausetime=0;
			if(arrsize>=(i+2)) {
				time2=markers[i+1].getAttribute("time");
				pausetime=Math.abs(parseMyDate(time2) - parseMyDate(time)) / 60000;
			}			
			
			var speed = markers[i].getAttribute("speed");
			var label = markers[i].getAttribute("device_id");
			var tdiff = markers[i].getAttribute("tdiff");
			var Lat=parseFloat(markers[i].getAttribute("lat"));
			var Lng=parseFloat(markers[i].getAttribute("lng")); 
			var point = new google.maps.LatLng(Lat,Lng);
				  
  var html ='<div style="margin:1px !important; font-size:12px;">' + "<b>" + name + "<br/> Time : " + time + "<br/>Lat : " + Lat + "<br/>Lng : " + Lng;
  // display stopped time if it had stopped  
  if(pausetime>=2) {
  	if(Math.round(pausetime)>=60) 
	html = html + "<br/> Stopped : " + Math.round(pausetime/60) + " Hours & " + Math.round(pausetime%60) + " Mins";
	else html = html + "<br/> Stopped : " + Math.round(pausetime) + "Mins";
  }
  html = html + '</div>' ;
 
	map.panTo(new google.maps.LatLng(markers[i].getAttribute("lat"),markers[i].getAttribute("lng")));
	// Add a marker when click on the map
    addMarker(point,dt_image,course,html,arrsize,i,pausetime);
	i++

 });     // downloadUrl end here
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
// Add a marker to the map and push to the array.
function addMarker(location,dt_image,course,html,arrsize,i,pausetime) {
	<?php 
	if($ShowPoints<>'1')
	{
		echo '
		if(course<=22.5)
			image = "images/"+dt_image+"0.png";
		else if (course<=67.5)
			image = "images/"+dt_image+"45.png";
		else if (course<=112.5)
			image = "images/"+dt_image+"90.png";
		else if (course<=157.5)
			image = "images/"+dt_image+"135.png";
		else if (course<=202.5)
			image = "images/"+dt_image+"180.png";
		else if (course<=247.5)
			image = "images/"+dt_image+"225.png";
		else if (course<=292.5)
			image = "images/"+dt_image+"270.png";
		else
			image = "images/"+dt_image+"315.png";';
	}
	else echo 'image = "images/rdot.png";';			
	?>
	if(pausetime>=2)
	image = "images/park.png"; // park img  	
	if(i==0)
	image = "images/start.png"; // 0 angle img
	if(i==(arrsize-1))
	image = "images/stop.png"; // 0 angle img
//////////////////////////////////////////////////////////
  var path = poly.getPath();
  path.push(location);		
	
 var marker = new google.maps.Marker({
    position: location,
	icon:image,
    map: map
  });
  
var infoWindow = new google.maps.InfoWindow({maxWidth:300});
google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
  
markers.push(marker); 
} 

// Sets the map on all markers in the array.
function setAllMap(map) {
  for (var j = 1; j < markers.length; j++) {
    markers[j].setMap(map);
  }
}
// Deletes all markers in the array by removing references to them.
function deleteMarkers() {
   setAllMap(null);
 // markers = [];
}

    function doNothing() {}
	
	
	
function Timer(callback, delay) {
    var timerId, start, remaining = delay;

    this.pause = function() {
        window.clearTimeout(timerId);
        remaining = <?php echo $PlayBackSpeed; ?>;
    };

    this.resume = function() {
		start = new Date();
        timerId = window.setTimeout(callback, remaining);
    };

    this.resume();
}



	
var timer = new Timer(function() {	
initialize_loop();
timer.resume();
}, <?php echo $PlayBackSpeed;?>);	

function btnstart(){
document.getElementById('Start').disabled=true;
document.getElementById('Pause').disabled=false;	
}
function btnpause(){
document.getElementById('Pause').disabled=true;
document.getElementById('Start').disabled=false;	
}
</script>

    
    
    
  </head>
  <body onLoad="timer.pause(); btnpause();">
<div id="btn-div">
<input type="button" id="Start" onClick="timer.resume(); btnstart();" value="Start" /> &nbsp;
<input type="button" id="Pause" onClick="timer.pause(); btnpause();" value="Pause" />  
</div> 
<form name="control" method="get" action="playback.php">
    <div id="FromDate-div">
        From :    <input id="FromDate" name="FromDate" style="width:150px;" />
    </div>
    <div id="ToDate-div">
        To :    <input id="ToDate" name="ToDate" style="width:150px;" />
    </div>
    <div id="playspeed">
    Playback Speed :
    <select name="PlayBackSpeed">
    <?php
	if($PlayBackSpeed<>"")
	{
		if($PlayBackSpeed=='10')
			echo '<option value="10">Very Fast</option>';	
		else if ($PlayBackSpeed=='250')
			echo '<option value="250">Fast</option>';	
		else if ($PlayBackSpeed=='500')
			echo '<option value="500">Medium</option>';	
		else if ($PlayBackSpeed=='750')
			echo '<option value="750">Slow</option>';	
		else if ($PlayBackSpeed=='1000')
			echo '<option value="1000">Very Slow</option>';	
	}
	?>
    	<option value="1000">Very Slow</option>
        <option value="750">Slow</option>
        <option value="500">Medium</option>
        <option value="250">Fast</option>
        <option value="10">Very Fast</option>        
    </select>
    &nbsp;
    <?php
	if($ShowPoints=='1')
    echo '<input type="checkbox" name="ShowPoints" value="1" checked>Show Points';
	else  echo '<input type="checkbox" name="ShowPoints" value="1">Show Points';
	?>
    </div>
    <div id="btn-load">
        <input type="submit" name="LoadData" value="Load Data"> 
        <input type="hidden" name="PlayBackDeviceId" value="<?php echo $PlayBackDeviceId; ?>">
    </div>
            
</form>
   
        
<div id="map-canvas"></div>
  </body>
</html>


