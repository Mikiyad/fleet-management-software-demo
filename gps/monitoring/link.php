<?php
require("phpsqlajax_dbinfo.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script> 
<script type="text/javascript" src="js/jquery-ui-1.8.2.custom.min.js"></script>
<link rel="stylesheet" href="css/jquery-ui-1.8.2.custom.css" /> 
<script type="text/javascript">
jQuery(document).ready(function(){
	$('#search_place').autocomplete({source:'autocomplete_place.php', minLength:2});
});

jQuery(document).ready(function(){
	$('#search_dis').autocomplete({source:'autocomplete_dis.php', minLength:2});
});
</script>
<style>
.vname { background:url(images/bg/u_online.gif) left no-repeat; }
.vname a{ text-decoration:none; color:#22cc22; margin:1px 1px 1px 20px; }
</style>

</head>

<body>
<table width="100%" border="0" style="font-size:12px; font-family:Arial, Helvetica, sans-serif; ">
    <tr>
      <td>
      
      <table width="100%" border="0">
          <tr>
            <td align="left"><strong>Search Added Places
              <a href="gis.php" target="mapframe.php">
              <input style="float:right; background:#F00;" type="submit" name="button" id="button" value="Reset">
              </a>
            </strong></td>
          </tr>
          <tr>
            <td align="left" height="2"></td>
          </tr>
          <tr>
            <td align="left" height="1" bgcolor="#000000"></td>
          </tr>
		<?php
		$sql=mysql_query("SELECT * FROM markers LIMIT 25");
			$i=1;
			mysql_query("UPDATE markers SET type=''");
			while($result=mysql_fetch_array($sql))
			{
				mysql_query("UPDATE markers SET type='$i' WHERE id='".$result['id']."'");
				echo  '<tr>
				<td colspan="2" width="85%">
				<div class="vname">
				<a target="iframe" href="mapframe.php?cLat='.$result['lat'].'&cLong='.$result['lng'].'&num='.$i.'">'.$result['name'].'</a>
				<div>
				</td>
				</tr>';
			}
        ?>
          <tr>
            <td align="left" height="5"></td>
          </tr>
        </table>
      
      </td>
    </tr>
</table>
</body>
</html>
