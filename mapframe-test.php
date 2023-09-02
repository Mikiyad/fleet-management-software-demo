<?php
require("phpsqlajax_dbinfo.php");
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
<!DOCTYPE html >
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>PHP/MySQL & Google Maps Example</title>
    <style>
	#map {position:fixed !important; position:absolute; top:0; right:0; bottom:0; left:0; }
	.ITitle { font:Georgia, "Times New Roman", Times, serif; font-size:15px; }
	</style>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
     
    <script type="text/javascript">
    //<![CDATA[
    var customIcons = {
      0: {icon: 'images/rcar0.png'},
      45: {icon: 'images/rcar45.png'},
	  90: {icon: 'images/rcar90.png'},
	  135: {icon: 'images/rcar135.png'},
	  180: {icon: 'images/rcar180.png'},
	  225: {icon: 'images/rcar225.png'},
	  270: {icon: 'images/rcar270.png'},
	  315: {icon: 'images/rcar315.png'},
    };

var num=<?php echo $num;?>;

    function load(a,b) {
      var map = new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(a,b),
        zoom: <?php echo $zvalue;?>,
        mapTypeId: 'roadmap'
      });
    //]]>
var infoWindow = new google.maps.InfoWindow({maxWidth:300});

      // Change this depending on the name of your PHP file

var N = 1000; //every 1 second
var timeout = function() { 
  setTimeout(function()
  {		
  
      downloadUrl("phpsqlajax_genxml3.php", function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");
		 
			
		 
        for (var i = 0; i < markers.length; i++) {
          var name = markers[i].getAttribute("name");
		  var description = markers[i].getAttribute("description");
          var address = markers[i].getAttribute("address");
		  var telephone = markers[i].getAttribute("telephone");
		  var email = markers[i].getAttribute("email");
          var type = markers[i].getAttribute("type");
		  var degree=80;
          var point = new google.maps.LatLng(
              parseFloat(markers[i].getAttribute("lat")),
              parseFloat(markers[i].getAttribute("lng")));
			  
			 
			  
          var html = "<b>" + name + "</b> <br/>" + description + "<br/>" + "Address : " + address + "<br/>" + telephone + "<br/>" + email;
		  if(degree>315) var icon = customIcons[315] || {};
		  else if(degree>270) var icon = customIcons[270] || {};
		  else if(degree>225) var icon = customIcons[225] || {};
		  else if(degree>180) var icon = customIcons[180] || {};
		  else if(degree>135) var icon = customIcons[135] || {};
		  else if(degree>90) var icon = customIcons[90] || {};
		  else if(degree>45) var icon = customIcons[45] || {};
		  else var icon = customIcons[0] || {};
		  
		  
          var marker = new google.maps.Marker({
            map: map,
            position: point, 
            icon:icon.icon,
            shadow: icon.shadow
          });
		  		
          bindInfoWindow(marker, map, infoWindow, html);
		  
		   if(num==(i+1))
			{
				map.panTo(new google.maps.LatLng(markers[i].getAttribute("lat"),markers[i].getAttribute("lng")));
			}
		  
        }
      });  
	  
	  
	  
	  
	   function bindInfoWindow(marker, map, infoWindow, html) {
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
    }
	
	timeout();
	  }, N); 
}


	
	
	

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

timeout();	  
 }

	
  </script>

  </head>

  <body onload="load(<?php echo $cLat;?>,<?php echo $cLong;?>)">
    <div id="map"></div>
  </body>

</html>
