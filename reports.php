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
    <meta charset="utf-8">
    <title>LTM Gps Tracking</title>
    
<style>
 html, body
	{
		height: 100%;
		width: 100%;
		margin: 0px;
		padding: 0px;
		overflow: hidden;
	}
#iframe { width:100%; height:650px;; }
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
</style>
    
  </head>
  <body>
  <table width="100%" height="100%" border="0" style="margin-top:-2px; margin-left:-1px;">
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
      <tr>
        <td width="83%" align="left" valign="top"><iframe frameborder="0" name="iframe" src="daily_distance_report.php" id="iframe"></iframe></td>
    </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
  </table>
  </body>
</html>

