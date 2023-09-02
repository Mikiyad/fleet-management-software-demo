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
    <title>Settings | Globe GIS</title>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
    <style>
	#iframe { width:100%; height:650px;; }
	.ITitle { font:Georgia, "Times New Roman", Times, serif; font-size:15px; }
	</style>
  </head>
  <body onload="load(<?php echo $cLat;?>,<?php echo $cLong;?>)">
  <table width="100%" height="100%" border="0">
      <tr>
        <td height="73" colspan="2" style="background:url(images/bg/top_bg.gif) repeat-x;">
        <div style="margin-left:10px; float:left; font-size:35px; font:Georgia, 'Times New Roman', Times, serif bold; width:250px;">
        GLOBE GIS
        </div>
        <div style="margin: 10px 50px; font-size:18px; float:left; color:#FFF; font-family:Arial, Helvetica, sans-serif; font-weight:bold; 
        width:250px;">
        <?php echo $GPSUSERNAME; ?>
        </div>
        <div align="right" style="margin: 10px 10px; font-size:15px; float:right; color:#FFF; 
        font:Georgia, 'Times New Roman', Times, serif bold; width:250px;">
        <a href="gis.php" style="text-decoration:none; color:#FFF;">Home</a> | <a href="settings.php" style="text-decoration:none; color:#FFF;">Settings</a> |  <a href="logout.php" style="text-decoration:none; color:#FFF;">Logout</a> </div>
        </td>
      </tr>
      <tr>
        <td width="250" valign="top"><table width="100%" border="0">      
          <tr>
            <td align="left">
            <iframe width="100%" height="650px" src="settings_left.php" frameborder="0"></iframe>
          </td>
          </tr>
        </table></td>
        <td width="83%" align="left" valign="top"><iframe frameborder="0" name="iframe" src="add_users.php" scrolling="no" id="iframe"></iframe></td>
    </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
  </table>
  </body>
</html>

