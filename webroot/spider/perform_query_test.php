<?
	require_once('config.php');

	require('design_head.php');

	$list = unserialize(file_get_contents('dump.txt'));

	/* Maps up an array of all different script paths and their different parameters, for later use */
	$path = array();
	foreach($list as $url)
	{
		$parsed = parse_url($url);

		if (!isset($path[$parsed['path']])) $path[ $parsed['path'] ] = array();
		if (!empty($parsed['query'])) $path[ $parsed['path'] ] [] = $parsed['query'];
	}
	//d($path);

	/* Further maps up the array, and figures out each parameter name for each script, and the default data type */
	$scripts = array();
	foreach($path as $row => $val)
	{
		//Set this value if you want the resulting $scripts array to contain entries for script-files without parameters aswell
		//$scripts[ $row ] = array();
		foreach($val as $query)
		{
			$res = explode('=', $query);
			if (isset($res[1])) {
				//analyserar datatyp
				if (is_numeric($res[1])) {
					if (!empty($scripts[$row][$res[0]]) && $scripts[$row][$res[0]] != 'numeric') {
						$scripts[$row][$res[0]] = 'mixed';
					} else {
						$scripts[$row][$res[0]] = 'numeric';
					}
				} else {
					$scripts[$row][$res[0]] = 'unknown ('.$res[1].')';
				}
			} else {
				$scripts[$row][$res[0]] = 'unset';
			}
		}
	}
	d($scripts);

	$url = array();
	foreach($scripts as $script => $queries)
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
		//$x = get_http_contents($val, $errno);
		//echo strip_tags($x);
	}

	require('design_foot.php');
?>