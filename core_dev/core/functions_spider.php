<?
	/*
		functions for implementing a web spider / web crawler

		crawl_site.php:
		todo: allow input of site to crawl from browser
		todo: store result of the crawl in database (currently in dump.txt)

		perform_query_test.php:
			* leta efter keywords som "warning / error" i resultaten

		todo: förbättra hittandet av parametrar och url, stöd även post-parametrar samt javascript-url
		todo: sessioner, eller iaf till en början en möjlighet att attacha en hårdkodad cookie / get-parameter som fejkar session handling
		todo/senare: definiera login-url, scriptet klurar ut login-forumuläret å gissar username / password parametrar
			sen med detta automatiskt logga in i systemet med angivet user/pwd och auto-behåll sessionen hela tiden

		todo: skippa denna typ av url:
				Skipping download of http://www.aftonbladet.se/javascript:logout();) - Using cache
				Skipping download of .loginform.submit(); (from http://www.unicorn.tv/login/javascript:document.loginform.submit();)
				Skipping download of .jsp'); (from http://www.dn.se/javascript:openBildspel('/DNet/road/Classic/article/47/jsp/bildspel.jsp');)

		todo: parameter parsing funkar ej som den ska:
			http://www.dn.se/DNet/jsp/polopoly.jsp?d=147&a=650638
				=>
    [/DNet/jsp/polopoly.jsp] => Array
        (
            [a] => numeric
            [d] => unknown (2836&a)
        )

    [101] => http://www.dn.se/DNet/jsp/polopoly.jsp?d=1348&a=66290&previousRenderType=3
    [86] => http://www.dn.se/DNet/jsp/polopoly.jsp?d=772&homeView=true
    [13] => http://www.dn.se/DNet/jsp/polopoly.jsp?d=145&a=617242&tab=b&period=bmonth

		Features:
			Keeps an internal web page cache for faster operation

		Notice:
			This script may use some RAM. Tweak php.ini and set memory_limit to at least 32M
	*/

	//framtida alternativ: skicka en HEAD request till webbservern och kolla "Content-Type" responsen.
	//PDF:		Content-Type: application/pdf
	$config['spider']['allowed_extensions'] = array('.html', '.htm', '.asp', '.aspx', '.jsp', '.php', '.php4', '.php5', '.pl');

	$config['spider']['user_agent'] = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; sv-SE; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3';

	$config['spider']['cache_age'] = 3600*24;	//24 hour

	$config['spider']['max_http_requests'] = 50;	//max number of http requests to do during one execution of the script, to avoid server overload

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
					$str = trim(substr($str, $param_pos2+1));

					//echo 'resterande: '.$str.'<br>';
				} else {
					//Detta är sista parametern
					$param_value = substr($str, $param_pos1+1);

					$str = '';
				}
				//echo 'nonquoted: '.$param_name.' = '.$param_value.'<br>';
				$arr[$param_name] = $param_value;
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
		$url = str_replace('\"', '', $url);

		$pos = strpos($url, '://');
		if ($pos === false) {
			$url = 'http://'.$url;
		}

		$arr = parse_url($url);
		if (!isset($arr['scheme'])) {
			echo 'Scheme undefined!! '.$url.'<br>';
			return false;
		}

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
			if ($arr['scheme'] != 'javascript') {
				echo 'Unsupported scheme: <b>'.$arr['scheme'].'</b> in URL <b>'.$url.'</b><br>';
			}
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

		do {
			$org_data = $data;
			$data = str_replace("  ", " ", $data);
		} while ($org_data != $data);

		//Parsa taggar, identifiera relevanta taggar

		do {
			$pos1 = strpos($data, '<');					//Find first opening bracket
			$pos2 = strpos($data, '>', $pos1);	//Find first closing bracket occuring after $pos1

			if ($pos1 === false || $pos2 === false) break;

			$tag = trim(substr($data, $pos1+1, $pos2-$pos1-1));

			$tag_pos = strpos($tag, ' ');
			if ($tag_pos) {
				$tag_name = strtolower(substr($tag, 0, $tag_pos));

				$tag_params_org = substr($tag, $tag_pos+1);
				if (strpos($tag_params_org, 'javascript:') === false) {
					$tag_params = parse_html_parameters($tag_params_org);
				} else {
					$tag_params = array();
				}

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
	//it will set $errno to the HTTP error number returned
	function get_http_contents($url, &$errno)
	{
		//todo: $site ska nog inte va global, utan en parameter..
		global $db, $site, $config, $http_request_counter;

		$errno = 0;
		//echo 'get_http_contents('.$url.')<br>';

		//check if cached version is not out of date
		$q = 'SELECT body FROM tblSpiderCache WHERE url="'.$db->escape($url).'" AND timeCreated >= NOW()-'.$config['spider']['cache_age'];
		$body = $db->getOneItem($q);
		if ($body) {
			echo '<b>Skipping download of '.$url.')</b> - Using cache<br>';
			return $body;
		}

		$host = nice_parse_url($url);

		if (!empty($host['file_ext']) && !in_array($host['file_ext'], $config['spider']['allowed_extensions'])) {
			echo '<b>Skipping download of '.$host['file_ext'].'</b> (from '.$url.')<br>';
			return false;
		}

		echo 'Downloading and parsing '.$url.' ...<br>';

		if ($http_request_counter >= $config['spider']['max_http_requests']) {
			echo 'Done '.$http_request_counter.' HTTP requests now. Please reload this page to do some more, dont want to overload the target server<br>';
			die;
		}

		//todo: handle unusual ports, https etc (?)
		$fp = fsockopen($host['host'], 80, $errno, $errstr, 30);
		if (!$fp) {
			echo "$errstr ($errno)<br />\n";
			return false;
		}

		//fixme: url parametrar ignoreras här ?!?!?!
		$file = $host['path'];

		$header  = "GET ".$file." HTTP/1.0\r\n";
		$header .= "Host: ".$host['host']."\r\n";
		$header .= "User-Agent: ".$config['spider']['user_agent']."\r\n";
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

		//todo: kanske parsa upp header-elementen mer tillgängliga som keys i en array,om vi ska göra mycket med header-datan
		foreach ($arr as $val) {
			if (substr($val, 0, 9) == 'HTTP/1.1 ') $errno = intval(substr($val, 9));
		}

		if ($header && $errno == 0) {
			d($header);
			d($arr);
			die('<b>ERROR! Failed to extract HTTP error number from request for '.$url.'</b><br>');
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

		//403 - forbidden
		if ($errno == 403)
		{
			echo '<b>403 - Forbidden: '.$url.'</b><br>';
			$site['403'][$url] = true;
			$errno = 0;
		}

		//404 - file not found
		if ($errno == 404)
		{
			echo '<b>File not found: '.$url.'</b><br>';
			$site['404'][$url] = true;
			$errno = 0;
		}

		if (!$errno) {
			//delete old revisions
			$q = 'DELETE FROM tblSpiderCache WHERE url="'.$db->escape($url).'"';
			$db->delete($q);

			//store header & body in database
			$q = 'INSERT INTO tblSpiderCache SET url="'.$db->escape($url).'",header="'.$db->escape($header).'",body="'.$db->escape($body).'",sha1="'.sha1($body).'",timeCreated=NOW()';
			$db->insert($q);
		} else {
			echo 'errno: '.$errno.'<br>';
		}

		$http_request_counter++;
		//echo 'done<br/>';

		return $body;
	}

	/* Tar en array med absoluta url:er. Genererar en robots.txt utifrån url:erna */
	function generate_robots_txt($host, $arr)
	{
		$listed = array();

		$result =
			"# http://".$host."/robots.txt\n".
			"# Generated by crawler at ".date('Y-m-d H:i')."\n".
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