<?
	function cache_read($url)
	{
		$cache_name = 'cache/'.basename($url);

		if (file_exists($cache_name)) {

			$diff = time() - filectime($cache_name);
			echo 'using '.$diff.' seconds old cache<br/>';
			return file_get_contents($cache_name);

		} else {
			echo 'reading new '.$url.'<br/><br/>';
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

	//generates a VLC-compilant XSPF playlist
	function generate_xspf_playlist($items)
	{
		$fp = fopen('cache/ab.xspf', 'w');
		
		fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>');
		fwrite($fp, '<playlist version="0" xmlns="http://xspf.org/ns/0/">');
			fwrite($fp, '<trackList>');

			foreach ($items as $row) {
				fwrite($fp, '<track>');
				fwrite($fp, '<location>'.$row['wmp'].'</location>');
				fwrite($fp, '<duration>'.($row['duration']*1000).'</duration>');	//in milliseconds
				fwrite($fp, '<title><![CDATA['.$row['desc'].']]></title>');
				fwrite($fp, '</track>');
			}

			fwrite($fp, '</trackList>');
		fwrite($fp, '</playlist>');

		fclose($fp);
	}



	$name = 'http://wwwc.aftonbladet.se/special/webbtv/xml2/senaste.xml';
	$news = array();
	$items = array();
	$ptr = 0;// $items array pointer

	$data = cache_read($name);

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
?>