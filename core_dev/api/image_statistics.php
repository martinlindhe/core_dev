<?
	require_once('find_config.php');

	if (!$session->isAdmin || empty($_GET['y']) || !is_numeric($_GET['y']) || empty($_GET['m']) || !is_numeric($_GET['m'])) die;

	$year = $_GET['y'];
	$month = $_GET['m'];

	$im = imagecreate(800, 260);
	$background_color = imagecolorallocate($im, 20, 20, 20);

	$month_start = mktime(0, 0, 0, $month, 1, $year);
	$month_days  = date('t', $month_start);
	$month_end   = mktime(23,59,59,$month, $month_days, $year);

	$q = 'SELECT * FROM tblStatistics WHERE time BETWEEN "'.sql_datetime($month_start).'" AND "'.sql_datetime($month_end).'"';
	$list = $db->getArray($q);

	//Find max number
	$max_logins = 0;
	foreach($list as $row) {
		if ($row['logins'] > $max_logins) $max_logins = $row['logins'];
	}

	$bottom_y = 242;
	$start_x = 24;

	$logins_col = imagecolorallocate($im, 44, 255, 110);	//greenish
	$txt_col = imagecolorallocate($im, 233, 220, 110);	//yellowish
	$grid_col = imagecolorallocate($im, 40, 40, 40);	//dark gray

	imagestring($im, 2, 0, 0, 'scale: '.$max_logins, $txt_col);	//scale

	imagestring($im, 2, 764, $bottom_y-10, 'day of', $txt_col);
	imagestring($im, 2, 770, $bottom_y, 'month', $txt_col);

	foreach ($list as $row) {
		$timestamp = strtotime($row['time']);
		$day = date('j', $timestamp);
		$hour = date('G', $timestamp);

		$logins = $row['logins'];

		$x = $start_x+(($day-1)*24);

		if ($hour == 0) {
			imageline($im, $x, 20, $x, $bottom_y, $grid_col);
		}

		imagesetpixel($im, $x + $hour, $bottom_y - $logins, $logins_col);

		imagestring($im, 2, $x, $bottom_y, $day, $txt_col);
	}

	header('Content-type: image/png');
	imagepng($im);
	imagedestroy($im);
?>