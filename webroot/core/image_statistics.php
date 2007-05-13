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

	$bottom_y = 100;

	foreach($list as $row) {
		$timestamp = strtotime($row['time']);
		$day = date('j', $timestamp);
		$logins = $row['logins'];
		imageline($im, ($day*10), $bottom_y-($logins*10), ($day*10)+9, $bottom_y-($logins*10), $col);

		imagestring($im, 1, $day*10, 110,  $day, $col);
	}

	imagepng($im); imagedestroy($im);
?>