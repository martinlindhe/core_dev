<?
	//kanske todo: ha koden som klistrar på en overlay bild som en separat funktion


	function formatPct($pct)
	{
		$result = number_format($pct, 2, '.', '');	//"4.2%" => "4.20%"
		if ($pct < 10.0) $result = ' '.$result;	// "4.23%" => " 4.23%"
		
		return $result;
	}


	//$filename should not have a extension, .png and .xml files will be created
	function writeWebBrowserTrendsImage(&$db, $filename, $time_from, $time_to)
	{
		if ($time_to > time()) return false;
		
		$list = getTrackerBrowserinfoTimeperiod($db, $time_from, $time_to);
		
		if (!$list) return false;
		
		$show_browsers = Array('Internet Explorer', 'Firefox', 'Opera', 'Other');
		
		$browser_stats = parseBrowserStats($list);
	
		$img_width = 260;
		$img_height = 314;
	
		$im = imagecreatetruecolor($img_width, $img_height);
		
		$bg_color = imagecolorallocate($im, 255, 255, 255);
		imagefill($im, 0, 0, $bg_color);
		$txt_color = imagecolorallocate($im, 0, 0, 0);
	
		imagestring($im, 2, 4, 3, "Web browser trends for ".formatShortMonth($time_from), $txt_color);
		
		$pos_start = 0;
		$pos_end = 0;
	
		$pos_text_y = 250;
		$left_cnt = 0;
		
		foreach ($browser_stats['stats'] as $browser => $versions)
		{
			if (!in_array($browser, $show_browsers))
			{
				$left_cnt += $versions['tot_cnt'];				
			} else {
				$show[$browser] = $versions;
			}
		}
		$show['Other']['tot_cnt'] = $left_cnt;
		$show['Other']['tot_pct'] = round($left_cnt / $browser_stats['stats']['tot_cnt'] * 100, 2);
		
		$show = aRSortBySecondIndex($show, 'tot_pct');

		foreach($show as $browser => $versions)
		{
				if (!$versions['tot_cnt']) continue;

				$offset = round($versions['tot_cnt'] / $browser_stats['stats']['tot_cnt'] * 360, 2);
	
				$pos_start = $pos_end;
				$pos_end += $offset;
				
				//todo: kan dettta fixas med en array lookup?
				switch ($browser) {
					case 'Internet Explorer': $fill_color = imagecolorallocate($im, 46, 150, 232); break;
					case 'Firefox':						$fill_color = imagecolorallocate($im, 223, 107, 22); break;
					case 'Opera':							$fill_color = imagecolorallocate($im, 242, 83, 68); break;
					case 'Other':							$fill_color = imagecolorallocate($im, 0, 0, 0); break;
				}
				
				imagefilledarc($im, ($img_width/2), ($img_height/2)-24, 225, 225, $pos_start, $pos_end, $fill_color, IMG_ARC_PIE);

				imagefilledrectangle($im, 4, $pos_text_y+3, 11, $pos_text_y+10, $fill_color);

				if ($browser == 'Other') {
					$text = formatPct($versions['tot_pct']).'% - Other browsers';
				} else {
					$text = formatPct($versions['tot_pct']).'% - '.$browser.' (v'.$browser_stats['stats'][$browser]['top_ver'].' popular)';
				}
				imagestring($im, 2, 15, $pos_text_y, $text, $fill_color);

				$pos_text_y += 10;
		}

		imagestring($im, 2, 4, 300, 'Based on data from '.formatNumberSize($browser_stats['stats']['tot_cnt']).' users', $txt_color);
		
		
		$overlay_file = 'webtrends/ai_thumb.png';
		$overlay = imagecreatefrompng($overlay_file);

		list($ovr_width, $ovr_height) = getimagesize($overlay_file);

		imagecopy($im, $overlay, $img_width - $ovr_width, $img_height - $ovr_height, 0, 0, $ovr_width, $ovr_height);

		imagepng($im, $filename.'.png');
		imagedestroy($im);
	}
	
	
	function writeSearchEngineTrendsImage(&$db, $filename, $time_from, $time_to)
	{
		if ($time_to > time()) return false;

		$list = getTrackerEntriesAllReferrers($db, $time_from, $time_to);

		$search = parseReferrers($list);
		//echo '<pre>'; print_r($search);


		$img_width = 260;
		$img_height = 314;
	
		$im = imagecreatetruecolor($img_width, $img_height);
		
		$bg_color = imagecolorallocate($im, 255, 255, 255);
		imagefill($im, 0, 0, $bg_color);
		$txt_color = imagecolorallocate($im, 0, 0, 0);
	
		imagestring($im, 2, 4, 3, "Search engine trends for ".formatShortMonth($time_from), $txt_color);
		
		$pos_start = 0;
		$pos_end = 0;

		$pos_text_x = 15;
		$pos_text_y = 250;

		foreach ($search['engines'] as $engine => $cnt)
		{
			if (!$cnt) continue;

			$pct = round($cnt / $search['engine_cnt'] * 100, 2);
			$offset = round($cnt / $search['engine_cnt'] * 360, 2);
	
			$pos_start = $pos_end;
			$pos_end += $offset;
			
			//todo: kan dettta fixas med en array lookup?
			switch ($engine) {
				case 'google': 			$name = 'Google';			$fill_color = imagecolorallocate($im, 46, 150, 232); break;
				case 'msn':					$name = 'MSN';				$fill_color = imagecolorallocate($im, 223, 107, 22); break;
				case 'yahoo':				$name = 'Yahoo';			$fill_color = imagecolorallocate($im, 12, 195, 3); break;
				case 'eniro':				$name = 'Eniro';			$fill_color = imagecolorallocate($im, 0, 0, 0); break;
				case 'aol':					$name = 'AOL';				$fill_color = imagecolorallocate($im, 123, 107, 22); break;
				case 'altavista':		$name = 'Altavista';	$fill_color = imagecolorallocate($im, 43, 47, 172); break;
				case 'spray':				$name = 'Spray';			$fill_color = imagecolorallocate($im, 199, 120, 172); break;
				case 'sesam':				$name = 'Sesam';			$fill_color = imagecolorallocate($im, 227, 11, 255); break;
			}			
			
			imagefilledarc($im, ($img_width/2), ($img_height/2)-24, 225, 225, $pos_start, $pos_end, $fill_color, IMG_ARC_PIE);

			imagefilledrectangle($im, $pos_text_x-11, $pos_text_y+3, $pos_text_x-4, $pos_text_y+10, $fill_color);

			$text = formatPct($pct).'% - '.$name;

			imagestring($im, 2, $pos_text_x, $pos_text_y, $text, $fill_color);

			$pos_text_y += 10;
			if ($pos_text_y >= 290) {
				$pos_text_x = 150;
				$pos_text_y = 250;
			}

		}
		
		imagestring($im, 2, 4, 300, 'Based on data from '.formatNumberSize($search['engine_cnt']).' searches', $txt_color);


		$overlay_file = 'webtrends/ai_thumb.png';
		$overlay = imagecreatefrompng($overlay_file);

		list($ovr_width, $ovr_height) = getimagesize($overlay_file);

		imagecopy($im, $overlay, $img_width - $ovr_width, $img_height - $ovr_height, 0, 0, $ovr_width, $ovr_height);

		imagepng($im, $filename.'.png');
		imagedestroy($im);
	}
?>