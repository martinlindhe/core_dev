<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('SqlFactory.php');

require_once('DatabaseDescribeMysql.php');




$desc = new MysqlDescribe();
$desc->setDatabase('dbLyrics');

echo $desc->renderDatabase();

