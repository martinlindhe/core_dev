<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('output_svg.php');


$polys[0]['coords'] = array(10, 10, 20, 20, 30, 30, 40, 40);	//4 points
$polys[0]['color'] = 0xffff00;
$polys[0]['border'] = 0x00ffff;

$polys[1]['coords'] = array(50, 10, 50, 20, 50, 30, 50, 40, 50, 50);	//5 points
$polys[1]['color'] = 0xaaff80;
$polys[1]['border'] = 0x00ff00;


$svg = new svg(100, 300);
$svg->addPolys($polys);

echo $svg->render();

?>
