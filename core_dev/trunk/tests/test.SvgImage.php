<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('SvgImage.php');


$c = new SvgColor('#aaeeaa');
if ($c->render() != 'rgb(170,238,170)') echo "FAIL 1\n";

$c = new SvgColor(180, 240, 190);
if ($c->render() != 'rgb(180,240,190)') echo "FAIL 2\n";



/*
$polys[0]['coords'] = array(10, 10, 20, 20, 30, 30, 40, 40);    //4 points
$polys[0]['color'] = 0xffff00;
$polys[0]['border'] = 0x00ffff;

$polys[1]['coords'] = array(50, 10, 50, 20, 50, 30, 50, 40, 50, 50);    //5 points
$polys[1]['color'] = 0xaaff80;
$polys[1]['border'] = 0x00ff00;


$svg = new svg(100, 300);
$svg->addPolys($polys);

echo $svg->render();
*/

?>
