<?php

/**
 * $Id$
 */

require_once('find_config.php');

if (!$session->isAdmin || empty($_GET['y']) || !is_numeric($_GET['y']) || empty($_GET['m']) || !is_numeric($_GET['m'])) die;

$year = $_GET['y'];
$month = $_GET['m'];

$im = imagecreate(800, 240);
$background_color = imagecolorallocate($im, 20, 20, 20);

$month_start = mktime(0, 0, 0, $month, 1, $year);
$month_days  = date('t', $month_start);
$month_end   = mktime(0,0,0, $month+1, 1, $year);

$q = 'SELECT * FROM tblStatistics WHERE time BETWEEN "'.sql_datetime($month_start).'" AND "'.sql_datetime($month_end).'" ORDER BY time ASC';
$list = $db->getArray($q);

//Find max number
$max_logins = 0;
foreach($list as $row) {
	if ($row['logins'] > $max_logins) $max_logins = $row['logins'];
}

$top_y = 20;
$bottom_y = 200;
$start_x = 24;

$txt_col = imagecolorallocate($im, 233, 220, 110);	//yellowish
$grid_col = imagecolorallocate($im, 140, 40, 40);	//dark gray
$logins_col = imagecolorallocate($im, 44, 255, 110);	//greenish
$regs_col = imagecolorallocate($im, 44, 44, 255);	//blueish

imagestring($im, 2, 754,0, date('Y-m', $month_start), $txt_col);

imagestring($im, 2, 764, $bottom_y-10, 'day of', $txt_col);
imagestring($im, 2, 770, $bottom_y, 'month', $txt_col);

imagestring($im, 2, $start_x, $bottom_y + 14, 'legend:', $txt_col);

imagefilledrectangle($im, $start_x + 50, $bottom_y+18, $start_x + 56, $bottom_y+24, $logins_col);
imagestring($im, 2, $start_x + 66, $bottom_y + 14, '- logins', $txt_col);

if ($max_logins) {
	$scale = (($bottom_y-$top_y) / $max_logins);
} else $scale = ($bottom_y-$top_y);

imagestring($im, 2, 0, 0, 'scale', $txt_col);

$label = $max_logins;
for ($y = 0; $y <= $bottom_y; $y+= $scale) {
	imagestring($im, 2, $start_x-12, ($top_y-8) + $y, $label, $txt_col);
	imageline($im, $start_x, $top_y + $y, $start_x+($month_days*24), $top_y + $y, $grid_col);		//draw horizontal grid
	$label--;
	if ($label < 0) break;	//XXX hack, shouldnt happen but it does sometimes, because of $scale is wierd
}

foreach ($list as $row) {
	$timestamp = strtotime($row['time']);
	$day = date('j', $timestamp);
	$hour = date('G', $timestamp);

	$x = $start_x+(($day-1)*24);

	if ($hour == 0) {
		imageline($im, $x, 20, $x, $bottom_y, $grid_col);	//draw vertical grid
	}
/*
	imagesetpixel($im, $x + $hour, $bottom_y - ($row['registrations']*$scale), $regs_col);
	imagesetpixel($im, $x + $hour, $bottom_y - ($row['logins']*$scale), $logins_col);
*/
	imageline($im, $x + $hour, $bottom_y, $x + $hour, $bottom_y - ($row['logins']*$scale), $logins_col);
	//imageline($im, $x + $hour, $bottom_y, $x + $hour, $bottom_y - ($row['registrations']*$scale), $regs_col);

	imagestring($im, 2, $x+8, $bottom_y, $day, $txt_col);
}

header('Content-type: image/png');
imagepng($im);
imagedestroy($im);
?>
