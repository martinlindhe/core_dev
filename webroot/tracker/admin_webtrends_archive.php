<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');

	$dir = 'webtrends/';
	$year = 2006;

	$browser_trends = '';
	$searchengine_trends = '';

	for ($i=1; $i<=12; $i++) {

		if ($i < 10) $prefix_i = '0'.$i;
		else $prefix_i = $i;

		$file_browsers			= $dir.'browsers_'.$year.'.'.$prefix_i.'.png';
		$file_searchengines	= $dir.'searchengines_'.$year.'.'.$prefix_i.'.png';

		if (is_file($file_browsers)) {
			$browser_trends .= '<img src="'.$file_browsers.'" alt="">';
		}

		if (is_file($file_searchengines)) {
			$searchengine_trends .= '<img src="'.$file_searchengines.'" alt="">';
		}
	}

	echo '<h2>Web browser trends archive for '.$year.'</h2>';
	echo $browser_trends;

	echo '<h2>Search engine trends archive for '.$year.'</h2>';
	echo $searchengine_trends;

	include('design_foot.php');

?>