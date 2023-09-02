<?php
require("phpsqlajax_dbinfo.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<div style="width:100%; padding:5px; background:#D7DAFB;">
  <table width="100%" border="0">
    <tr>
      <td bgcolor="#000000"><div style="color:#FFF; font-weight:bold; background:url(images/top_bg.gif) repeat-x;">:: ALL ADDED PLACES</div></td>
    </tr>
    <tr>
      <td height="3px"></td>
    </tr>
    <tr>
      <td><table width="100%" border="0">
        <tr style="font-size:13px; font-weight:bold;">
          <td width="60" align="left" bgcolor="#CCCCCC">ID</td>
          <td width="100" align="left" bgcolor="#CCCCCC">PLACE (AREA)</td>
          <td width="150" align="left" bgcolor="#CCCCCC">DISTRIBUTOR</td>
          <td width="80" align="left" bgcolor="#CCCCCC">LATITUDE</td>
          <td width="80" align="left" bgcolor="#CCCCCC">LONGITUDE</td>
          <td align="left" bgcolor="#CCCCCC">DESCRIPTION</td>
          <td width="200" align="left" bgcolor="#CCCCCC">ADDRESS</td>
          <td width="100" align="left" bgcolor="#CCCCCC">TELEPHONE</td>
          <td width="150" align="left" bgcolor="#CCCCCC">EMAIL</td>
          <td width="50" align="left" bgcolor="#CCCCCC">&nbsp;</td>
          </tr>
          <?php
		  $sql=mysql_query("SELECT * FROM markers ORDER BY id ASC");
		  while($row=mysql_fetch_array($sql))
		  {
			  echo '<form name="frmone" method="get" action="add_places.php"><tr style="font-size:13px;">
			  <td align="left">'.$row['id'].'</td>
			  <td align="left">'.$row['place'].'</td>
			  <td align="left">'.$row['name'].'</td>
			  <td align="left">'.$row['lat'].'</td>
			  <td align="left">'.$row['lng'].'</td>
			  <td align="left">'.$row['description'].'</td>
			  <td align="left">'.$row['address'].'</td>
			  <td align="left">'.$row['telephone'].'</td>
			  <td align="left">'.$row['email'].'</td>
			  <td align="right"><input type="image" src="images/edit.png"><input type="hidden" name="EID" value="'.$row['id'].'"</td>
			  </tr><tr><td colspan="10" height="1px" bgcolor="#555"></td></tr></form>';
		  }
          ?>
      </table></td>
    </tr>
    <tr>
      <td height="3px"></td>
    </tr>
  </table>
</div>
</body>
</html>