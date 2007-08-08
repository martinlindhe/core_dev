<?
	//todo: rename config variables
	$config['url_rewrite_length'] = 50;
	$config['url_rewrite_redirfile'] = '';

	function makeThumbLink($_id, $_title = '')
	{
		global $files, $config;
		if (!is_numeric($_id)) return false;

		return '<img src="'.$config['core_web_root'].'api/file.php?id='.$_id.'&amp;w='.$files->thumb_default_width.'&amp;h='.$files->thumb_default_height.getProjectPath().'" alt="Thumbnail" title="'.strip_tags($_title).'"/>';
	}

	function makeImageLink($_id, $_title = '')
	{
		global $config;
		if (!is_numeric($_id)) return false;

		return '<img src="'.$config['core_web_root'].'api/file.php?id='.$_id.getProjectPath().'" alt="Image" title="'.strip_tags($_title).'"/>';
	}

	/* Returns a string like "2 KiB" */
	function formatDataSize($bytes)
	{
		//$units = array('bytes', 'KiB', 'MiB', 'GiB', 'TiB');
		$units = array('bytes', 'k', 'mb', 'gb', 'tb');
		foreach ($units as $unit) {
			if ($bytes < 1024) break;
			$bytes = round($bytes/1024, 1);
		}
		return $bytes.' '.$unit;
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

	function formatUserInputText($text, $convert_html = true)
	{
		global $config;

		$text = trim($text);

		//convert html tags to &lt; and &gt; etc:
		if ($convert_html) $text = htmlspecialchars($text);

		//convert dos line-endings to Unix format for easy handling
		$text = str_replace("\r\n", "\n", $text);

		/* [b]bold text[/b] */
		$text = str_ireplace('[b]', '<b>', $text);
		$text = str_ireplace('[/b]', '</b>', $text);

		/* [i]italic text[/i] */
		$text = str_ireplace('[i]', '<i>', $text);
		$text = str_ireplace('[/i]', '</i>', $text);

		/* [u]underlined text[/u] */
		$text = str_ireplace('[u]', '<u>', $text);
		$text = str_ireplace('[/u]', '</u>', $text);

		/* [s]strikethru text[/u] */
		$text = str_ireplace('[s]', '<del>', $text);
		$text = str_ireplace('[/s]', '</del>', $text);

		/* [h1]headline level 1[/h1] */
		$text = str_ireplace('[h1]', '<h1 class="bb_h1">', $text);
		$text = str_ireplace('[/h1]', '</h1>', $text);

		/* [h2]headline level 2[/h2] */
		$text = str_ireplace('[h2]', '<h2 class="bb_h2">', $text);
		$text = str_ireplace('[/h2]', '</h2>', $text);

		/* [h3]headline level 3[/h3] */
		$text = str_ireplace('[h3]', '<h3 class="bb_h3">', $text);
		$text = str_ireplace('[/h3]', '</h3>', $text);

		$text = str_ireplace("[hr]\n", '<hr/>', $text);	//fixme: this is a hack. a better solution would be to trim all whitespace directly following a [hr] tag
		$text = str_ireplace('[hr]', '<hr/>', $text);

		//code block, example: [code]text text[/code]
		$text = str_ireplace("[/code]\n", "[/code]", $text);
		do {
			$pos1 = stripos($text, '[code]');
			if ($pos1 === false) break;

			$pos2 = stripos($text, '[/code]');
			if ($pos2 === false) break;
			$codeblock = trim(substr($text, $pos1+strlen('[code]'), $pos2-$pos1-strlen('[code]')));
			$codeblock = str_replace("\n", '(_br_)', $codeblock);

			$codeblock =
				'<div class="bb_code">'.
				'<div class="bb_code_head">code</div>'.
				'<div class="bb_code_body">'.$codeblock.'</div>'.
				'</div>';

			$text = substr($text, 0, $pos1) . $codeblock . substr($text, $pos2+strlen('[/code]'));
		} while (1);

		//quote block, example: [quote name=elvis]text text text[/quote]
		//or: [quote]text text text[/quote]
		do {
			$pos1 = stripos($text, '[quote');
			if ($pos1 === false) break;

			$pos2 = stripos($text, '[/quote]');
			if ($pos2 === false) break;

			$quoteblock = substr($text, $pos1+strlen('[quote'), $pos2-$pos1-strlen('[quote'));

			$qpos1 = stripos($quoteblock, 'name=');
			$qpos2 = strpos($quoteblock, ']');
			if ($qpos1 !== false) {
				$nameblock = substr($quoteblock, $qpos1+strlen('name='), $qpos2-$qpos1-strlen('name='));
				$quoteblock = substr($quoteblock, $qpos1+strlen('name=')+strlen($nameblock)+strlen(']'));
				if ($nameblock) $nameblock .= ' wrote';
				else $nameblock = 'Quote';
			} else {
				$nameblock = 'Quote';
				$quoteblock = substr($quoteblock, $qpos2+strlen(']'));
			}

			$quoteblock =
				'<div class="bb_quote">'.
				'<div class="bb_quote_head">'.$nameblock.':</div>'.
				'<div class="bb_quote_body">'.trim($quoteblock).'</div>'.
				'</div>';

			$text = substr($text, 0, $pos1) .$quoteblock. substr($text, $pos2+strlen('[/quote]'));
		} while (1);

		//wiki links, example [[wiki:About]] links to wiki.php?Wiki:About
		//example 2: [[wiki:About|read about us]] links to wiki.php?Wiki:About but "read about us" is link text
		//example 3: [[link:page.php|click here]] makes a clickable link

		do {
			$pos1 = strpos($text, '[[');
			if ($pos1 === false) break;

			$pos2 = strpos($text, ']]');
			if ($pos2 === false) break;

			$wiki_command = substr($text, $pos1+strlen('[['), $pos2-$pos1-strlen(']]'));

			$link = array();
			if (strpos($wiki_command, '|') !== false) {
				list($link['coded'], $link['title']) = explode('|', $wiki_command);
			} else {
				$link['coded'] = $wiki_command;
				$link['title'] = 'title';
			}

			$arr = explode(':', $link['coded']);
			$link['cmd'] = $arr[0];
			$link['param'] = '';
			for ($i=1; $i<count($arr); $i++) {
				$link['param'] .= $arr[$i];
			}

			if (empty($link['cmd'])) continue;

			$result = '';

			switch ($link['cmd']) {
				case 'wiki':
					if ($link['title']) {
						//[[wiki:About|read about us]] format
						$result = '<a href="wiki.php?Wiki:'.$link['param'].'">'.$link['title'].'</a>';
					} else {
						//[[wiki:About]] format
						$result = '<a href="wiki.php?Wiki:'.$link['param'].'">'.$link['param'].'</a>';
					}
					break;

				case 'link':
					$result = '<a href="'.$link['param'].'">'.$link['title'].'</a>';
					break;

				case 'file':
					$result = makeImageLink($link['param']);
					break;
					
				case 'poll':
					$result = poll(POLL_NEWS, $link['param']);
					break;

				default:
					die('unknown command: '. $link['cmd']);
					break;
			}

			if (!$result) $result = '['.$wiki_command.']';

			$text = substr($text, 0, $pos1) .$result. substr($text, $pos2+strlen(']]'));
		} while (1);


		//todo: add [img]url[/img] tagg för bildlänkning! och checka för intern länkning

		$text = replaceLinks($text);

		$text = nl2br($text);
		$text = str_replace('(_br_)', "\n", $text);

		return $text;
	}

	/* Converts URL's & email-addresses in the text to hyperlinks */
	function replaceLinks($text)
	{
 		//replaces url's with html links that is optionally passed thru redir.php if specified in $config['url_rewrite_redirfile']
/*
		$regexp = "(((http|ftp|https)://)|(www\.))+(([a-zA-Z0-9\._-]+\.[a-zA-Z]{2,6})|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(\:[0-9]+)*(/[a-zA-Z0-9\&%#_\./-~-]*)?";
		$replace = '<a href="'.$config['url_rewrite_redirfile'].'\\0" target="_blank">\\0</a>';
		$text = ereg_replace($regexp, $replace, $text);
*/

		//fixme: matchar inte urler's avslutande: /, t.ex: http://www.brainjar.com/css/positioning/
		//fixme: matchar inte url'er med ), t.ex: http://en.wikipedia.org/wiki/Ajax_(programming)
		//Den matchar url'er med portnummer, t.ex: http://www.kevinworthington.com:8181/
		$regexp = "/\b((http(s?):\/\/)|(www\.))([\w\.]+)([\#\,\/\~\?\&\=\;\%\(\-\w+\.\:]+)\b/i";
		$text = preg_replace_callback($regexp, 'replaceLinks_callback', $text);

		//replaces mail addresses with clickable links
		$regexp = "/([\w\.]+)(@)([\w\.]+)/i";
		$replacement = '<a href="mailto:\0" class="bb_url">\0</a>';
		$text = preg_replace($regexp, $replacement, $text);

		return $text;
	}

	function replaceLinks_callback($matches)
	{
		global $config;

		$url_text = $matches[0];

		//chops down long urls to http://addons.miranda-i...ile&id=2455
		if (strlen($url_text) > $config['url_rewrite_length']) {
			$url_text = substr($matches[0], 0, $config['url_rewrite_length']-$config['url_rewrite_trailing']).'...'.substr($matches[0], -$config['url_rewrite_trailing']+3);
		}

		$ret = '<a href="'.$config['url_rewrite_redirfile'].$matches[0].'" class="bb_url" target="_blank">'.$url_text.'</a>';

		return $ret;
	}


	/* Returns a sting like: 4h10m3s */
	function shortTimePeriod($seconds)
	{
		if (is_float($seconds)) $seconds = round($seconds);
		$retval='';

		//years
		$a = date('Y', $seconds) - 1970;
		if ($a==1) $retval=$a.' year, ';
		else if ($a>0) $retval=$a.' years, ';
		$seconds -= (((($a*60)*60)*24)*30)*365;

		//months
		$a=date('n',$seconds)-1;
		if($a==1) $retval.=$a.' month, ';
		else if($a>0) $retval.=$a.' months, ';
		$seconds -= ((($a*60)*60)*24)*30;

		//days
		$a=date('j',$seconds)-1;
		if($a==1) $retval.=$a.' day, ';
		else if ($a>0) $retval.=$a.' days, ';
		$seconds -= (($a*60)*60)*24;

		//hours
		$a=date('H',$seconds)-1;
		if ($a>0) $retval.=$a.'h';
		$seconds -= ($a*60)*60;

		//minutes
		$a=date('i',$seconds)-0; //translate from 09 to 9 quickly ;)
		if ($a>0) $retval.=$a.'m';
		$seconds -= $a*60;

		//seconds
		$a=date('s',$seconds)-0; //translate from 09 to 9 quickly ;)
		if ($a>0) $retval.=$a.'s';

		if (substr($retval, -2) == ', ') $retval = substr($retval, 0, -2);
		if ($retval == '') $retval = '0s';

		return $retval;
	}


	/* Returns array with parsed up news article texts
		[head]news article heading[/head]
		
		[body]news article body[/body]
	*/
	function parseArticle($text)
	{
		$pos1 = strpos($text, '[head]');
		$pos2 = strpos($text, '[/head]');
		if ($pos1 === false || $pos2 === false) {
			//handle as raw text, no markups found

			$text = htmlentities($text);
			if (strlen($text) > 30) {
				$art['head'] = substr($text, 0, 30).' [...]';
			} else {
				$art['head'] = $text;
			}
			$art['body'] = '';

			return $art;
		}

		$art['head'] = substr($text, $pos1+strlen('[head]'), $pos2-$pos1-strlen('[/head]')+1);
		$art['head'] = nl2br(trim(strip_tags($art['head'])));

		$pos1 = strpos($text, '[body]');
		$pos2 = strpos($text, '[/body]');
		if ($pos1 === false || $pos2 === false) return $art;

		$art['body'] = substr($text, $pos1+strlen('[body]'), $pos2-$pos1-strlen('[/body]')+1);
		$art['body'] = formatUserInputText($art['body']);

		return $art;
	}

	//Denna regexp har jag testat ordentligt den verkar ultimat
	//http://www.regexlib.com/REDetails.aspx?regexp_id=295:
/*
	echo ValidEmail('hej@kex.com').'<br/>'; //ok
	echo ValidEmail('apa sxev ke@com.com').'<br/>'; //deny
	echo ValidEmail('kuvös@kom.uk').'<br/>'; //deny
	echo ValidEmail('kuvös').'<br/>';	 //deny
	echo ValidEmail('martin.partin@hotmail.se').'<br/>'; //ok
	echo ValidEmail('gupp_golo@cnn.com').'<br/>'; //ok
	echo ValidEmail('<gupp@kep.com').'<br/>'; //deny
	echo ValidEmail('rl@123.22.11.92').'<br/>'; //deny
*/
	define('REGEXP_VALID_EMAIL', '/^(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}$/');
	function ValidEmail($email)
	{
		if (preg_match(REGEXP_VALID_EMAIL, $email)) return true;
		return false;
	}

	/* returns true if $_mobil is a valid swedish cellphone number */
	function ValidMobilNr($_mobil)
	{
		$_mobil = str_replace('-', '', $_mobil);
		$_mobil = str_replace(' ', '', $_mobil);

		$prefix = substr($_mobil, 0, 3);
		$number = substr($_mobil, 3);

		$fake_numbers = array('1234567', '0000000', '1111111', '2222222');
		if (in_array($number, $fake_numbers)) return false;

		switch ($prefix) {
			case '070':
			case '073':
			case '075':
			case '076':
				return true;
		}

		return false;
	}
?>