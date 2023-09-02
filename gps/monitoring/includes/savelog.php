<?php
function SaveLog ($LUID,$LDES) {
$Now=date("Y-m-d H:i:s");
//------------------------- Insert Log ---------------------------------------------
mysql_query("INSERT INTO tbl_system_log (`LDT`,`LUSER`,`LDESCRIPTION`) VALUES ('$Now','$LUID','$LDES')");
}
?>