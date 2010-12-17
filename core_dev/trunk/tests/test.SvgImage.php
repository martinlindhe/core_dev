<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('SvgImage.php');


$c = new SvgColor('#aaeeaa');
if ($c->render() != 'rgb(170,238,170)') echo "FAIL 1\n";

$c = new SvgColor(180, 240, 190);
if ($c->render() != 'rgb(180,240,190)') echo "FAIL 2\n";

$c = new SvgColor('#fff');
if ($c->render() != 'rgb(255,255,255)') echo "FAIL 3\n";

$c = new SvgColor('#000');
if ($c->render() != 'rgb(0,0,0)') echo "FAIL 4\n";


$chart = new SvgImage(500, 500);
$chart->setBackground('#aaff00');

$line = new SvgLine();
$line->color = new SvgColor('#000000');
$line->x1 = 0 + 10;
$line->y1 = 10;
$line->x2 = 10;
$line->y2 = $chart->height - 10;
$chart->add($line);


$circ = new SvgCircle();
$circ->x = 100;
$circ->y = 100;
$circ->radius = 50;
$chart->add($circ);


$poly = new SvgPolygon();
$poly->addPoint(50, 100);
$poly->addPoint(120, 180);
$poly->addPoint(40, 160);
$poly->addPoint(290, 300);
$chart->add($poly);

$rect = new SvgRectangle();
$rect->fill_color = new SvgColor('#eeeeee');
$rect->x = 150;
$rect->y = 150;
$rect->width = 120;
$rect->height = 90;
$chart->add($rect);


$txt = new SvgText('hello world');
$txt->x = 100;
$txt->y = 350;
$txt->size = 55;
$chart->add($txt);

$chart->output();

?>
