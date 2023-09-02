<?php
include('session.php');
include('phpsqlajax_dbinfo.php');

$Device=$_POST['vid'];
$Center=$_POST['center'];
$Rad=$_POST['radius'];
$GFname=$_POST['fname'];

$Delete=$_POST['del'];
$DID=$_POST['did'];

$CLatLong=explode(',',$Center);
$CLat=$CLatLong[0];
$CLong=$CLatLong[1];
$CLat = substr($CLat, 1);
$CLong = substr($CLong,0,-1);

if(isset($Device)){
$query=("SELECT * FROM geo_fence WHERE gfuser_id='$GPSUSERID' AND gfdevice_id='$Device' AND gfstatus=''");
$count=mysql_num_rows(mysql_query($query));
if($count<=2){ 
	$query.=(" AND gfname='$GFname' ");
	$count=mysql_num_rows(mysql_query($query));
		if($count==0){
			$sql=mysql_query("INSERT INTO geo_fence (`gfuser_id`,`gfdevice_id`,`gfname`,`gfcenter_lat`,`gfcenter_long`,
			`gfradius`) VALUES ('$GPSUSERID','$Device','$GFname','$CLat','$CLong','$Rad')");
			if($sql) echo "Success";
			else echo "Error.. Try again !";
		}
	else echo "Error.. Geo-Fence name already exists !";
}
else echo "Maximum numer of Geo-Fences (".$count." per vehicle) reached !";		
}

if(isset($Delete)){
$sql=mysql_query("UPDATE geo_fence SET gfstatus='Deleted' WHERE gfid='$DID'");
if($sql) echo "Success";
else echo "Delete failed. Try again !";
}
?>