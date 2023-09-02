<?php
include('session.php');
include('phpsqlajax_dbinfo.php');
include("includes/savelog.php");

if($GPSPRIVILEGE=='End-User') header('location:restricted.php');

$sql=mysql_query("SELECT * FROM users WHERE id='$GPSUSERID'");
$result=mysql_fetch_array($sql);
$GPSUSERNAME=strtoupper($result['login']);

$Add=$_GET['Add'];
$Save=$_GET['Save'];
$SDID=$_GET['SDID'];
$Edit=$_GET['Edit'];
$EDID=$_GET['EDID'];

$name=$_GET['name'];
$type=$_GET['type'];
$imei=$_GET['imei'];
$group=$_GET['group'];
$simno=$_GET['simno'];
$description=$_GET['description'];
$licensepn=$_GET['licensepn'];
$overspeed=$_GET['overspeed'];

$Time=date("Y-m-d H:i:s");

if(isset($Add))
{
	if(!empty($name) and $type<>'0')
	{
		if(!empty($imei) and is_numeric($imei) and !empty($simno))
		{
			if($group<>'0')
			{
				$sql=mysql_query("SELECT * FROM devices WHERE uniqueId='$imei'");
				$count=mysql_num_rows($sql);
				if($count==0)
				{
					if($overspeed>0) $overspeed=$overspeed; else $overspeed=60;
					$sql1=mysql_query("INSERT INTO devices (`name`,`uniqueId`,`device_type`,`simno`,`description`,
					`licensepn`,`overspeed`,`GroupId`) VALUES ('$name','$imei','$type','$simno','$description',
					'$licensepn','$overspeed','$group')");
					$devices_id=mysql_insert_id(); // last inserted id of the devices table
					$sql2=mysql_query("INSERT INTO users_devices (`users_id`,`devices_id`) VALUES ('1','$devices_id')");
					if($sql1 and $sql2)
					{
						mysql_query("INSERT INTO positions (`address`,`altitude`,`course`,`latitude`,
						`longitude`,`other`,`power`,`speed`,`time`,`valid`,`device_id`) VALUES ('','0','360',
						'-15.785894','35.006425','initial','','0','$Time','1','$devices_id')");
						$InitialPosition=mysql_insert_id(); // inserted id of the positions table
						mysql_query("UPDATE devices SET latestPosition_id='$InitialPosition' WHERE id='$devices_id' ");
						
						SaveLog($GPSUSERID,'A new vehicle added to the system. IMEI '.$imei.'');
					}
					else $ermsg="Error occured when adding vehicle. Try again !";
				}
				else $ermsg="This vehicle(IMEI) already exist on the system. Try again !";	
			}
			else $errmsg="Please select group name. Try again !";
		}
		else $ermsg="Invalid IMEI number or Sim card number. Try again !";
	}
	else $ermsg="Vehicle name and Type required !";
}

if(isset($Save))
{
	if(!empty($name) and $type<>'0')
	{
		if(!empty($imei) and is_numeric($imei) and !empty($simno))
		{
			if($group<>'0')
			{
				$sql=mysql_query("SELECT * FROM devices WHERE uniqueId='$imei' AND id<>'$SDID' ");
				$count=mysql_num_rows($sql);
				if($count==0)
				{
					if($overspeed>0) $overspeed=$overspeed; else $overspeed=60;
					$sql=mysql_query("UPDATE devices SET `name`='$name',`uniqueId`='$imei',
					`device_type`='$type',`simno`='$simno',`description`='$description',
					`licensepn`='$licensepn',`overspeed`='$overspeed',GroupId='$group' WHERE id='$SDID'");
					if($sql)
					{
						SaveLog($GPSUSERID,'Vehicle updated successfully. IMEI '.$imei.'');
					}
					else $ermsg="Error occured when updating vehicle. Try again !";
				}
				else $ermsg="This vehicle already exist on the system. Try again !";
			}
			else $ermsg="Please select group name. Try again !";
		}
		else $ermsg="Invalid IMEI number or Sim card number. Try again !";
	}
	else $ermsg="Vehicle name and Type required !";
}

if(isset($Disable))
{
$sql=mysql_query("SELECT * FROM users WHERE id='$EDID'");
$result=mysql_fetch_array($sql);
$Duser=$result['login'];	
$Dprivilege=$result['privilege'];	
	if($Dprivilege<>'admin')
	{
		$sql=mysql_query("UPDATE users SET status='Inactive' WHERE id='$EDID'");
		if($sql)
		{
			SaveLog($GPSUSERID,'User successfully disabled. User ID '.$EDID.'');
		}
		else $ermsg='Error occured when user disable. Try again !';
	}
	else $ermsg="Admin user can't disable. Please ask from system admininstrator !";
}

//////////////////////////////////////////////////////////////////////////////////
if(isset($_GET['Edit']))
{
	$sql=mysql_query("SELECT * FROM devices JOIN device_type ON device_type.dt_id=devices.device_type LEFT JOIN device_groups ON device_groups.gid=devices.GroupId WHERE id='".$_GET['EDID']."'");
	$result=mysql_fetch_array($sql);
	
	$Ename=$result['name'];
	$Eimei=$result['uniqueId'];
	$EtypeId=$result['dt_id'];
	$Etype=$result['dt_name'];
	$Erepassword=$result['password'];
	$Esimno=$result['simno'];
	$Edescription=$result['description'];
	$Elicensepn=$result['licensepn'];
	$Eoverspeed=$result['overspeed'];
	$EgroupId=$result['GroupId'];
	$Egroup=$result['gname'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

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
    <td bgcolor="#000000"><div style="color:#FFF; font-weight:bold; background:url(images/top_bg.gif) repeat-x;">:: ADD VEHICLES</div></td>
  </tr>
  <tr>
    <td><div style="font-size:12px; color:#F00; font-weight:bold;"><?php echo $ermsg;?></div></td>
  </tr>
  <tr>
    <td>
    <form name="frmset" method="get" action="add_vehicles.php">
    <table width="100%" border="0">
      <tr>
        <td width="200" align="left">Enter Vehicle Name</td>
        <td align="left"><label for="name"></label>
          <input name="name" type="text" required="required" id="name" placeholder="Vehicle Name" value="<?php echo $Ename;?>" size="15" /> 
          <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td align="left">Select Vehicle Type</td>
        <td align="left">
        <select name="type" id="type">
        <?php 
		if(isset($EDID))
		echo '<option value="'.$EtypeId.'">'.$Etype.'</option>';
		else
		echo '<option value="0">- Select Vehicle Type -</option>';
		$sql=mysql_query("SELECT * FROM device_type WHERE dt_status='1'");
        while($row=mysql_fetch_array($sql))
		{    
		echo '<option value="'.$row['dt_id'].'">'.$row['dt_name'].'</option>';
		}
		?>
        </select>        
          <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td align="left"><strong>IMEI Number (Unique ID)</strong></td>
        <td align="left">
          <input name="imei" type="text" required="required" id="imei" placeholder="IMEI Number" <?php if($GPSPRIVILEGE<>'admin') echo 'readonly="readonly"';?>  value="<?php echo $Eimei; ?>" size="15"/>
          <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td align="left">Select Group Name </td>
        <td align="left"><select name="group" id="group">
          <?php 
		if(isset($EDID))
		echo '<option value="'.$EgroupId.'">'.$Egroup.'</option>';
		else
		echo '<option value="0">- Select Group -</option>';
		$sql=mysql_query("SELECT * FROM device_groups");
        while($row=mysql_fetch_array($sql))
		{    
		echo '<option value="'.$row['gid'].'">'.$row['gname'].'</option>';
		}
		?>
                </select></td>
      </tr>
      <tr>
        <td align="left"><strong>SIM Card  Number</strong></td>
        <td align="left"><input name="simno" type="text" required="required" id="simno" placeholder="SIM Number" <?php if($GPSPRIVILEGE<>'admin') echo 'readonly="readonly"';?>  value="<?php echo $Esimno; ?>" size="15"/>
            <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td align="left">Enter Description</td>
        <td align="left"><label for="description"></label>
          <textarea name="description" id="description" cols="40" rows="3"><?php echo $Edescription; ?></textarea></td>
      </tr>
      <tr>
        <td align="left">Licence Plate Number</td>
        <td align="left"><input name="licensepn" type="text" id="licensepn" size="15" value="<?php echo $Elicensepn; ?>" placeholder="Licence Plate No." /></td>
      </tr>
      <tr>
        <td align="left">Over  Speed Limit</td>
        <td align="left"><input name="overspeed" type="text" id="overspeed" size="5" value="<?php echo $Eoverspeed; ?>" />
          <input name="SDID" type="hidden" id="SDID" value="<?php echo $EDID; ?>"/></td>
      </tr>
      <tr>
        <td align="left">&nbsp;</td>
        <td align="left">&nbsp;</td>
      </tr>
      <tr>
        <td align="left">&nbsp;</td>
        <td align="left">
        <?php
		if(isset($_GET['EDID']))
        echo '<input type="submit" name="Save" id="Save" value="Save Vehicle Details" />';
		else
        echo '<input type="submit" name="Add" id="Add" value="Add Vehicle Details" />';
		?>        </td>
      </tr>
    </table>
    </form>
    </td>
  </tr>
</table>

</div>
<br />
<div style="width:100%; padding:5px; background:#D7DAFB;">
  <table width="100%" border="0">
    <tr>
      <td bgcolor="#000000"><div style="color:#FFF; font-weight:bold; background:url(images/top_bg.gif) repeat-x;">:: Current Vehicles</div></td>
    </tr>
    <tr>
      <td height="3px"></td>
    </tr>
    <tr>
      <td>
      
<table width="100%">
<thead>
        <tr style="font-size:12px; font-weight:bold;">
        	<th width="50" align="left" bgcolor="#CCCCCC">NO</th>
            <th width="150" align="left" bgcolor="#CCCCCC">VEHICLE NAME</th>
            <th width="100" align="left" bgcolor="#CCCCCC">TYPE</th>
            <th width="150" align="left" bgcolor="#CCCCCC">IMEI NUMBER</th>
            <th width="150" align="left" bgcolor="#CCCCCC">LICENCE PLATE NO.</th>
            <th width="100" align="left" bgcolor="#CCCCCC">OVER SPPED</th>
            <th align="left" bgcolor="#CCCCCC">SIM NUMBER</th>
            <th width="50" align="right" bgcolor="#CCCCCC">&nbsp;</th>
            <th width="13" align="right" bgcolor="#CCCCCC">&nbsp;</th>            
        </tr>
</thead>
    </table>
  <div style="overflow: auto; height:220px; width: 100%; font-size:12px;">
    <table width="100%">
        <?php
		  $sql=mysql_query("SELECT * FROM users_devices JOIN devices ON devices.id=users_devices.devices_id WHERE users_id='$GPSUSERID' ORDER BY id ASC");
		  $i=1;
		  while($row=mysql_fetch_array($sql))
		  {
			  $query=mysql_query("SELECT * FROM device_type WHERE dt_id='".$row['device_type']."'");
			  $result=mysql_fetch_array($query);
			  if($i%2==1) $color="#eee"; else $color="#ddd";
			  echo '<form name="frmone" method="get" action="add_vehicles.php"><tr style="background:'.$color.'; font-size:12px;">
			  <td width="50" align="left">'.$i++.'</td>
			  <td width="150" align="left">'.$row['name'].'</td>
			  <td width="100" align="left"><img src="images/'.$result['dt_image'].'90.png" height="20"></td>
			  <td width="150" align="left">'.$row['uniqueId'].'</td>
			  <td width="150" align="left">'.$row['licensepn'].'</td>
			  <td width="100" align="left">'.$row['overspeed'].'</td>
			  <td align="left">'.$row['simno'].'</td>
			  <td width="50" align="right"><input type="hidden" name="EDID" value="'.$row['id'].'">';
			  echo '<button type="submit" name="Edit" style="height:15px;"><img src="images/bg/edit.png" width="15" height="10"></button>
			  </td></tr></form>';
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
</body>
</html>
