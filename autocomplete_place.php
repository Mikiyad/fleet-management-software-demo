<?php
require("phpsqlajax_dbinfo.php");
if ( !isset($_REQUEST['term']) )
    exit;

$rs = mysql_query("SELECT * FROM markers WHERE place LIKE '". mysql_real_escape_string($_REQUEST['term']) ."%' GROUP BY place");

$data = array();
if ( $rs && mysql_num_rows($rs) )
{
    while( $row = mysql_fetch_array($rs, MYSQL_ASSOC) )
    {
        $data[] = array(
            'label' => $row['place'] ,
            'value' => $row['place']
        );
    }
}

echo json_encode($data);
flush();

