<?
	require_once('find_config.php');

	if (!$session->isAdmin || empty($_GET['y']) || !is_numeric($_GET['y']) || empty($_GET['m']) || !is_numeric($_GET['m'])) die;

	$year = $_GET['y'];
	$month = $_GET['m'];

	//header('Content-type: image/png');
	$im = imagecreate(500, 200);
	$background_color = imagecolorallocate($im, 20, 20, 20);
	$text_color = imagecolorallocate($im, 233, 220, 110);
	imagestring($im, 1, 5, 5,  "A Simple Text String", $text_color);


	$month_start = mktime(0, 0, 0, $month, 1, $year);
	$month_days  = date('t', $month_start);
	$month_end   = mktime(23,59,59,$month, $month_days, $year);

	$q = 'SELECT * FROM tblStatistics WHERE time BETWEEN "'.sql_datetime($month_start).'" AND "'.sql_datetime($month_end).'"';
	$list = $db->getArray($q);
	d($list);

	for ($day=1; $day<= $month_days; $day++) {
		//Generate stats for each day
		for ($h=1; $h<=24; $h++) {

			//$logins = $list[$
			$col = imagecolorallocate($im, 233, 220, 110);
			imageline($im, ($day*10), $logins*5, ($day*10)+9, $logins*5, $col);

		}
	}




	imagepng($im); imagedestroy($im);
?>