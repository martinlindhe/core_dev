<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('SqlFactory.php');

require_once('sql_mysql_describe.php');


$db = SqlFactory::factory('mysql', true);
SqlHandler::addInstance($db); //registers the created database connection as the one to use by SqlHandler

//GRANT SELECT,INSERT,UPDATE,DELETE ON marti32_lyric.* TO 'lyrics'@'%' IDENTIFIED BY 'sdvVAYEvaDD43szzy';
$db->setConfig( array('host' => 'styggve.dyndns.org', 'port' => 44308, 'username' => 'lyrics', 'password' => 'sdvVAYEvaDD43szzy', 'database' => 'marti32_lyric') );


$desc = new MysqlDescribe();
$desc->setDatabase('marti32_lyric');

echo $desc->renderDatabase();



?>
