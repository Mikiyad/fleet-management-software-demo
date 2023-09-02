<?php
include('session.php');
include('phpsqlajax_dbinfo.php');
include("includes/savelog.php");

if($GPSPRIVILEGE=='End-User') header('location:restricted.php');

session_start();

$sql=mysql_query("SELECT * FROM users WHERE id='$GPSUSERID'");
$result=mysql_fetch_array($sql);
$GPSUSERNAME=strtoupper($result['login']);

$Add=$_GET['Add'];
$EDID=$_GET['EDID'];
$DID=$_GET['DID'];
$Save=$_GET['Save'];
$Edit=$_GET['Edit'];
$Delete=$_GET['Delete'];

$user=$_GET['user'];
$privilege=$_GET['privilege'];
$password=$_GET['password'];
$repassword=$_GET['repassword'];
$description=$_GET['description'];
$address=$_GET['address'];
$telephone=$_GET['telephone'];
$email=$_GET['email'];

if(isset($Add))
{
	if(!empty($user) and $status<>'0' and !empty($password) and !empty($repassword))
	{
		if($password <> $repassword)
		{
			$ermsg='Password mismatch. Try again !';
		}
		else
		{
			$sql=mysql_query("SELECT * FROM users WHERE login='$user'");
			$count=mysql_num_rows($sql);
			if($count==0)
			{
				$password=md5($password);
				$sql=mysql_query("INSERT INTO users (`privilege`,`login`,`password`,`userSettings_id`,`status`,`description`,`address`,`telephone`,
				`email`,`subacc_id`) VALUES ('$privilege','$user','$password','1','Active','$description','$address','$telephone','$email',
				'$GPSUSERID')");
				if($sql)
				{
					SaveLog($GPSUSERID,'A new user added to the system. Username '.$login.'');
				}
				else $ermsg="Error occured when adding user. Try again !";
			}
			else
			$ermsg="This username already exist on the system. Try again !";
		}
	}
	else
	$ermsg="Username, Password, and Privilege required !";
}

if(isset($Save))
{
	if(!empty($user) and $status<>'0')
	{
		if($password <> $repassword)
		{
			$ermsg='Password mismatch. Try again !';
		}
		else
		{
			$sql=mysql_query("SELECT * FROM users WHERE login='$user' AND id<>'$EDID'");
			$count=mysql_num_rows($sql);
			if($count==0)
			{
				$password=md5($password);
				$sql=mysql_query("UPDATE users SET `privilege`='$privilege',`login`='$user',`password`='$password',`userSettings_id`='1',`status`='Active'
				,`description`='$description',`address`='$address',`telephone`='$telephone',`email`='$email' WHERE id='$EDID' ");
								
				if($sql)
				{
					SaveLog($GPSUSERID,'User updated successfully. Username '.$login.'');
				}
				else $ermsg="Error occured when updating user. Try again !";
			}
			else
			$ermsg="This username already exist on the system. Try again !";
		}		
	}
	else $ermsg="Username and Privilege required !";
}

if(isset($Delete))
{
$sql2=mysql_query("DELETE FROM users_devices WHERE subacc_id='$DID' ");
$sql1=mysql_query("DELETE FROM users WHERE subacc_id='$DID'");
	if($sql1 and $sql2)
	{
		$sql2=mysql_query("DELETE FROM users_devices WHERE users_id='$DID' ");
		$sql1=mysql_query("DELETE FROM users WHERE id='$DID'");
		if($sql1 and $sql2)
		{
			SaveLog($GPSUSERID,'User successfully disabled. User ID '.$DID.'');
		}
		else $ermsg='Error occured when deleting user. Try again !';
	}
	else $ermsg="Error occured when deleting user's sub accounts. Try again !";
}
//////////////////////////////////////////////////////////////////////////////////
if(isset($_GET['Edit']))
{
	$sql=mysql_query("SELECT * FROM users WHERE id='".$_GET['EDID']."'");
	$result=mysql_fetch_array($sql);
	
	$Euser=$result['login'];
	$Eprivilege=$result['privilege'];
	$Edescription=$result['description'];
	$EAddress=$result['address'];
	$ETelephone=$result['telephone'];
	$EEmail=$result['email'];
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
#Ddraggable { display:none; }
#Edraggable { display:none; }
</style>
<script type="text/javascript">
function Ddialog(did) {
$("#Ddraggable").dialog({modal:true});	
document.getElementById("DID").value=did;
}

function Edialog(did) {
$("#Edraggable").dialog({modal:true});	
document.getElementById("EID").value=did;
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
    <td bgcolor="#000000"><div style="color:#FFF; font-weight:bold; background:url(images/top_bg.gif) repeat-x;">:: ADD USERS</div></td>
  </tr>
  <tr>
    <td><div style="font-size:12px; color:#F00; font-weight:bold;"><?php echo $ermsg;?></div></td>
  </tr>
  <tr>
    <td>
    <form name="frmset" method="get" action="add_users.php">
    <table width="100%" border="0">
      <tr>
        <td width="200" align="left">Enter Username (Login) </td>
        <td align="left"><label for="user"></label>
          <input type="text" name="user" id="user" value="<?php echo $Euser;?>" placeholder="Login Name" required="required" /> 
          <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td align="left">Select Privilege</td>
        <td align="left">
        <select name="privilege">
        <?php 
		if(isset($Edit))
		echo '<option value="'.$Eprivilege.'">'.$Eprivilege.'</option>';
		else
		echo '<option value="0">- Select Privilege -</option>';
		
        if($GPSPRIVILEGE=='admin') echo '<option value="Distributor">Distributor</option>';			
		?>
        <option value="End-User">End-User</option>    
        </select>        
          <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td align="left"><strong>Enter Password</strong></td>
        <td align="left">
          <input type="password" name="password" id="password" placeholder="Password" required="required"/>
          <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td align="left"><strong>Re-Enter Password</strong></td>
        <td align="left">
        <input type="password" name="repassword" id="repassword" placeholder="Re-Type Password" required="required"/>
          <font color="#FF0000">*</font></td>
      </tr>
      <tr>
        <td align="left">Enter Description</td>
        <td align="left"><label for="description"></label>
          <textarea name="description" id="description" cols="40" rows="3"><?php echo $Edescription; ?></textarea></td>
      </tr>
      <tr>
        <td align="left">Enter Address</td>
        <td align="left"><input name="address" type="text" id="address" size="40" value="<?php echo $EAddress; ?>" placeholder="Address" /></td>
      </tr>
      <tr>
        <td align="left">Enter Telephone Number(s)</td>
        <td align="left"><input name="telephone" type="text" id="telephone" size="40" value="<?php echo $ETelephone; ?>" placeholder="Telephone"/></td>
      </tr>
      <tr>
        <td align="left">Enter Email Address(s)</td>
        <td align="left">
        <input name="email" type="email" id="email" size="40" value="<?php echo $EEmail; ?>" placeholder="E-Mail"/>
        <input name="EDID" type="hidden" id="EDID" value="<?php echo $EDID; ?>"/>
        </td>
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
        echo '<input type="submit" name="Add" id="Add" value="Add User Details" />';
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
          <td width="200" align="left" bgcolor="#CCCCCC">ADDRESS</td>
          <td width="100" align="left" bgcolor="#CCCCCC">TELEPHONE</td>
          <td width="150" align="left" bgcolor="#CCCCCC">EMAIL</td>
          <td width="60" align="left" bgcolor="#CCCCCC">STATUS</td>
          <td align="left" bgcolor="#CCCCCC">DESCRIPTION</td>
          <td width="100" align="left" bgcolor="#CCCCCC">&nbsp;</td>
          <td width="13" align="left" bgcolor="#CCCCCC">&nbsp;</td>
         </tr>
         </table>
         <div style="overflow: auto; height:240px; width: 100%; font-size:12px;">
         <table width="100%" border="0">
          <?php
		  $sql=mysql_query("SELECT * FROM users WHERE subacc_id='$GPSUSERID' ORDER BY id ASC");
		  $i=1;
		  while($row=mysql_fetch_array($sql))
		  {
			  if($i%2==1) $color="#ddd"; else $color="#eee";
			  echo '<form name="frmone" method="get" action="add_users.php"><tr height="20" style="background:'.$color.'; font-size:12px;">
			  <td width="50" align="left">'.$i++.'</td>
			  <td width="100" align="left">'.$row['login'].'</td>
			  <td width="100" align="left">'.$row['privilege'].'</td>
			  <td width="200" align="left">'.$row['address'].'</td>
			  <td width="100" align="left">'.$row['telephone'].'</td>
			  <td width="150" align="left">'.$row['email'].'</td>
			  <td width="60" align="left">'.$row['status'].'</td>			  
			  <td align="left">'.$row['description'].'</td>
			  <td width="100" align="right"><input type="hidden" name="EDID" value="'.$row['id'].'">';
			  echo '<button type="submit" name="Edit" style="height:15px;"><img src="images/bg/edit.png" width="15" height="10"></button>';
				if($row['status']=='Active')         
					echo '&nbsp;<button type="button" onclick="Ddialog('.$row['id'].')" name="Disable" style="height:15px;"><img src="images/bg/disable.png" width="15" height="10"></button>';
				else 
					echo '&nbsp;<button type="button" onclick="Edialog('.$row['id'].')" name="Enable" style="height:15px;"><img src="images/bg/enable.png" width="15" height="10"></button>';
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


<div title=":: Delete User" id="Ddraggable" class="ui-widget-content">
  <div style="margin-top:30px; text-align:left; font-size:14px; font-weight:bold; color:#000;">
  Are you sure you want to delete this user ?
  </div>
<div style="text-align:center; margin-top:30px;">
<form name="popupfrm" method="get" action="add_users.php" style="background:transparent; border:none;">
<input type="hidden" name="DID" id="DID" />
<input style="width:50px; height:30px; box-shadow:1px 1px 2px #000000; border-radius:5px;" name="Delete" type="submit" value="Yes" />
&nbsp;&nbsp;&nbsp;
<input type="submit" style="width:50px; box-shadow:1px 1px 2px #000000; height:30px; border-radius:5px;" value="No" />
</form>
</div>
</div>

</body>
</html>