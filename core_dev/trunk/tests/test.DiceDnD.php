<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('DiceDnD.php');

$d = new DiceDnD('2D6');
if ($d->min() != 2)    echo "FAIL 1\n";
if ($d->max() != 12)   echo "FAIL 2\n";


$d = new DiceDnD('2D6+2');
if ($d->min() != 4)    echo "FAIL 3\n";
if ($d->max() != 14)   echo "FAIL 4\n";


$d = new DiceDnD('3D6-2');
if ($d->min() != 1)    echo "FAIL 5\n";
if ($d->max() != 16)   echo "FAIL 6\n";


$d = new DiceDnD('1D8+60');
if ($d->min() != 61)   echo "FAIL 7\n";
if ($d->max() != 68)   echo "FAIL 8\n";


/*
d($d);
echo $d->about()."\n";
echo 'roll: '.$d->roll()."\n";

*/
