<?php
require_once('/var/www/core_dev/core/db_mysqli.php');

require_once('/var/www/core_dev/core/class.Blog.php');

$db = new db_mysqli(array('host'=>'localhost', 'username'=>'root', 'password'=>'', 'database'=>'dbIssues'));



$b = new Blog();
echo $b->edit();

?>
