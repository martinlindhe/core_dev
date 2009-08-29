<?
require_once('sql_helpers.php');

require('config.php'); //for $db

$s = new sql_id_list();
$s->tbl_name   = 'sw_list';
$s->owner_name = 'owner';
$s->val_name   = 'url_id';

$s->load(1);



//$s->add(666);



print_r($s);
print_r($db->queries);

?>
