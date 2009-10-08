<?php
require_once('class.sw_url.php');

require('config.php'); //for $db

$s = new sql_id_key();
$s->tbl_name = 'sw_url';
$s->id_name  = 'id';
$s->key_name = 'url';

$s->load(2);

print_r($s);
print_r($db->queries);

?>
