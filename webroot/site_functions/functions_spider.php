<?
	//functions for implementing a web spider / web crawler

	//Tar en sträng med parametrar till en html-tagg, och returnerar en array med dom parsade
	function parse_html_parameters($str)
	{
		$arr = array();
		
		//Normalize parameters
		$str = str_replace(' =', '=', $str);
		$str = str_replace('= ', '=', $str);

		//Parse parameters. todo: gör till en funktion
		do {
			$param_pos1 = strpos($str, '=');
			$param_name = substr($str, 0, $param_pos1);
			
			if (substr($str, $param_pos1+1, 1) == '"') {
				//handle "quouted" parameter
				$param_pos2 = strpos($str, '"', $param_pos1+2); //hitta nästföljande ", EFTER det öppnande "
				$param_value = substr($str, $param_pos1+2, $param_pos2-$param_pos1-2);
				$arr[$param_name] = $param_value;

				$str = trim(substr($str, $param_pos2+1));

				//echo 'quoted: '.$param_name.' = '.$param_value.'<br>';
				//echo 'resterande: '.$str.'<br>';
			} else {
				//handle non-quoted parameter
				$param_pos2 = strpos($str, ' ');
				if ($param_pos2 !== false) {
					//Det finns ytterligare parametrar efter denna
					$param_value = substr($str, $param_pos1+1, $param_pos2-$param_pos1-1);
					$arr[$param_name] = $param_value;
					$str = trim(substr($str, $param_pos2+1));

					//echo 'nonquoted: '.$param_name.' = '.$param_value.'<br>';
					//echo 'resterande: '.$str.'<br>';
				} else {
					//Detta är sista parametern
					$param_value = substr($str, $param_pos1+1);
					$arr[$param_name] = $param_value;
					//echo 'noquoted: '.$param_name.' = '.$param_value.'<br>';

					$str = '';
				}
			}
		} while ($str);

		return $arr;
	}

	//Takes a user inputted URL and cleans it up, returning clean url in $url parameter & a parse_url() array
	//Also adds 'path_only', that returns 'path', but with script/file name removed. /dir/file.php becomes /dir/
	//Also adds 'file_ext', that returns the file extension if its a filename in the URL
	function nice_parse_url(&$url)
	{
		$url = trim($url);

		$pos = strpos($url, '://');
		if ($pos === false) {
			$url = 'http://'.$url;
		}

		$arr = parse_url($url);

		$arr['file_ext'] = '';

		if (!isset($arr['path'])) {
			$url .= '/';
			$arr = parse_url($url);
			$arr['path_only'] = '/';
		} else {
			$pos = strrpos($arr['path'], '/');	//last position of a /
			$dir = substr($arr['path'], 0, $pos+1);
			$arr['path_only'] = $dir;
			
			//Find the file extension, if one exists
			$filename = substr($arr['path'], $pos+1);
			$pos2 = strpos($filename, '.');			//fixme: är detta korrekt, tänk med en fil med flera punkter i namnet, strrpos() istället?
			if ($pos2 !== false) $arr['file_ext'] = substr($filename, $pos2);
		}

		if ($arr['scheme'] != 'http' && $arr['scheme'] != 'https') {
			echo 'Unsupported scheme: <b>'.$arr['scheme'].'</b> in URL <b>'.$url.'</b><br>';
			return false;
		}

		return $arr;
	}
	
	//Returns an array with all URL's it encounters (as seen in the HTML)
	function extract_filenames($data)
	{
		//Normalisera HTML koden
		$data = str_replace("\n", " ", $data);
		$data = str_replace("\r", " ", $data);
		$data = str_replace("\t", " ", $data);
	
		$files_found = array();
	
		//Upprepar "  " -> " " tills strängen inte ändras
		do {
			$org_data = $data;
			$data = str_replace("  ", " ", $data);
		} while ($org_data != $data);
	
		//Parsa taggar, identifiera relevanta taggar

		//echo '<pre>';
		do {
			$pos1 = strpos($data, '<');					//Find first opening bracket
			$pos2 = strpos($data, '>', $pos1);	//Find first closing bracket occuring after $pos1
	
			if ($pos1 === false || $pos2 === false) break;
	
			$tag = trim(substr($data, $pos1+1, $pos2-$pos1-1));
	
			$tag_pos = strpos($tag, ' ');
			if ($tag_pos) {
				$tag_name = strtolower(substr($tag, 0, $tag_pos));
	
				$tag_params_org = substr($tag, $tag_pos+1);
				$tag_params = parse_html_parameters($tag_params_org);
	
			} else {
				$tag_name = $tag;
				$tag_params = array();
			}
	
			switch ($tag_name)
			{
				case 'a':				//<a href="filename.html">
				case 'area':		//<area href="blog-rss.php" />
					if (empty($tag_params['href'])) break;

					$pos = strpos($tag_params['href'], '#');
					if ($pos !== false) $tag_params['href'] = substr($tag_params['href'], 0, $pos);
					if (!$tag_params['href']) break;

					if (substr($tag_params['href'], 0, 7) == 'mailto:') break;
	
					if (!in_array($tag_params['href'], $files_found)) {
						//echo '* Located new link: '.$tag_params['href'].'<br>';
						$files_found[] = $tag_params['href'];
					}
					break;

				case 'link':			//<link href="stylesheet.css" rel="stylesheet" type="text/css">
					break;

				case 'script':		//<script src="/inc/global.js" language="javascript" type="text/javascript">
					break;
			}
	
		} while ( $data = trim(substr($data, $pos2)) );
		
		return $files_found;
	}
	

	//Takes a array of URL's, relative & absolute, applies "$url" basename to the relative ones and returns the array
	//$url must be a proper URL that has already been processed with nice_parse_url(), we expect it to look like this: http://www.domain.com/
	function generate_absolute_urls($list, $url)
	{
		$url_arr = nice_parse_url($url);
		
		$result = array();
		
		foreach ($list as $val)
		{
			if (strpos($val, '://') === false)
			{
				if (substr($val, 0, 1) == '/')
				{
					//Is relative url starting with a /? Then relative path is from the root
					$result[] = $url_arr['scheme'].'://'.$url_arr['host'].$val;
				}
				else if (substr($val, 0, 2) == './')
				{
					if ($val == './') {
						//This is the full path. Meaning / of current relative path
						$val = '/';
					} else {
						//This is an unusual relative addressing, meaning the file is in the current level
						$val = substr($val, 2);
					}

					$result[] = $url_arr['scheme'].'://'.$url_arr['host'].$val;
				}
				else if (substr($val, 0, 3) == '../')
				{
					//Is relative url starting with a ../? Then relative path is 1 level below $url's level
					//echo '<b>'.$url.'</b> referrs to '.$val.'<br><br>';
					
					$val = substr($val, 3);		//remove the ../ part
					
					$temp = $url_arr['path_only'];
					if (substr($temp, -1) == '/') $temp = substr($temp, 0, -1);
					if ($temp) {
						$pos = strrpos($temp, '/');
						if ($pos !== false) {
							$result[] = $url_arr['scheme'].'://'.$url_arr['host'].substr($temp, 0, $pos+1).$val;
							//echo 'temp became: '.$temp.'<br>';
						} else {
							//fixme: tror detta är unreachable code...
							echo 'ERROR! obscure case! temp='.$temp.'<br>';
						}

					} else {
						//We're at the top level, fixme! is this correct?
						$xxx = $url_arr['scheme'].'://'.$url_arr['host'].$val;
						echo '<b>TOP LEVEL ../ UNIMPLEMENTED: '.$xxx.'</b><br>';
					}

				}
				else
				{
					//This is a normal relative path
					$result[] = $url_arr['scheme'].'://'.$url_arr['host'].$url_arr['path_only'].$val;
				}

			} else {
				$temp = nice_parse_url($val);

				//kolla om det är samma som $url, eller om det är en extern länk
				//todo: ska detta kollas i ett senare steg istället?
				if ($temp['host'] == $url_arr['host']) {
					//This is a normal absolute path
					$result[] = $val;
				} else {
					//echo '<b>IGNORING EXTERNAL URL: '.$temp['host'].'<br></b>';
				}
			}
		}

		return $result;
	}
	
	//This function acts like file_get_contents() very much, except that instead of returning FALSE on failure,
	//it will set $errno to the HTTP error number returned. $errno = 9999 if connection could not be established
	function get_http_contents($url, &$errno)
	{
		//todo: $site ska nog inte va global, utan en parameter..
		global $site, $config, $http_request_counter;
		
		$errno = 0;

		$host = nice_parse_url($url);
		
		if (!empty($host['file_ext']) && !in_array($host['file_ext'], $config['allowed_extensions'])) {
			echo '<b>Skipping download of '.$host['file_ext'].' (from '.$url.')</b><br>';
			return false;
		}

		echo 'Downloading and parsing '.$url.' ...<br>';
		
		if ($http_request_counter > 100) {
			echo 'http request '.$http_request_counter.', dying<br>';
			//sleep(1);
			die;
		}

		//todo: handle unusual ports, https etc (?)
		$fp = fsockopen($host['host'], 80, $errno, $errstr, 30);
		if (!$fp) {
			echo "$errstr ($errno)<br />\n";
			$errno = 9999;
			return false;
		}
		
		//fixme: url parametrar ignoreras här ?!?!?!
		$file = $host['path'];
		
		$header  = "GET ".$file." HTTP/1.0\r\n";
		$header .= "Host: ".$host['host']."\r\n";
		$header .= "User-Agent: Agent Interactive Web Spider (www.agentinteractive.se)\r\n";
		$header .= "Connection: close\r\n\r\n";
		fwrite($fp, $header);

		$result = '';
		while (!feof($fp)) {
			$result .= fgets($fp, 128);
		}
		fclose($fp);
		
		//Cut off header
		$pos = strpos($result, "\r\n\r\n");
		$header = substr($result, 0, $pos);
		$body = substr($result, $pos);

		$arr = explode("\r\n", $header);
		//echo '<pre>'; print_r($arr);
		
		//todo: kanske parsa upp header-elementen mer tillgängliga som keys i en array,om vi ska göra mycket med header-datan
		foreach ($arr as $val) {
			if (substr($val, 0, 9) == 'HTTP/1.1 ') $errno = intval(substr($val, 9));
		}
		
		if ($errno == 0) {
			echo '<b>ERROR! Failed to extract HTTP error number from request for '.$url.'</b><br>';
		}

		//HTTP/1.1 200 OK
		if ($errno == 200) $errno = 0;
		
		//HTTP/1.1 302 Object Moved
		if ($errno == 302)
		{
			$new_loc = '';
			foreach ($arr as $val) {
				if (substr($val, 0, 10) == 'Location: ') $new_loc = substr($val, 10);
			}

			if ($new_loc) {
				echo '<b>302 object moved from '.$url.' to: '.$new_loc.'</b><br>';
				$site['302'][$url] = $new_loc;
			} else {
				echo '<b>ERROR: HTTP header 302 object moved didnt specify a new Location:</b><br>';
			}
			$errno = 0;
		}

		//404 - file not found
		if ($errno == 404)
		{
			echo '<b>File not found: '.$url.'</b><br>';
			$site['404'][$url] = true;
			$errno = 0;
		}

		$http_request_counter++;

		return $body;
	}
	
	/* Tar en array med absoluta url:er. Genererar en robots.txt utifrån url:erna */
	function generate_robots_txt($host, $arr)
	{
		$listed = array();

		$result =
			"# http://".$host."/robots.txt\n".
			"# Generated by ai-crawler at ".date('Y-m-d H:i')."\n".
			"\n".
			"User-agent: *\n";

		for ($i=0; $i<count($arr); $i++)
		{
			$parsed = nice_parse_url($arr[$i]);
			
			if ($parsed['host'] != $host) {
				echo 'generate_robots_txt() ERROR: IGNORING HOST '.$parsed['host']."\n";
				continue;
			}
			
			$parsed['dir'] = dirname($parsed['path']);
			if ($parsed['dir'] == '\\') $parsed['dir'] = '/';

			if (!in_array($parsed['dir'], $listed)) {
				$result .= "Allow: ".$parsed['dir']."\n";

				$listed[] = $parsed['dir'];
			}
		}

		return $result;
	}
	
	/* Tar en array med absoluta url:er. Genererar en Google XML sitemap utifrån url:erna
		Implementering enligt https://www.google.com/webmasters/tools/docs/en/protocol.html

		OBS: Utgår från att arrayen innehåller en lista med unika url:er för samma domän.
	*/
	function generate_google_sitemap($arr)
	{
		$result =
			'<?xml version="1.0" encoding="UTF-8"?>'."\n".
			'<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'."\n\n";

		for ($i=0; $i<count($arr); $i++)
		{
			$result .= "\t<url>\n";

			$result .= "\t\t<loc>".$arr[$i]."</loc>\n";									//Required
			//$result .= "\t\t<lastmod>".date('Y-m-d')."</lastmod>\n";		//Optional
			//$result .= "\t\t<changefreq>daily</changefreq>\n";					//Optional
			//$result .= "\t\t<priority>0.5</priority>\n";								//Optional

			$result .= "\t</url>\n\n";
		}

  	$result .= '</urlset>';

  	return $result;
	}

?>