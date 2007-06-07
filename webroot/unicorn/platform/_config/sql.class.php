<?
class sql {
	var $result, $connected, $t, $db;
	function sql() {
		$this->connected = false;
		$this->t = T;
	}
	function connect() {
		try {
			$link = @mysql_connect(SQL_H, SQL_U, SQL_P);
			@mysql_select_db(SQL_D);
			$this->db = SQL_D;
		} catch(Exception $e) {
			return false;
		}
		if($link) {
			$this->connected = true;
			return true;
		} else return false;
	}
	function checkconnected() {
		if(!$this->connected) {
			if(!$this->connect()) { splashACT('Could not connect do database.'); }
		}
	}
	function query($query, $debug = false, $assoc = false, $error = false) {
		$this->checkconnected();
		if($debug) print $query;
		$result = mysql_query($query);
		$return = array();
		#if($error)
		echo mysql_error();
		if($assoc) {
			while($row = mysql_fetch_assoc($result))
				$return[] = $row;
		} else {
			while($row = mysql_fetch_row($result))
				$return[] = $row;
		}
		return $return;
	}

	function querybycontent($query, $debug = false, $assoc = false, $name = 'content_type') {
		$this->checkconnected();
		if($debug) print $query;
		$result = mysql_query($query);
		$return = array();
		if($assoc) {
			while($row = mysql_fetch_assoc($result))
				$return[$row[$name]] = $row;
		} else {
			while($row = mysql_fetch_row($result))
				$return[$row[0]] = $row;
		}
		return $return;
	}
	function db($db) {
		$this->checkconnected();
		$this->db = $db;
		if(!@mysql_select_db($db)) die('Could not change db');
	}
	function queryLine($query, $assoc = false) {
		$this->checkconnected();
		$result = mysql_query($query);
		if($assoc)
			return mysql_fetch_assoc($result);
		else
			return mysql_fetch_row($result);
	}

	function queryResult($query, $debug = false) {
		$this->checkconnected();
		if($debug) print $query;
		$result = @mysql_query($query);
		return @mysql_result($result, 0);
	}
	function queryInsert($query) {
		$this->checkconnected();
		@mysql_query($query);
		return(mysql_insert_id());
	}
	function queryNumrows($query) {
		$this->checkconnected();
		@mysql_query($query);
		return(mysql_num_rows());
	}
	function queryUpdate($query, $debug = false) {
		$this->checkconnected();
		if($debug) print $query;
		@mysql_query($query);
		return(mysql_affected_rows());
	}
	function gc($type = 1) {
		$this->checkconnected();
		if(!empty($_REQUEST["PHPSESSID"]))
			$sess5454 = md5($_REQUEST["PHPSESSID"].'SALTHELGVETE');
		else
			$sess5454 = md5(microtime() . rand(1, 99999));
		if(!empty($_COOKIE['SOEBR']))
			$cookie_id = (is_md5($_COOKIE['SOEBR']))?$_COOKIE['SOEBR']:cookieSET("SOEBR", $sess5454);
		else
			$cookie_id = cookieSET("SOEBR", $sess5454);
		return ($type?$cookie_id:$sess5454);
	}
	function logAdd($category = '', $unique = '', $type = 'START') {
		$this->checkconnected();
		global $tab;
		$ret = false;
		if($type != 'INDEX') {
			$cookie_id = $this->gc();
			$this->queryInsert("INSERT INTO {$this->t}log SET
			sess_id = '".secureINS($cookie_id)."',
			sess_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."',
			category_id = '".((!empty($category))?secureINS($category):'')."',
			unique_id = '".((!empty($unique))?$unique:'')."',
			type_inf = '".((empty($type))?'START':$type)."',
			date_cnt = NOW()");
			$this->queryInsert("INSERT INTO {$this->t}logvisit SET
			sess_id = '".secureINS($cookie_id)."',
			sess_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."',
			user_string = '".$this->get_os_($_SERVER['HTTP_USER_AGENT']).' - '.$this->get_browser_($_SERVER['HTTP_USER_AGENT'])."',
			date_snl = NOW(),
			date_cnt = NOW()");
		}
		if(!empty($_SERVER['HTTP_REFERER'])) {
			$c = $this->queryResult("SELECT type_cnt FROM {$this->t}logreferer WHERE type_referer = '".secureINS($_SERVER['HTTP_REFERER'])."' LIMIT 1");
			if($c) {
				$this->queryUpdate("UPDATE {$this->t}logreferer SET type_cnt = type_cnt + 1 WHERE type_referer = '".secureINS($_SERVER['HTTP_REFERER'])."'");
			} else {
				$this->queryInsert("INSERT INTO {$this->t}logreferer SET type_cnt = '1', type_referer = '".secureINS($_SERVER['HTTP_REFERER'])."'");
			}
		}
		return $ret;
	}
	function get_os_($user_agent) {
	$oses = array (
		'Windows 3.11' => 'Win16',
		'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
		'Windows 98' => '(Windows 98)|(Win98)|(Win 9x)',
		'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
		'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
		'Windows 2003' => '(Windows NT 5.2)',
		'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)|(Windows-NT)',
		'Windows ME' => 'Windows ME',
		'Open BSD' => 'OpenBSD',
		'Sun OS' => 'SunOS',
		'Linux' => '(Linux)|(X11)',
		'Macintosh' => '(Mac_PowerPC)|(Macintosh)|(Mac_PPC)',
		'QNX' => 'QNX',
		'BeOS' => 'BeOS',
		'Sony Ericsson' => 'SonyEricsson',
		'OS/2' => 'OS/2',
		'Search Bot' => '(nuhk)|(Googlebot)|(Google)|(Yammybot)|(Openbot)|(psbot)|(Slurp/cat)|(msnbot)|(ia_archiver)|(Cerberian Drtrs)',
		'LG' => 'LG'
	);

	foreach($oses as $os=>$pattern)
	{
		if (eregi($pattern, $user_agent))
			return $os;
	}
	return 'Unknown';
	}

	function get_browser_($user_agent) {
	$browsers = array(
		'Opera' => 'Opera',
		'Mozilla Firefox' => '(Firebird)|(Firefox)',
		'Galeon' => 'Galeon',
		'Mozilla' => 'Gecko',
		'MyIE' => 'MyIE',
		'Lynx' => 'Lynx',
		'Lotus-Notes' => 'Lotus-Notes',
		'Netscape' => '(Mozilla/4\.75)|(Netscape6)|(Mozilla/4\.08)|(Mozilla/4\.5)|(Mozilla/4\.6)|(Mozilla/4\.79)',
		'Konqueror' => 'Konqueror',
		'Pic Bot' => 'psbot',
		'Google' => 'Google',
		'T610' => 'T610',
		'T630' => 'T630',
		'K500i' => 'K500i',
		'K700i' => 'K700i',
		'K750i' => 'K750i',
		'P800' => 'P800',
		'W800i' => 'W800i',
		'Z1010' => 'Z1010',
		'Z800' => 'Z800',
		'U8138' => 'U8138',
		'Search Bot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)|(Cerberian Drtrs)',
		'Internet Explorer 7' => '(MSIE 7\.[0-9]+)|(MSIE 7)',
		'Internet Explorer 6' => '(MSIE 6\.[0-9]+)',
		'Internet Explorer 5' => '(MSIE 5\.[0-9]+)',
		'Internet Explorer 4' => '(MSIE 4\.[0-9]+)',
		'Internet Explorer' => 'MSIE'
	);

	foreach($browsers as $browser=>$pattern)
	{
		if (eregi($pattern, $user_agent))
			return $browser;
	}
	return 'Unknown';
	}
}
?>