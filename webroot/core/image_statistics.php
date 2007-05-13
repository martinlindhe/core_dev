<?
	require_once('find_config.php');

	if (!$session->isAdmin || empty($_GET['y']) || !is_numeric($_GET['y']) || empty($_GET['m']) || !is_numeric($_GET['m'])) die;

	$year = $_GET['y'];
	$month = $_GET['m'];

	header('Content-type: image/png');
	$im = imagecreate(400, 260);
	$background_color = imagecolorallocate($im, 20, 20, 20);

	$month_start = mktime(0, 0, 0, $month, 1, $year);
	$month_days  = date('t', $month_start);
	$month_end   = mktime(23,59,59,$month, $month_days, $year);

	$q = 'SELECT * FROM tblStatistics WHERE time BETWEEN "'.sql_datetime($month_start).'" AND "'.sql_datetime($month_end).'"';
	$list = $db->getArray($q);
	//d($list);

	//Find max numbers
	/*
	$max_logins = 0;
	foreach($list as $row) {
		if ($row['logins'] > $max_logins) $max_logins = $row['logins']; 
	}*/

	//echo 'max logins:'. $max_logins;

	$col = imagecolorallocate($im, 233, 220, 110);

	for ($i=0; $i<24; $i++) {
		imagestring($im, 2, 0, $i*10, $i, $col);
	}

	$bottom_y = 242;
	$start_x = 10;

	foreach($list as $row) {
		$timestamp = strtotime($row['time']);
		$day = date('j', $timestamp);
		$hour = date('G', $timestamp);
		$logins = $row['logins'];
		
		$x = $start_x+($day*10);
		$y = ($hour*10);
		//imageline($im, $x, $y, $x+9, $y, $col);
		if ($logins) $use_col = $col;
		else $use_col = imagecolorallocate($im, 80, 80, 80);
		imagestring($im, 2, $x, $y, $logins, $use_col);

		imagestring($im, 2, $start_x+($day*10), $bottom_y, $day, $col);
	}

	imagepng($im); imagedestroy($im);
?>