<?
	require_once('config.php');

	require('design_head.php');

	$list = unserialize(file_get_contents('dump.txt'));

	//d($list);

	$url = array();
	foreach($list as $script => $queries)
	{
		//d($script);
		foreach($queries as $key => $val)
		{
			//echo $key . ' = '.$val.'<br/>';

			//generate random urls to test with
			if ($val == 'numeric') {
				$url[] = 'http://localhost'.$script.'?'.$key.'=123\'a';
				$url[] = 'http://localhost'.$script.'?'.$key.'=456"a';
				$url[] = 'http://localhost'.$script.'?'.$key.'=789a';
			} else {
				//echo 'dont know what to do<br/>';
			}
		}
	}

	d($url);

	foreach($url as $key => $val)
	{
		echo '<h3>'.$val.'</h3>';
		$x = file_get_contents($val);
		echo strip_tags($x);
	}

	require('design_foot.php');
?>