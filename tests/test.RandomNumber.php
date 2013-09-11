<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('RandomNumber.php');

$low = 10;
$hi  = 20;
$d = RandomNumber::getInRange($low, $hi);
if ($d < $low || $d > $hi) echo "FAIL 1\n";

