<?php
include('phpsqlajax_dbinfo.php');
include("includes/savelog.php");

$username=$_POST['username'];
$password=md5($_POST['password']);
$login=$_POST['login'];

if(isset($login))
{
	if(!empty($username) and !empty($password))
	{
		$sql=mysql_query("SELECT * FROM users WHERE login='$username' AND password='$password' AND status='Active'");
		$count=mysql_num_rows($sql);
		$result=mysql_fetch_array($sql);
		if($count==1)
		{
			if($result['status']=='Active')
			{			
				session_start();
				$USERID=$result['id'];
				$PRIVILEGE=$result['privilege'];
				$_SESSION['GPSUSERID']=$USERID;
				$_SESSION['GPSPRIVILEGE']=$PRIVILEGE;
				SaveLog($GPSUSERID,'User Loged in to the system : '.$username.'');
				header('location:index.php');
			}
			else
			$ermsg="Your account disabled by system administrator. Please contact him !";
		}
		else
		$ermsg="Username or Password incorrect ! Try again ...";
	}
	else
	$ermsg="Username and Password cannot be empty ! Try again ...";
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Login Panel</title>
<link rel="shortcut icon" href="favicon.ico" >
<link rel="icon" href="animated_favicon.gif" type="image/gif" >
  
<link href="sidestyle/images/style1.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.back{
background: rgb(76,76,76); /* Old browsers */
background: -moz-radial-gradient(center, ellipse cover, rgba(76,76,76,1) 0%, rgba(71,71,71,1) 0%, rgba(102,102,102,1) 0%, rgba(44,44,44,1) 0%, rgba(17,17,17,1) 0%, rgba(28,28,28,1) 48%, rgba(0,0,0,1) 100%, rgba(19,19,19,1) 100%); /* FF3.6+ */
background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(76,76,76,1)), color-stop(0%,rgba(71,71,71,1)), color-stop(0%,rgba(102,102,102,1)), color-stop(0%,rgba(44,44,44,1)), color-stop(0%,rgba(17,17,17,1)), color-stop(48%,rgba(28,28,28,1)), color-stop(100%,rgba(0,0,0,1)), color-stop(100%,rgba(19,19,19,1))); /* Chrome,Safari4+ */
background: -webkit-radial-gradient(center, ellipse cover, rgba(76,76,76,1) 0%,rgba(71,71,71,1) 0%,rgba(102,102,102,1) 0%,rgba(44,44,44,1) 0%,rgba(17,17,17,1) 0%,rgba(28,28,28,1) 48%,rgba(0,0,0,1) 100%,rgba(19,19,19,1) 100%); /* Chrome10+,Safari5.1+ */
background: -o-radial-gradient(center, ellipse cover, rgba(76,76,76,1) 0%,rgba(71,71,71,1) 0%,rgba(102,102,102,1) 0%,rgba(44,44,44,1) 0%,rgba(17,17,17,1) 0%,rgba(28,28,28,1) 48%,rgba(0,0,0,1) 100%,rgba(19,19,19,1) 100%); /* Opera 12+ */
background: -ms-radial-gradient(center, ellipse cover, rgba(76,76,76,1) 0%,rgba(71,71,71,1) 0%,rgba(102,102,102,1) 0%,rgba(44,44,44,1) 0%,rgba(17,17,17,1) 0%,rgba(28,28,28,1) 48%,rgba(0,0,0,1) 100%,rgba(19,19,19,1) 100%); /* IE10+ */
background: radial-gradient(ellipse at center, rgba(76,76,76,1) 0%,rgba(71,71,71,1) 0%,rgba(102,102,102,1) 0%,rgba(44,44,44,1) 0%,rgba(17,17,17,1) 0%,rgba(28,28,28,1) 48%,rgba(0,0,0,1) 100%,rgba(19,19,19,1) 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#4c4c4c', endColorstr='#131313',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
</style>

</head>

<body style="background:#000 url(images/bg/bg.jpg) repeat-x;">
<div align="center">
<div style="padding:20px;"></div>
<form name="frmlogin" method="post" action="login.php">
  <table  class="back" width="800" height="280" style="border:1px solid #FFF; border-radius :15px; color: #FFFFFF; font-weight: bold; background:url(images/bg/login-bg.gif) no-repeat;">
    <tr>
      <td height="110" align="center" valign="middle">&nbsp;</td>
    </tr>
      <?php
	if(isset($ermsg))
	{
		echo '<tr>
      	<td height="30" colspan="2" align="center" valign="middle"><strong><div style="color:#EE0000;">'.$ermsg.'</strong></div></td>
    	</tr>
		';
	}
	?>
    <tr>
      <td background="image/loginwindow.jpg">			
		
		<div style="float:right; width:200px;" align="left">
			<div style="margin:10px 2px 2px 10px;"><input type="text" name="username" placeholder="username" /></div>
			<div style="margin:10px 2px 2px 10px;"><input type="password" name="password" placeholder="password" /></div>		
			<div><input style="height:30px; margin:10px 2px 2px 10px; width:100px; font-size:16px;" type="submit" name="login" value="LOGIN" /></div>	
		</div>	
		<div style="float:right; width:250px;" align="left">
			<div style="margin:8px 2px 2px 150px; font-size:16px; font-family:Arial, Helvetica, sans-serif; height:25px; color:#444;">Username</div>
			<div style="margin:8px 2px 2px 150px; font-size:16px; font-family:Arial, Helvetica, sans-serif; height:25px; color:#444;">Password</div>        
		</div>    
 
		 </td>
      </tr>
  </table>
  <br />
  <div style=" margin:0 auto; font-size:10px; font-family:Arial, Helvetica, sans-serif; color:#CCC;">
   System developed by <a style=" text-decoration:none; color:#999;" href="http://www.globemw.net">lahirutm - lahirutm@gmail.com</a>
   </div>
</form>
</div>
</body>
</html>
