<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('ConvertEnergy.php');

if (ConvertEnergy::convert('kWh', 'MWh', 15) != 0.015)    echo "FAIL 1\n";
if (ConvertEnergy::convert('GWh', 'kWh', 15) != 15000000) echo "FAIL 2\n";
