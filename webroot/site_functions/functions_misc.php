<?
	$config['url_rewrite_length'] = 45;		//max length of visible url's after rewrite to hyperlinks
	$config['url_rewrite_trailing'] = 15;	//number of characters to save at the end of the string
	$config['url_rewrite_redirfile'] = ''; //'redir.php?url=';	//set to '' to disable redir feature

	//fixme: använd antingen preg_match eller eregi enbart, blanda inte...



	if (!function_exists('str_ireplace')) {	//For PHP 4 compatiblity
		function str_ireplace($search, $replacement, $string)
		{
			$delimiters = array(1,2,3,4,5,6,7,8,14,15,16,17,18,19,20,21,22,23,24,25,
			26,27,28,29,30,31,33,247,215,191,190,189,188,187,186,
			185,184,183,182,180,177,176,175,174,173,172,171,169,
			168,167,166,165,164,163,162,161,157,155,153,152,151,
			150,149,148,147,146,145,144,143,141,139,137,136,135,
			134,133,132,130,129,128,127,126,125,124,123,96,95,94,
			63,62,61,60,59,58,47,46,45,44,38,37,36,35,34);
			foreach ($delimiters as $d) {
				if (strpos($string, chr($d))===false) {
					$delimiter = chr($d);
					break;
				}
			}
			if (!empty($delimiter)) {
				return preg_replace($delimiter.quotemeta($search).$delimiter.'i', $replacement, $string);
			}
			
			trigger_error('Homemade str_ireplace could not find a proper delimiter.', E_USER_ERROR);
   	}
	}
	
	if (!function_exists('stripos')) {	//For PHP 4 compatiblity
		function stripos($string, $word)
		{
			$retval = false;
			for($i=0;$i<=strlen($string);$i++)
			{
				if (strtolower(substr($string,$i,strlen($word))) == strtolower($word))
				{
					$retval = true;
				}
			}
			return $retval;
		}
	}








	

	//verifies user entered url
	//http://www.regexlib.com/REDetails.aspx?regexp_id=153: note: denna regexp räknar INTE http://localhost/url.html som valid url
/*
	echo ValidURL('/helo.php?a=4').'<br>'; //should deny
	echo ValidURL('/helo.php?a=4&x=.gif').'<br>'; //should deny
	echo ValidURL('/helo.gif').'<br>'; //should deny if local linking is disallowed. använd regexp för valid url för att verifiera
	echo ValidURL('http://www.kex.com/helo.gif').'<br>'; //should allow
	echo ValidURL('http://www.remotehost.com/display.php?i=helo.gif').'<br>'; //should deny
	echo ValidURL('/buy.gif').'<br>'; //	- Blocked
	echo ValidURL('http://81.216.159.67/buy.gif').'<br>';	//Blocked
	echo ValidURL('http://81.216.159.67/buy.gif').'<br>';						//Blocked
	echo ValidURL('http://81.216.159.67:16200/buy.gif').'<br>';				//Blocked
	echo ValidURL('http://localhost:16200/buy.gif').'<br>';				//Blocked
	echo ValidURL('http://127.0.0.1:16200/buy.gif').'<br>';				//Blocked
	echo ValidURL('http://192.168.0.1:16200/buy.gif').'<br>';				//Blocked
	echo ValidURL('http://www.kex.com/helo.gif').'<br>'; //should allow
	echo ValidURL('http://47.192.168.11/image.gif').'<br>'; //should allow
*/
	define('ALLOW_LOCAL_URLS', 0);
	define('REGEXP_VALID_URL', '^((http|https|ftp)\://)?[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(:[a-zA-Z0-9]*)?/?([a-zA-Z0-9\-\._\?\,\'/\\\+&%\$#\=~])*[^\.\,\)\(\s]$');
	function ValidURL($url)
	{
		if (!ALLOW_LOCAL_URLS && IsThisLocalInclude($url)) return false; //block local includes
		if (eregi(REGEXP_VALID_URL, $url)) return true; //argument is a valid url
		return false;
	}

	define('ALLOW_LOCAL_IMAGES', 0);
	function IsThisLocalInclude($url)
	{
		$parsed = parse_url($url);

		//fixme: snygga upp den här if-satsen för fan
		if (!ALLOW_LOCAL_IMAGES &&			
				(
					empty($parsed['host']) ||			//blocks [img]/image.gif[/img]
						(
							(stripos($_SERVER['SERVER_ADDR'], $parsed['host']) !== FALSE) ||	//if SERVER_ADDR is www.domain.com, this will match: 'subdomain.domain.com' 'domain.com'
							(stripos($parsed['host'], 'localhost') !== FALSE) ||
							(stripos($parsed['host'], '127.0.0.1') !== FALSE) ||
							(substr($parsed['host'], 0, strlen('192.168.')) == '192.168.') ||
							(substr($parsed['host'], 0, strlen('10.0.')) == '10.0.')
						)
				)
			)
		{
			//We suspect this is an attepmt to inject a local url

			//These checks guard us against linking to local files in the following ways: 
			//	[img]/img.gif[/img]			- Blocked
			//	[img]http://ourhostname.com/img.gif[/img]			- Blocked
			//	[img]http://ourip/img.gif[/img]					- Blocked
			//	[img]http://ourip:80/img.gif[/img]			- Blocked
			//	[img]http://localhost/img.gif[/img]			- Blocked
			//	[img]http://127.0.0.1/img.gif[/img]			- Blocked
			//	[img]http://192.168.0.1/img.gif[/img]		- Blocked
			//	[img]http://10.0.0.1/img.gif[/img]			- Blocked
			return true;
		}
		return false;
	}

	//ALLOW_REMOTE_SCRIPTS: Shall we allow url's with ? and & in them basically. if IsThisLocalInclude() is missing url validation somehow,
	//enabling this setting would lead to security flaw, so it is disabled for now. it will make it impossible for members to send parameters into
	//their remote scripts that they try to inject somewhere. but we dont want them to do that anyway.
	define('ALLOW_REMOTE_SCRIPTS', 0);

	function ValidImageURL($url)
	{
		$url = strip_tags($url);
		$allowed_ext = array('.gif', '.jpg', '.png');
		$sep = ini_get('arg_separator.output');
		$ext = substr($url, strrpos($url, '.'));
		$parsed = parse_url($url);

		//Cross-site request forgeries (CSRF) immunity code
		//These checks guard us against the following attacks:
		//	[img]/buy.php?id=400[/img]
		//	[img]/buy.php?id=400&xxx=.gif[/img]
		if (IsThisLocalInclude($url)) return '<img src="/suspected-csrf-image-blocked.png" title="Image blocked: '.$url.'">';

		if (array_search($ext, $allowed_ext) === FALSE) return 'supplied url is unrecognized image';
		
		//It also blocks remote linking to dynamic url's such as:
		//	[img]http://www.imghost.com/display.php?n=image.gif[/img]
		//
		//It does not guard against (it is impossible):
		//	[img]http://remote-host.com/logger-script.gif[/img]
		if (!ALLOW_REMOTE_SCRIPTS && (substr_count($url, '?') || substr_count($url, $sep)))
		{
			return '<img src="/suspected-remote-script-blocked.png">';
		}
		return '<img src="'.$url.'">';
	}

	function Valid_IPv4($ip_addr)	//takes ip address in the form: 123.123.123.123
	{
		$num = "(1?\d\d|2[0-4]\d|25[0-5]|[0-9])";
		if (preg_match("/^".$num."\.".$num."\.".$num."\.".$num."$/",$ip_addr, $matches))
		{
			return true;
			//return $matches[0];
		}
		return false;
	}

	//todo: valid-url, valid-username, valid-password


	/* Returns a string like "2 KiB" */
	function formatDataSize($bytes)
	{
		$units = array('bytes', 'KiB', 'MiB', 'GiB', 'TiB');
		foreach ($units as $unit) {
			if ($bytes < 1024) break;
			$bytes = round($bytes/1024, 1);
		}
		return $bytes.' '.$unit;
	}
	
	/* Returns a string like "2K", meaning 2000 */
	function formatNumberSize($bytes)
	{
		$units = array('', 'K', 'M');
		foreach ($units as $unit) {
			if ($bytes < 1000) break;
			$bytes = round($bytes/1000, 1);
		}
		return $bytes.''.$unit;
	}
	
	function formatNumber($number)
	{
		$decimal_mark = '.';
		$thousand_mark = ',';
		
		//Formats integers with grouped thousands, example: 2005 => 2,005
		if (intval($number) == $number) return number_format($number, 0, $decimal_mark, $thousand_mark);
		
		//Formats floats with 2 decimals and grouped thousands, example: 2005.4791 => 2,005.48
		return number_format($number, 2, $decimal_mark, $thousand_mark);
	}

	function JS_Alert($string)
	{
		echo '<script type="text/javascript">';
		echo 'alert("'.$string.'");';
		echo '</script>';
	}
	
	/* Convert html tags to &lt; &gt; etc, and turns linefeeds to <br> for user input */
	function formatUserData($string)
	{
		$string = htmlentities($string, ENT_COMPAT, 'UTF-8');
		$string = nl2br($string);
		return $string;
	}
	
	function title($title)
	{
		echo '<title>'.$title.'</title>';
	}
	
	/* Creates html so you can split up data on multiple pages */
	/* $pagelimit är antal pages som ska visas utifrån aktuella */
	function pageCounter($itemcount, $max, $url, $page, $pagelimit = '') {

		$result = '';

		if (!$page) {
			$page = 1;
		}

		if ($page > 1) {
			$result = '<a href="'.$url.'&p='.($page - 1).'">&laquo;</a> ';
		}


		$pagecount = round(($itemcount / $max) + 0.49);

		if ($pagecount < 2) {
			return;
		}

		if ($pagelimit && ($pagecount > $pagelimit)) {
			$pagestart = $page;
			if ($pagecount > ($pagelimit+($page-1))) {
				$pagecount = $pagelimit+($page-1);
			}
		} else {
			$pagestart = 1;
		}

		for ($i = $pagestart; $i <= $pagecount; $i++) {
			if ($i == $page) {
				$result .= '<b>' .$i.'</b> ';
			} else {
				$result .= '<a href="'.$url.'&p='.$i.'">'.$i.'</a> ';
			}
		}
		if ($page < $pagecount) {
			$result .= '<a href="'.$url.'&p='.($page + 1).'">&raquo;</a>';
		}

		return $result;
	}


	function nameLink($userId, $userName) {
		$result = '<a href="user_show.php?id='.$userId.'" style="font-weight: normal;">'.$userName.'</a>';
		return $result;

/*
		global $db;
		
		$nickname = getUserdataByFieldname($db, $userId, 'Nickname');

		if (!$nickname) $nickname = '<i>inget nickname</i>';
		else $nickname = '<a href="user_show.php?id='.$userId.'">'.$nickname.'</a>';
		return $nickname;
*/
	}

	function makeCheckBox($form_name, $var_name, $value, $text, $checked = false)
	{
		//funkar i IE7, FF1.5, Opera 9
		$html  = '<span class="checkbox" onClick="document.'.$form_name.'.'.$var_name.'.checked = !document.'.$form_name.'.'.$var_name.'.checked;">';
		$html .= '<input type="checkbox" class="checkbox" name="'.$var_name.'" value="'.$value.'"';
		if ($checked) $html .= ' checked';
		$html .= '>'.$text;
		$html .= "</span>\n";

		return $html;
	}


	function makeRadioButton($form_name, $var_name, $index, $value, $text, $checked = false)
	{
		//funkar i IE7, FF1.5, Opera 9

		//todo: baka in $index i $var_name, som varname = "option[1]"
		$html  = '<span onClick="document.'.$form_name.'.'.$var_name.'['.$index.'].checked = true;">';
		$html .= '<input type="radio" class="radio" name="'.$var_name.'" value="'.$value.'"';
		if ($checked) $html .= ' checked';
		$html .= '>'.$text;
		$html .= "</span>\n";

		return $html;
	}
	
	function MakeBox($title, $content, $min_height = false)
	{
		//detta ger lika stora boxar i IE & ff:
		//return '<div style="height: 100%; background-color: #ccc; margin: 1px;">'.$content.'</div>';
		
		/* If title contains a | character, split up title text in 2 parts, one left- and one right-aligned */
		$pos = strpos($title, '|');
		if ($pos !== false) {
			$part1 = substr($title, 0, $pos);
			$part2 = substr($title, $pos+1);

			$title =
				'<table cellpadding=0 cellspacing=0 style="width: 100%; height: 100%">'.
					'<tr>'.
						'<td>'.$part1.'</td>'.
						'<td align="right">'.$part2.'</td>'.
					'</tr>'.
				'</table>';
		}

		/* If content contains a | character, split up in 2 parts, one top-left and one bottom-right aligned */
		$pos = strpos($content, '|');
		if ($pos !== false) {
			$part1 = substr($content, 0, $pos);
			$part2 = substr($content, $pos+1);
			
			/*$content =
				'<div style="height: 100%; background-color: #CCC;">'.
					$part1.
					'<div style="float: right; height: 15px; text-align: right; background-color: red;">'.$part2.'</div>'.
				'</div>';*/
			
			$content =
				'<table cellpadding=0 cellspacing=0 style="width: 100%; height: 100%">'.
					'<tr>'.
						'<td valign="top">'.$part1.'</td>'.
					'</tr>'.
					'<tr height=12>'.
						'<td valign="bottom" align="right">'.$part2.'</td>'.
					'</tr>'.
				'</table>';
		}

		$right_hack = '';
		if ($min_height) {
			//min-height hack
			$right_hack = '<div style="height: '.$min_height.'px; float: right; width: 1px;"></div>';
		}

		$str =
		'<table class="small_box" cellpadding=0 cellspacing=0>'.
			'<tr>'.
				'<td class="default_top_left"></td>'.
				'<td class="default_top">'.$title.'</td>'.
				'<td class="default_top_right"></td>'.
			'</tr>'.
			'<tr>'.
				'<td class="default_left"></td>'.
				'<td class="default_box_main">'.
						$content.
				'</td>'.
				'<td class="default_right">'.$right_hack.'</td>'.
			'</tr>'.
			'<tr>'.
				'<td class="default_bottom_left"></td>'.
				'<td class="default_bottom"></td>'.
				'<td class="default_bottom_right"></td>'.
			'</tr>'.
		'</table>';

		return $str;
	}

	function aSortBySecondIndex($multiArray, $secondIndex)
	{
		while (list($firstIndex, ) = each($multiArray))
			$indexMap[$firstIndex] = $multiArray[$firstIndex][$secondIndex];

		asort($indexMap);
		while (list($firstIndex, ) = each($indexMap))
			if (is_numeric($firstIndex))
				$sortedArray[] = $multiArray[$firstIndex];
			else $sortedArray[$firstIndex] = $multiArray[$firstIndex];
		return $sortedArray;
	}

	function aRSortBySecondIndex($multiArray, $secondIndex)
	{
		while (list($firstIndex, ) = each($multiArray))
			$indexMap[$firstIndex] = $multiArray[$firstIndex][$secondIndex];

		if (empty($indexMap)) return false;

		arsort($indexMap);
		while (list($firstIndex, ) = each($indexMap))
			if (is_numeric($firstIndex))
				$sortedArray[] = $multiArray[$firstIndex];
			else $sortedArray[$firstIndex] = $multiArray[$firstIndex];
		return $sortedArray;
	}





	//creates a tooltip of long strings, else return orginal, also passes data thru url_safedecode()
	//todo: cut string in middle of string instead
	//todo: dont return tooltip of short strings
	function create_tooltip($str, $pos)
	{
		if (!trim($str)) return '';
		
		$cut = $str;
		$org = url_safedecode($str);

		if (strlen($cut) > $pos+10) $cut = substr($cut, 0, $pos).'...'.substr($cut, -13);

		$cut = url_safedecode($cut);

		if ($cut) {
			//return '<span onmouseover="domTT_activate(this, event, \'content\', \''.$org.'\', \'trail\', true);">'.$cut.'</span>';
			return $cut;
		}
		
		return '&nbsp;';
	}

?>