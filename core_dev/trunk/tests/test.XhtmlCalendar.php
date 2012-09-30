<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('XhtmlCalendar.php');

$cal = new XhtmlCalendar(2010, 4);
$cal->addEvent('2010-04-14', 'fjortonde!');
$cal->addEvent('2010-04-14', 'fjortonde222!');
$cal->addEvent('2010-04-17', 'mer skit');
echo $cal->renderFlat();

?>
