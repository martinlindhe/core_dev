<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('ConvertEnergy.php');

$m = new ConvertEnergy();
$m->setPrecision(3);

if ($m->conv('kWh', 'MWh', 15) != 0.015)    echo "FAIL 1\n";
if ($m->conv('GWh', 'kWh', 15) != 15000000) echo "FAIL 2\n";
