<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('db_mssql.php');

//die('XXX: need better db testing code');

$config['debug'] = true;

$mssql_db['username']   = 'user';
$mssql_db['password']   = 'password';
$mssql_db['database']   = 'database';
$mssql_db['host']   = 'server';
$mssql_db['port']   = 1433;

$mssql = new db_mssql($mssql_db);

print_r($mssql);

$q = 'SELECT * FROM table';

$res = $mssql->getArray($q);

print_r($res);

//FIXME
/*
if (!$db->findDatabase('dbKUK')) {
    $db->createDatabase('dbKUK');
}
$db->selectDatabase('dbKUK');

$layout = array(
'commentId'      => array('bigint:20:unsigned','null:NO','key:PRI','extra:auto_increment'),
'commentType'    => array('tinyint:3:unsigned','null:NO'),
'commentText'    => array('text'),
'commentPrivate' => array('tinyint:3:unsigned','null:NO'),
'timeCreated'    => array('datetime','null:YES'),
'timeDeleted'    => array('datetime','null:YES'),
'deletedBy'      => array('bigint:20:unsigned','null:NO'),
'ownerId'        => array('bigint:20:unsigned','null:NO'),
'userId'         => array('bigint:20:unsigned','null:NO'),
'userIP'         => array('bigint:20:unsigned','null:NO')
);

//$parsed = $db->parseLayout($layout);


if (!$db->findTable('tblKEX1')) {
    if (!$db->createTable('tblKEX1', $layout))
        $db->status();
}

if (!$db->verifyTable('tblKEX1', $layout)) echo "FAIL1";
*/


?>
