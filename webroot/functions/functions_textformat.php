<?
	$config['url_rewrite_length'] = 50;
	$config['url_rewrite_redirfile'] = '';
	


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
				$nameblock = $nameblock.' skrev';
			} else {
				$nameblock = 'Citat';
				$quoteblock = substr($quoteblock, $qpos2+strlen(']'));
			}

			$quoteblock =
				'<div class="bb_quote">'.
				'<div class="bb_quote_head">'.$nameblock.':</div>'.
				'<div class="bb_quote_body">'.trim($quoteblock).'</div>'.
				'</div>';

			$text = substr($text, 0, $pos1) .$quoteblock. substr($text, $pos2+strlen('[/quote]'));
		} while (1);
		
		//wiki links, example [[About]] links to wiki.php?View:About
		//example 2: [[About|read about us]] links to wiki.php?View:About but "read about us" is link text


		do {
			$pos1 = stripos($text, '[[');
			if ($pos1 === false) break;
			
			$pos2 = stripos($text, ']]');
			if ($pos2 === false) break;

			$wiki_link = substr($text, $pos1+strlen('[['), $pos2-$pos1-strlen(']]'));
			
			$qpos1 = strpos($wiki_link, '|');

			if ($qpos1 !== false) {
				//[[About|read about us]] format
				$link_text = substr($wiki_link, $qpos1+strlen('|'));
				$wiki_link = substr($wiki_link, 0, $qpos1);

				$wiki = '<a href="wiki.php?View:'.$wiki_link.'">'.$link_text.'</a>';
			} else {
				//[[About]] format
				$wiki = '<a href="wiki.php?View:'.$wiki_link.'">'.$wiki_link.'</a>';
			}

			$text = substr($text, 0, $pos1) .$wiki. substr($text, $pos2+strlen(']]'));
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
?>