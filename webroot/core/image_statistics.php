<?
	require_once('find_config.php');

	if (!$session->isAdmin || empty($_GET['y']) || !is_numeric($_GET['y']) || empty($_GET['m']) || !is_numeric($_GET['m'])) die;

	$year = $_GET['y'];
	$month = $_GET['m'];

	header('Content-type: image/png');
	$im = imagecreate(500, 200);
	$background_color = imagecolorallocate($im, 20, 20, 20);

	$month_start = mktime(0, 0, 0, $month, 1, $year);
	$month_days  = date('t', $month_start);
	$month_end   = mktime(23,59,59,$month, $month_days, $year);

	$q = 'SELECT * FROM tblStatistics WHERE time BETWEEN "'.sql_datetime($month_start).'" AND "'.sql_datetime($month_end).'"';
	$list = $db->getArray($q);
	//d($list);

	$col = imagecolorallocate($im, 233, 220, 110);

	imagestring($im, 2, 0, 0, '00:00', $col);
	imagestring($im, 2, 0, 20, '06:00', $col);
	imagestring($im, 2, 0, 40, '12:00', $col);
	imagestring($im, 2, 0, 60, '18:00', $col);
	imagestring($im, 2, 0, 80, '24:00', $col);

	$bottom_y = 100;
	$start_x = 30;

	foreach($list as $row) {
		$timestamp = strtotime($row['time']);
		$day = date('j', $timestamp);
		$logins = $row['logins'];
		
		$x = $start_x+($day*10);
		$y = $bottom_y-($logins*10);
		imageline($im, $x, $y, $x+8, $y, $col);

		imagestring($im, 2, $start_x+($day*10), $bottom_y,  $day, $col);
	}

	imagepng($im); imagedestroy($im);
?>