<?php
include('session.php');
include('phpsqlajax_dbinfo.php');
include("includes/savelog.php");

if($GPSPRIVILEGE=='End-User') header('location:restricted.php');

session_start();

$sql=mysql_query("SELECT * FROM users WHERE id='$GPSUSERID'");
$result=mysql_fetch_array($sql);
$GPSUSERNAME=strtoupper($result['login']);

$Sale=$_GET['Sale'];
$Delete=$_GET['Delete'];
$UID=$_GET['UID'];
$DID=$_GET['DID'];

$touser=$_GET['touser'];
$vehicle=$_GET['vehicle'];

if(isset($Sale))
{
	$sql=mysql_query("SELECT * FROM users_devices WHERE users_id='$touser' AND devices_id='$vehicle'");
	$count=mysql_num_rows($sql);
	if($count==0)
	{
		if($touser<>'0' and $vehicle<>'0')
		{
			$sql=mysql_query("SELECT * FROM users WHERE id='$touser' ");
			$result=mysql_fetch_array($sql);
			$subacc_id=$result['subacc_id'];
			$sql=mysql_query("INSERT INTO users_devices (`users_id`,`devices_id`,`subacc_id`) VALUES ('$touser','$vehicle','$subacc_id')");
		}
		else
		$ermsg="Please select user and vehicle !";
	}
	else $ermsg='This vehicle already sold to this user. Try again !';
}



if(isset($Delete))
{
 mysql_query("DELETE FROM users_devices WHERE users_id='$UID' AND devices_id='$DID'");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="css/smoothness/jquery-ui-1.8.2.custom.css" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script> 
<script type="text/javascript" src="js/jquery-ui-1.8.2.custom.min.js"></script>
<title>Untitled Document</title>
<style type="text/css">
#draggable { display:none; }
</style>
<script type="text/javascript">
function dialog(uid,did) {
$("#draggable").dialog({modal:true});
document.getElementById("UID").value=uid;	
document.getElementById("DID").value=did;
}
</script>


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
.vname a { text-decoration:none; color:#22cc22; margin:1px 1px 2px 18px; }
.vname a:hover { text-decoration:underline; color:#22cc22; margin:1px 1px 2px 18px; }
</style>
</head>

<body style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">

<div style="width:200px; margin:10px 2px 2px 0px; float:left;">
	<?php include('settings_left.php');?>
</div>

<div id="map-canvas">
<div style="width:100%; padding:5px; background:#D7DAFB;">
<table width="100%" border="0">
  <tr>
    <td bgcolor="#000000"><div style="color:#FFF; font-weight:bold; background:url(images/top_bg.gif) repeat-x;">:: SALE VEHICLES</div></td>
  </tr>
  <tr>
    <td><div style="font-size:12px; color:#F00; font-weight:bold;"><?php echo $ermsg;?></div></td>
  </tr>
  <tr>
    <td>
    <form name="frmset" method="get" action="sale_vehicle.php">
    <table width="100%" border="0">
      <tr>
        <td width="200" align="left"><strong>Sale Vehicle To User</strong></td>
        <td align="left">
          <select name="touser">
          <option value="0">- Select User -</option>
            <?php 
		$sql=mysql_query("SELECT * FROM users WHERE subacc_id='$GPSUSERID' ");
		while($row=mysql_fetch_array($sql))
		{
			echo '<option value="'.$row['id'].'">'.$row['login'].'</option>';	
		}
		?>
            
            </select>        
          <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td align="left">Select Vehicle</td>
        <td align="left"><select name="vehicle">
        <option value="0">- Select Vehicle -</option>
          <?php 
		if($GPSPRIVILEGE=='admin')
		{
			$sql=mysql_query("SELECT * FROM devices");
			while($row=mysql_fetch_array($sql))
			{
				echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';	
			}
		}
		else
		{
			$sql=mysql_query("SELECT * FROM users_devices JOIN devices ON devices.id=users_devices.devices_id WHERE users_id='$GPSUSERID' ");
			while($row=mysql_fetch_array($sql))
			{
				echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';	
			}
		}			
		?>
        </select>         
          <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td align="left">&nbsp;</td>
        <td align="left">&nbsp;</td>
      </tr>
      <tr>
        <td align="left">&nbsp;</td>
        <td align="left">
        <?php
		if(isset($_GET['Edit']))
        echo '<input type="submit" name="Save" id="Save" value="Save User Details" />';
		else
        echo '<input type="submit" name="Sale" id="Sale" value="Sale Vehicle" />';
		?>
        </td>
      </tr>
    </table></form>
    </td>
  </tr>
</table>

</div>
<br />
<div style="width:100%; padding:5px; background:#D7DAFB;">
  <table width="100%" border="0">
    <tr>
      <td bgcolor="#000000"><div style="color:#FFF; font-weight:bold; background:url(images/top_bg.gif) repeat-x;">:: Current Users</div></td>
    </tr>
    <tr>
      <td height="3px"></td>
    </tr>
    <tr>
      <td> 
     
      <table width="100%" border="0">
        <tr style="font-size:12px; font-weight:bold;">
          <td width="50" align="left" bgcolor="#CCCCCC">NO</td>
          <td width="100" align="left" bgcolor="#CCCCCC">USER (LOGIN)</td>
          <td width="100" align="left" bgcolor="#CCCCCC">PRIVILEGE</td>
          <td width="200" align="left" bgcolor="#CCCCCC">VEHICLE</td>
          <td width="150" align="left" bgcolor="#CCCCCC">IMEI NUMBER</td>
          <td width="150" align="left" bgcolor="#CCCCCC">EMAIL</td>
          <td align="left" bgcolor="#CCCCCC">DESCRIPTION</td>
          <td width="100" align="left" bgcolor="#CCCCCC">&nbsp;</td>
          <td width="13" align="left" bgcolor="#CCCCCC">&nbsp;</td>
         </tr>
         </table>
         <div style="overflow: auto; height:430px; width: 100%; font-size:12px;">
         <table width="100%" border="0">
          <?php
		  $sql=mysql_query("SELECT * FROM users_devices JOIN devices ON devices.id=users_devices.devices_id JOIN users ON users.id=users_devices.users_id 
		  WHERE users_devices.subacc_id='$GPSUSERID' ORDER BY login ASC");
		  $i=1;
		  while($row=mysql_fetch_array($sql))
		  {
			  $color1="#EEE"; $color2="#DCC";
			  
			  if ($i==1) $color="#EEE";
			  else
			  {
				  if($login==$row['login'])
				  {		  
				   if($color==$color1) $color=$color1; else $color=$color2;
				  }
				  else
				  {  
				   if($color==$color1) $color=$color2; else $color=$color1;
				  }
			  }
			  $login=$row['login'];
			  
			  echo '<form name="frmone" method="get" action="sale_vehicle.php"><tr height="20" style="background:'.$color.'; font-size:12px;">
			  <td width="50" align="left">'.$i++.'</td>
			  <td width="100" align="left">'.$row['login'].'</td>
			  <td width="100" align="left">'.$row['privilege'].'</td>
			  <td width="200" align="left">'.$row['name'].'</td>
			  <td width="150" align="left">'.$row['uniqueId'].'</td>
			  <td width="150" align="left">'.$row['email'].'</td>			  
			  <td align="left">'.$row['description'].'</td>
			  <td width="100" align="right">
			  <button type="button" onclick="dialog('.$row['users_id'].",".$row['devices_id'].')" style="height:15px;">
			  <img src="images/bg/disable.png" width="15" height="10"></button>';
				
			  echo '</td>
			  </tr></form>';
		  }
          ?>
      </table>
      </div>
      </td>
    </tr>
    <tr>
      <td height="3px"></td>
    </tr>
  </table>
</div>
</div>


<div title=":: Delete User" id="draggable" class="ui-widget-content">
  <div style="margin-top:30px; text-align:left; font-size:14px; font-weight:bold; color:#000;">
  Are you sure you want to delete this user ?
  </div>
<div style="text-align:center; margin-top:30px;">
<form name="popupfrm" method="get" action="sale_vehicle.php" style="background:transparent; border:none;">
<input type="hidden" name="UID" id="UID" />
<input type="hidden" name="DID" id="DID" />
<input style="width:50px; height:30px; box-shadow:1px 1px 2px #000000; border-radius:5px;" name="Delete" type="submit" value="Yes" />
&nbsp;&nbsp;&nbsp;
<input type="submit" style="width:50px; box-shadow:1px 1px 2px #000000; height:30px; border-radius:5px;" value="No" />
</form>
</div>
</div>

</body>
</html>