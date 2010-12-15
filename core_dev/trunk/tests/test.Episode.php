<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('Episode.php');

$e = new Episode('season 1, episode 24');
if ($e->get() != '1x24') echo "FAIL 1\n";

$e = new Episode('01x24');
if ($e->get() != '1x24') echo "FAIL 2\n";

$e = new Episode('S01E24');
if ($e->get() != '1x24') echo "FAIL 3\n";

$e = new Episode('s1e24');
if ($e->get() != '1x24') echo "FAIL 4\n";

?>
