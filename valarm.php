<?php
include('session.php');
include('phpsqlajax_dbinfo.php');

$sql=mysql_query("SELECT * FROM users WHERE id='$GPSUSERID'");
$result=mysql_fetch_array($sql);
$GPSUSERNAME=strtoupper($result['login']);
?>
<!DOCTYPE html>
<html>
  <head>
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
    <title>LTM Gps Tracking</title>
    
<style>
 html, body {   height: 100%; width: 100%; margin: 0px; padding: 0px; overflow: hidden; }
.ITitle { font:Georgia, "Times New Roman", Times, serif; font-size:15px; }
.lnk { 
padding-top:2px;
margin:2px;
background:#0066FF url(images/bg/meun_out.gif) repeat-x;
font-family:Arial, Helvetica, sans-serif;
font-size:12px;
font-weight:bold;
width:60px;
height:22px;
float:left;
border-radius:5px;
border:1px solid #006;
border-bottom:none;
text-align:center;
}
a:hover .lnk { background:#0066FF url(images/bg/menu_over.gif) repeat-x; }
a:active .lnk { background:#0066FF url(images/bg/menu_over.gif) repeat-x; }
.vname {  
	width:99%;
	height:16px;
	margin-bottom:2px; 
	font-size:11px; 
	font-family:Arial, Helvetica, sans-serif;
	text-align:left;   
}
.alarmdivtop { margin:0 auto; width:95%; height:50px; border-radius:5px; }
.alarmdiv { margin:0 auto; padding:5px; width:95%; height:500px; border:1px solid #CCCCCC; border-radius:5px; overflow:scroll; }

#DName { width:150px; font-size:12px; font-family:Arial, Helvetica, sans-serif; float:left; }
#DIMEI { width:150px; font-size:12px; font-family:Arial, Helvetica, sans-serif; float:left; }
#DTime { width:150px; font-size:12px; font-family:Arial, Helvetica, sans-serif; float:left;}
#DAlarm { width:150px; font-size:12px; font-family:Arial, Helvetica, sans-serif; float:left;}
.ClearAlarm { width:30px; height:18px; float:right; background:url(images/bg/delete.png) center no-repeat; }

</style>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript">
function showAlarm(str){
	if (str=="") {
	  document.getElementById("overSpeed").innerHTML="";
	  return;
	}
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
	  if (xmlhttp.readyState==4 && xmlhttp.status==200) {
		document.getElementById("overSpeed").innerHTML=xmlhttp.responseText;
	  }
	}
	xmlhttp.open("GET","alarms.php?q="+str,true);
	xmlhttp.send();
}

function deleteOne(id) {
	var did=id;

    $.ajax({
        type: "POST",
        data: { id: did },
        url: "delete_alarms.php",
        success: function (data) {            
           $("#main"+did).fadeOut(500);
        },
        error: function () {
            alert("error occured !");
        }
    });
}
function deleteAll() {
	var did="All";
    $.ajax({
        type: "POST",
        data: { id: did },
        url: "delete_alarms.php",
        success: function (data) {            
           $(".mainid").fadeOut(500);
        },
        error: function () {
            alert("error occured !");
        }
    });
}
</script>  
</head>
  <body onLoad="showAlarm('ok')">
  <table width="100%" border="0" style="margin-top:-2px; margin-left:-1px;">
      <tr>
        <td height="53" style="background:url(images/bg/top_bg.gif) repeat-x;">
        <div style="float:left; padding-botom:-5px;"><img style="margin-bottom:-5px;" src="images/bg/images_logo_cn.gif" height="53" alt="Globe GPS"></div>
        <div style="margin: 10px 50px; font-size:18px; float:left; color:#FFF; font-family:Arial, Helvetica, sans-serif; font-weight:bold; 
        width:250px;">
        <?php echo $GPSUSERNAME; ?>
        </div>
        <div align="right" style="margin: 10px 10px; font-size:15px; float:right; color:#FFF; 
        font:Georgia, 'Times New Roman', Times, serif bold; width:350px;">
            <a href="gis.php" style="text-decoration:none; color:#FFF;"><div class="lnk">Monitor</div></a>
            <a href="reports.php" style="text-decoration:none; color:#FFF;"><div class="lnk">Reports</div></a>
            <a href="valarm.php" style="text-decoration:none; color:#FFF;"><div class="lnk">Alarms</div></a>
            <a href="settings.php" style="text-decoration:none; color:#FFF;"><div class="lnk">Settings</div></a>
            <a href="logout.php" style="text-decoration:none; color:#FFF;"><div class="lnk">Logout</div></a> 
        </div>
        </td>
      </tr>
	  <tr><td>
	  <div style="width:100%;">
	  	<div class="alarmdivtop" style="float:left; background:url(images/bg/alarms.png) center no-repeat;"></div>
	  </div>
	  </td></tr>    
	  <tr><td>
	  <div style="width:100%;">
	  
	  <div class="alarmdiv" style="border:1px solid #dddddd; height:20px; font-weight:bold; overflow:hidden;">
		<div id="DName">Name</div>
		<div id="DName">Group Name</div>
		<div id="DIMEI">IMEI </div>
		<div id="DAlarm">Alarm Message</div>
		<div id="DTime">Alarm Time</div>		
		<div><input type="button" id="clearAll" value="Clear All" onClick="deleteAll();" style="float:right;"></div>
	  </div>
	  
	  	<div id="overSpeed" class="alarmdiv">
			<div>
				<div style="width:50px; margin-top:20px; margin-left:50px; float:left;">
					<img src="images/bg/loading.gif" height="35">
				</div>
				<div style="width:150px; margin-top:25px; float:left; font-size:18px; font-family:Arial, Helvetica, sans-serif;">
				Loading ...
				</div>
			</div>
		</div>
	  </div>
	  </td></tr>
  </table>
  </body>
</html>

