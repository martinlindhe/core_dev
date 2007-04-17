<?
/*

	//I söndags
	$weekday['last'][0]='s&ouml;ndags';	$weekday['last'][1]='m&aring;ndags';	$weekday['last'][2]='tisdags';
	$weekday['last'][3]='onsdags';			$weekday['last'][4]='torsdags';				$weekday['last'][5]='fredags';
	$weekday['last'][6]='l&ouml;rdags';
	
	//På söndag
	$weekday['next'][0]='s&ouml;ndag';	$weekday['next'][1]='m&aring;ndag';		$weekday['next'][2]='tisdag';
	$weekday['next'][3]='onsdag';				$weekday['next'][4]='torsdag';				$weekday['next'][5]='fredag';
	$weekday['next'][6]='l&ouml;rdag';

	*/


	/* Returnerar en sträng formaterad relativt utifrån aktuell tid */
	//ex: I fredags 13:40
	function getRelativeTimeLong($timestamp)
	{
		global $config;
		//fixme: function broken
		//global $weekday;

		if (!is_numeric($timestamp) || !$timestamp) return 'Never';

		$datestamp	= mktime (0,0,0,date('m',$timestamp), date('d',$timestamp), date('Y',$timestamp));

		$yesterday	= mktime (0,0,0,date('m') ,date('d')-1,  date('Y'));
		$tomorrow		= mktime (0,0,0,date('m') ,date('d')+1,  date('Y'));

		$lastweek		= mktime (0,0,0,date('m'), date('d')-7, date('Y'));
		$nextweek		= mktime (0,0,0,date('m'), date('d')+7, date('Y'));

		$timediff = time() - $timestamp;

		if (date('Y-m-d', $timestamp) == date('Y-m-d')) {
			
			if ($timediff < (60*60)) {
				$result = round($timediff / 60, 0).' minutes ago';
			} else {			
				//Today 18:13
				$result = $config['time']['today'].date(' H:i',$timestamp);
			}

		} else if ($datestamp == $yesterday) {
			//Yesterday 18:13
			$result = $config['time']['yesterday'].date(' H:i',$timestamp);
			
		} else if ($datestamp == $tomorrow) {
			//Tomorrow 18:13
			$result = $config['time']['tomorrow'].date(' H:i',$timestamp);

		} else if (date('Y',$timestamp) == date('Y')) {
			//The date is this year: Oct 10:th 18:13
			$mn = $config['time']['month']['short'][date('n',$timestamp)];
			$dy = $config['time']['day']['pron'][date('j',$timestamp)];
			$result = $dy.' '.$mn.', '.date(' H:i',$timestamp);
		} else {
			//The date is another year: Oct 10:th 18:13 2001
			$mn = $config['time']['month']['short'][date('n',$timestamp)];
			$dy = $config['time']['day']['pron'][date('j',$timestamp)];
			$yr = date('Y', $timestamp);
			$result = $mn.' '.$dy.' '.$yr.', '.date(' H:i',$timestamp);
		}

		return $result;
	}
	
	/* Returns the date in a format like 'Tor 13:e Sep 14:30' */
	function getDateStringShort($timestamp)
	{
		global $config;

		$wd = $config['time']['weekday']['short'][date('w',$timestamp)];
		$mn = $config['time']['month']['short'][date('n',$timestamp)];
		$dy = $config['time']['day']['pron'][date('j',$timestamp)];

		return $wd.' '.$dy.' '.$mn. ' '.date('H:i',$timestamp);
	}

	/* Returns the date in a format like 'Tor 13:e Sep' */
	function getShortDate($timestamp)
	{
		global $config;

		$wd = $config['time']['weekday']['short'][date('w',$timestamp)];
		$mn = $config['time']['month']['short'][date('n',$timestamp)];
		$dy = $config['time']['day']['pron'][date('j',$timestamp)];

		return $wd.' '.$dy.' '.$mn;
	}

	/* Returns the date in a format like 'Torsdagen den 13:e September 2006' */
	function getLongDate($timestamp)
	{
		global $config;

		$wd = $config['time']['weekday']['long'][date('w',$timestamp)];
		$mn = $config['time']['month']['long'][date('n',$timestamp)];
		$dy = $config['time']['day']['pron'][date('j',$timestamp)];
		$yr = date('Y', $timestamp);

		return $wd.'en den '.$dy.' '.$mn.' '.$yr;
	}

	/* Returns the date in a format like 'Dec 2006' */
	function getShortMonth($timestamp)
	{
		global $config;

		$mn = $config['time']['month']['short'][date('n',$timestamp)];
		$yr = date('Y', $timestamp);

		return $mn.' '.$yr;
	}

	/* Returns the date in a format like 'December 2006' */
	function getLongMonth($timestamp)
	{
		global $config;

		$mn = $config['time']['month']['long'][date('n',$timestamp)];
		$yr = date('Y', $timestamp);

		return $mn.' '.$yr;
	}


	/* Returns the date in a format like 'Tor 13:e Sep 2006 23:59' */
	function getFullDate($timestamp)
	{
		global $config;

		$wd = $config['time']['weekday']['short'][date('w',$timestamp)];
		$mn = $config['time']['month']['short'][date('n',$timestamp)];
		$dy = $config['time']['day']['pron'][date('j',$timestamp)];
		$yr = date('Y', $timestamp);
		$hh = date('H', $timestamp);
		$mm = date('i', $timestamp);

		return $wd.' '.$dy.' '.$mn.' '.$yr.' '.$hh.':'.$mm;
	}
	
	/* Returns the time specified in XhYmZs format (eg 2h4m10s) */
	function getTimeCount($hours, $minutes, $seconds)
	{
		$result = '';
		if ($hours  >0) $result .= $hours.'h';
		if ($minutes>0) $result .= $minutes.'m';
		if ($seconds>0) $result .= $seconds.'s';

		if ($result == '') $result = '0s';

		return $result;
	}

	function formatDate($timestamp)
	{
		//returns: 2006.04.01
		
		return date('Y.m.d', $timestamp);
	}
	
	function formatShortDate($timestamp = 0)
	{
		global $config;
		
		//returns: 26. April (norsk tidvisning)
		if (!$timestamp) $timestamp = time();
		
		$mon = date('n', $timestamp);

		return date('j', $timestamp).'. '.$config['time']['month']['long'][$mon];
	}
	
	function formatShortMonth($timestamp = 0)
	{
		global $config;
		
		//returns: April 2006
		if (!$timestamp) $timestamp = time();
		
		$mon = date('n', $timestamp);

		return $config['time']['month']['long'][$mon].' '.date('Y', $timestamp);
	}


?>