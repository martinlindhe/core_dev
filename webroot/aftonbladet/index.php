<?
	//todo: sortera playlist efter tid innan den skrivs till disk
	//todo: konvertera kategori NÖJE till Nöje. problem med unicode konvertering med ucfirst()


	//ttl = time to live, in minutes (default 240 minute = 4 hour)
	function cache_read($url, $ttl = 240)
	{
		$cache_name = 'cache/'.basename($url);
		
		//echo 'cache diff: '.(time() - filemtime($cache_name)).'<br>';
		//echo 'ttl*60: '.($ttl*60).'<br/>';
		if (file_exists($cache_name) && ((time() - filemtime($cache_name)) < ($ttl*60))) {

			//echo 'using cached '.$url.'<br/>';
			return file_get_contents($cache_name);

		} else {
			//echo 'reading new '.$url.'<br/><br/>';
			$data = file_get_contents($url);
			file_put_contents($cache_name, $data);
			return $data;
		}
	}

	function ROOT_startE($parser, $name, $attrs)
	{
		global $news;
		//echo 'ROOT_startE('.$parser.','.$name.','.$attrs.')<br/>';

		if (count($attrs)) {
			foreach ($attrs as $k => $v) {
				//echo '<font color="#009900">'.$k.'</font>=<font color="#990000">'.$v.'</font><br/>';
				if ($name == 'ROOT' && $k == 'CONTENTPATH') {
					$news = explode('_', $v);
				}
			}
		}
	}

	function ROOT_endE($parser, $name)
	{
		//echo 'ROOT_endE('.$parser.','.$name.')<br/>';
	}



	function NEWS_startE($parser, $name, $attrs)
	{
		global $items, $ptr;
		//echo 'NEWS_startE('.$parser.','.$name.','.$attrs.')<br/>';

		if (count($attrs)) {
			foreach ($attrs as $k => $v) {
				//echo '<font color="#009900">'.$k.'</font>=<font color="#990000">'.$v.'</font><br/>';
				
				if ($name == 'ROOT' && $k == 'DATETIME') $items[$ptr]['time'] = strtotime($v);		//2007-08-15 17:32:00
				if ($name == 'CONTENT' && $k == 'NAME') $items[$ptr]['category'] = $v;		//kateoginamn, "Nyheter"
				if ($name == 'CONTENT' && $k == 'IMAGE') $items[$ptr]['image'] = $v;
				if ($name == 'CONTENT' && $k == 'HEAD') $items[$ptr]['head'] = $v;
				if ($name == 'CONTENT' && $k == 'DESC') $items[$ptr]['desc'] = $v;
				if ($name == 'CONTENT' && $k == 'DURATION') $items[$ptr]['duration'] = $v;	//in seconds
				if ($name == 'CONTENT' && $k == 'WMP') $items[$ptr]['wmp'] = $v;
				if ($name == 'CONTENT' && $k == 'QT') $items[$ptr]['qt'] = $v;
			}
		}

		if ($name == 'CONTENT') $ptr++;
	}

	function NEWS_endE($parser, $name)
	{
		//echo 'NEWS_endE('.$parser.','.$name.')<br/>';
	}
	
	function niceTime($timestamp)
	{
		if (!is_numeric($timestamp) || !$timestamp) return 'BAD TIME';

		$datestamp	= mktime (0,0,0,date('m',$timestamp), date('d',$timestamp), date('Y',$timestamp));
		$yesterday	= mktime (0,0,0,date('m') ,date('d')-1,  date('Y'));
		$tomorrow		= mktime (0,0,0,date('m') ,date('d')+1,  date('Y'));

		$timediff = time() - $timestamp;

		if (date('Y-m-d', $timestamp) == date('Y-m-d')) {
			//Today 18:13
			$result = date('H:i',$timestamp);
		} else if ($datestamp == $yesterday) {
			//Yesterday 18:13
			$result = 'Igår '.date(' H:i',$timestamp);
		} else if ($datestamp == $tomorrow) {
			//Tomorrow 18:13
			$result = 'Imorgon '.date(' H:i',$timestamp);
		} else {
			//2007-04-14 13:22
			$result = date('Y-m-d H:i', $timestamp);
		}

		return $result;
	}

	//generates a VLC-compilant XSPF playlist
	function generate_xspf_playlist($items)
	{
		//echo '<pre>'; print_r($items);

		$fp = fopen('cache/ab.xspf', 'w');
		
		fwrite($fp, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		fwrite($fp, "<playlist version=\"0\" xmlns=\"http://xspf.org/ns/0/\">\n");
			fwrite($fp, "\t<trackList>\n");

			foreach ($items as $row) {
				fwrite($fp, "\t\t<track>\n");
				fwrite($fp, "\t\t\t<location>".$row['wmp']."</location>\n");
				fwrite($fp, "\t\t\t<duration>".($row['duration']*1000)."</duration>\n");	//in milliseconds
				
				$title = niceTime($row['time']).' ['.$row['category'].'] '.$row['head'].' - '.$row['desc'];
				fwrite($fp, "\t\t\t<title><![CDATA[".$title."]]></title>\n");
				fwrite($fp, "\t\t</track>\n");
			}

			fwrite($fp, "\t</trackList>\n");
		fwrite($fp, "</playlist>\n");

		fclose($fp);
	}

	$name = 'http://wwwc.aftonbladet.se/special/webbtv/xml2/senaste.xml';
	$news = array();
	$items = array();
	$ptr = 0;// $items array pointer

	$data = cache_read($name, 5);

	//parse senaste.xml for individual news items
	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "ROOT_startE", "ROOT_endE");

	if (!xml_parse($xml_parser, $data)) {
		die(sprintf("XML error: %s at line %d",
			xml_error_string(xml_get_error_code($xml_parser)),
			xml_get_current_line_number($xml_parser)));
	}
	xml_parser_free($xml_parser);


	//fetch each individual news item
	foreach ($news as $row) {
		$url = 'http://wwwc.aftonbladet.se/special/webbtv/xml2/'.$row.'.xml';
		$data = cache_read($url);

		$xml_parser = xml_parser_create();
		xml_set_element_handler($xml_parser, "NEWS_startE", "NEWS_endE");

		if (!xml_parse($xml_parser, $data)) {
			die(sprintf("XML error: %s at line %d",
				xml_error_string(xml_get_error_code($xml_parser)),
				xml_get_current_line_number($xml_parser)));
		}
		xml_parser_free($xml_parser);
	}
	
	generate_xspf_playlist($items);
	
	echo 'done.';
?>