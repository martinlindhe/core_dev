<?
	//based on http://www.tapouillo.com/firefox_extension/sourcecode.txt

	define('GOOGLE_MAGIC', 0xE6359A60);

	function zeroFill($a, $b)
	{
		$z = hexdec(80000000);
		if ($z & $a) {
			$a = ($a>>1);
			$a &= (~$z);
			$a |= 0x40000000;
			$a = ($a>>($b-1));
		}
		else $a = ($a>>$b);
	
		return $a;
	}

	function mix($a, $b, $c)
	{
		$a -= $b; $a -= $c; $a ^= (zeroFill($c,13));
		$b -= $c; $b -= $a; $b ^= ($a<<8);
		$c -= $a; $c -= $b; $c ^= (zeroFill($b,13));
		$a -= $b; $a -= $c; $a ^= (zeroFill($c,12));
		$b -= $c; $b -= $a; $b ^= ($a<<16);
		$c -= $a; $c -= $b; $c ^= (zeroFill($b,5));
		$a -= $b; $a -= $c; $a ^= (zeroFill($c,3));
		$b -= $c; $b -= $a; $b ^= ($a<<10);
		$c -= $a; $c -= $b; $c ^= (zeroFill($b,15));
	
		return array($a,$b,$c);
	} 
	
	function GoogleCH($url, $length=null, $init=GOOGLE_MAGIC)
	{
		if (is_null($length)) $length = sizeof($url);
	
		$a = $b = 0x9E3779B9;
		$c = $init;
		$k = 0;
		$len = $length;
		while ($len >= 12) {
			$a += ($url[$k+0] +($url[$k+1]<<8) +($url[$k+2]<<16) +($url[$k+3]<<24));
			$b += ($url[$k+4] +($url[$k+5]<<8) +($url[$k+6]<<16) +($url[$k+7]<<24));
			$c += ($url[$k+8] +($url[$k+9]<<8) +($url[$k+10]<<16)+($url[$k+11]<<24));
			$mix = mix($a,$b,$c);
			$a = $mix[0]; $b = $mix[1]; $c = $mix[2];
			$k += 12;
			$len -= 12;
		}
	
		$c += $length;
		switch($len) {
			case 11: $c+=($url[$k+10]<<24);
			case 10: $c+=($url[$k+9]<<16);
			case 9 : $c+=($url[$k+8]<<8);
			/* the first byte of c is reserved for the length */
			case 8 : $b+=($url[$k+7]<<24);
			case 7 : $b+=($url[$k+6]<<16);
			case 6 : $b+=($url[$k+5]<<8);
			case 5 : $b+=($url[$k+4]);
			case 4 : $a+=($url[$k+3]<<24);
			case 3 : $a+=($url[$k+2]<<16);
			case 2 : $a+=($url[$k+1]<<8);
			case 1 : $a+=($url[$k+0]);
			/* case 0: nothing left to add */
		}
		$mix = mix($a,$b,$c);
	
		return $mix[2];
	}
	
	//converts a string into an array of integers containing the numeric value of the char
	function strord($string)
	{
		for($i=0;$i<strlen($string);$i++) {
			$result[$i] = ord($string{$i});
		}
		return $result;
	}
	
	// converts an array of 32 bit integers into an array with 8 bit values. Equivalent to (BYTE *)arr32
	function c32to8bit($arr32)
	{
		for($i=0;$i<count($arr32);$i++) {
			for ($bitOrder=$i*4;$bitOrder<=$i*4+3;$bitOrder++) {
				$arr8[$bitOrder]=$arr32[$i]&255;
				$arr32[$i]=zeroFill($arr32[$i], 8);
			}
		}
		return $arr8;
	}
	
	function GoogleCH2($url)
	{
		$url = 'info:'.$url;
	
		$ch = GoogleCH(strord($url));
		$ch = sprintf("%u", $ch);
	
		$ch = ((($ch/7) << 2) | (((int)fmod($ch,13))&7));
	
		$prbuf = array();
		$prbuf[0] = $ch;
		for($i = 1; $i < 20; $i++) {
			$prbuf[$i] = $prbuf[$i-1]-9;
		}
		$ch = GoogleCH(c32to8bit($prbuf), 80);
	
		$ch2 = sprintf("6%u", $ch);
		//echo 'Checksum >=2.0.114: '. $ch2.'<br>';
		
		return $ch2;
	}
	
	function GetGooglePR(&$db, $url)
	{
		global $config;

		//1. Do we have a recent PR lookup for this url in the PR cache?
		$slashed_url = dbAddSlashes($db, $url);
		$sql = 'SELECT PR,timeUpdated FROM tblGooglePR WHERE url="'.$slashed_url.'"';
		$data = dbOneResult($db, $sql);

		if ($data['timeUpdated'])
		{
			$timestamp = strtotime($data['timeUpdated']);

			if ($timestamp < (time()-$config['google']['pr_cache_expiration']) ) {
				//2. If the entry is old, remove it
				$sql = 'DELETE FROM tblGooglePR WHERE url="'.$slashed_url.'"';
				dbQuery($db, $sql);
			} else {
				return $data['PR'];
			}
		}

		//3. If not, fetch & store in PR cache
		$host = 'toolbarqueries.google.com';
		$port = 80;
	
		$header  = "GET /search?client=navclient-auto&ch=".GoogleCH2($url)."&ie=UTF-8&oe=UTF-8&features=Rank:FVN&q=info:".urlencode($url)." HTTP/1.1\r\n";
		$header .= "Host: ".$host."\r\n";
		$header .= "User-Agent: Mozilla/4.0 (compatible; GoogleToolbar 2.0.114-big; Windows XP 5.1)\r\n";
		$header .= "Connection: close\r\n\r\n";

		//echo $header;

		$fp = fsockopen($host, $port, $errno, $errstr, 30);
		if (!$fp) {
			echo "error: $errstr ($errno)<br />\n";
			return '';
		}
	
		$result = '';
	
		fwrite($fp, $header);
		while (!feof($fp)) {
			$result .= fgets($fp, 128);
		}
		fclose($fp);
		
		//Strip header
		$result = trim(substr($result, strpos($result, "\r\n\r\n")));

		$raw_result = $result;

		//Strip whitespace
		$result = str_replace("\r", "\n", $result);
		$result = str_replace("\n\n", "\n", $result);
		
		//Content now looks like: e\n\nRank_1:1:3\n\n0


		//Find "Rank_1:", if it dont exist return PR 0
		$pos = strpos($result, 'Rank_1:');
		if ($pos === false) return 0;

		$pr_score = substr($result, $pos+strlen('Rank_1:'));
		$pos = strpos($pr_score, "\n");
		$pr_score = substr($pr_score, 0, $pos);
		
		//Nu har vi kvar "X:Y", där Y är PR, och X är okänt, vanligtvis 0
		$pr = explode(':', $pr_score);

		//Save PR in the PR cache
		$sql = 'INSERT INTO tblGooglePR SET url="'.$slashed_url.'", PR='.$pr[1].', entry="'.$raw_result.'", timeUpdated=NOW()';
		dbQuery($db, $sql);		

		return $pr[1];
	}


	/* Accepts a array of absolute url:s to do google PR lookup on, returns array with PR scores */
	function perform_google_pr_lookups(&$db, $urls)
	{
		foreach ($urls as $url)
		{
			$pr[$url] = GetGooglePR($db, $url);
		}

		return $pr;
	}
	
?>