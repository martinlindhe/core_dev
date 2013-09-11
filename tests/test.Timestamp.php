<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('Timestamp.php');


$t = new Timestamp('now');

echo $t->getSqlDateTime()."\n";

?>
