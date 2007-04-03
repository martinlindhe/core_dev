<?
function createGenerated($img, $txt1 = '', $txt2 = '') {
	$im = imagecreatefromjpeg($img);
	$white = imagecolorallocate($im, 255, 255, 255);
	$grey = imagecolorallocate($im, 128, 128, 128);
	$black = imagecolorallocate($im, 0, 0, 0);
	$font = '/var/www/_output/foliom.TTF';
	$img_top = imagecreatefrompng('./_output/bg.png');
	imagecopyresampled($im, $img_top, 0, 99, 0, 0, 200, 32, 200, 32);
	imagettftext($im, 10, 0, 5, 111, $white, $font, $txt2);
	imagettftext($im, 12, 0, 5, 126, $white, $font, $txt1);
	imagepng($im, './_output/generated/'.md5($img.$txt1.$txt2).'.png');
	imagedestroy($im);
}
function getGenerated($img, $txt1 = '', $txt2 = '') {
	if(!file_exists('./_output/generated/'.md5($img.$txt1.$txt2).'.png')) {
		createGenerated($img, $txt1, $txt2);
	}
	return '/_output/generated/'.md5($img.$txt1.$txt2).'.png';
}
function nicedate($date, $type = 1) {
	$str = strtotime($date);
	$thisday = strftime("%Y-%m-%d", $str);
	$return = '';
	if($type == 1) {
		if(date("Y-m-d") == $thisday) $return .= '';
		else if(date("Y-m-d", strtotime("-1 day")) == $thisday) $return .= 'igår';
		else if(date("Y-m-d", strtotime("+1 day")) == $thisday) $return .= 'imorgon';
		else $return .= stripzero(strftime("%d", $str)).strftime(" %b", $str);
		$y = date('Y', $str);
		if(date('Y') != $y) $return .= ' -'.date('y', $str);
		$return .= ' kl '.date('H:i', $str);
	} elseif($type == 3) {
		if(date("Y-m-d") == $thisday) $return .= 'idag';
		else if(date("Y-m-d", strtotime("-1 day")) == $thisday) $return .= 'igår';
		else if(date("Y-m-d", strtotime("+1 day")) == $thisday) $return .= 'imorgon';
		else $return .= stripzero(strftime("%d", $str)).strftime(" %B", $str);
		$y = date('Y', $str);
		if(date('Y') != $y) $return .= ' -'.date('y', $str);
	} elseif($type == 4) {
		$return .= stripzero(strftime("%d", $str)).strftime(" %B", $str);
		$return .= ' -'.date('y', $str);
		$return .= ' kl '.date('H:i', $str);
	} elseif($type == 2) {
		$time = true;
		if(date("Y-m-d") == $thisday) {
			$return .= '';
		} else if(date("Y-m-d", strtotime("-1 day")) == $thisday) {
			$return .= 'igår';
		} else if(date("Y-m-d", strtotime("+1 day")) == $thisday) {
			$return .= 'imorgon';
		} else {
			$time = false;
			$return .= stripzero(strftime("%d", $str)).strftime(" %b", $str);
			$y = date('Y', $str);
			if(date('Y') != $y) $return .= ' -'.date('y', $str);
		}
		if($time) {
			$return .= ($return?' ':'').'kl '.date('H:i', $str);
		}
	}
	return $return;
}
function array_unshift_ref(& $ioArray, $iValueWrappedInAnArray) { 
   $lNewArray = false; 
   foreach (array_keys ($ioArray) as $lKey) 
       $lNewArray[$lKey+1] = & $ioArray[$lKey]; 
   $ioArray = array (& $iValueWrappedInAnArray[0]); 
   if ($lNewArray) 
       foreach (array_keys ($lNewArray) as $lKey) 
             $ioArray[] = & $lNewArray[$lKey]; 
   return count($ioArray); 
} 
function paging_limit($limit) {
	if(isset($limit) && is_numeric($limit)) {
		$array = array('X', 0, 60, 100);
		cookieSET("limit", (!empty($_COOKIE['limit']) && in_array($_COOKIE['limit'], $array))?$limit:60);
	} elseif(isset($_COOKIE['limit']) && is_numeric($_COOKIE['limit'])) {
		$array = array('X', 0, 60, 100);
		$limit = (in_array($_COOKIE['limit'], $array))?$_COOKIE['limit']:60;
	} else
		$limit = 60;
	return $limit;
}
function specialDate($date, $dday = 0, $type = 1) {
	if($type) {
		if($dday) {
			$first_date = stripzero(date("d", strtotime($date)));
			$first_month = strftime("%B", strtotime($date));
			$sec_date = stripzero(date("d", strtotime($date.' +1 DAY')));
			if($sec_date < $first_date) {
	# månadsskifte
				$first_month = stripzero(date("m", strtotime($date)));
				$sec_month = strftime("%B", strtotime($date.' +1 DAY'));
				return "$first_date/$first_month & $sec_date $sec_month";
			} else {
				return "$first_date & $sec_date $first_month";
			}
		} else {
			if(date("Y", strtotime($date)) == date("Y")) {
				return stripzero(date("d", strtotime($date))).' '.strftime("%B", strtotime($date));
			} else {
				return stripzero(date("d", strtotime($date))).' '.strftime("%B", strtotime($date)).' '.date("Y", strtotime($date));
			}
		}
	} else {
		return strftime("%A", strtotime($date)).' '.stripzero(date("d", strtotime($date))).' '.strftime("%B", strtotime($date));
	}
}
function doInt($str) {
	return str_replace(",", "&nbsp;", number_format($str));
}
function makeMenu($sel, $arr) {
	$i = false;
	$ret = '';
	foreach($arr as $key => $val) {
		if(!$i) $i = true; else $ret .= ' - ';
		$ret .= '<a class="wht" href="'.$val[0].'">'.(strtolower($key) == strtolower($sel)?'»'.$val[1].'«':$val[1]).'</a>';
	}
	return $ret;
}
function flash($s) {
	return urlencode(utf8_encode($s));
}
function getset($id, $opt = 'r', $type = 's', $order = 'main_id DESC') {
	global $t, $sql;
	if($type == 's') {
		$result = $sql->queryResult("SELECT ".CH." text_cmt FROM {$t}textsettings WHERE main_id = '$id' AND type_id = '$opt' LIMIT 1");
		if(!$result) return false; else return $result;
	} elseif($type == 'm') {
		$result = $sql->query("SELECT ".CH." main_id, text_cmt FROM {$t}textsettings WHERE type_id = '$opt'");
		if(!$result) return false; else return $result;
	} elseif($type == 'mo') {
		$result = $sql->query("SELECT ".CH." main_id, text_cmt FROM {$t}textsettings WHERE type_id = '$opt' ORDER BY $order");
		if(!$result) return false; else return $result;
	}
}
function gettxt($id, $opt = '0', $ext = false) {
	global $t, $sql;
	$result = $sql->queryResult("SELECT ".CH." text_cmt FROM {$t}text WHERE main_id = '$id' AND option_id = '$opt' LIMIT 1");
	if(!$result) return false; else if(!$ext) return $result; else return extOUT($result);
}
function headline($id = '') {
	return CS.'_objects/_heads/head_'.$id.'.gif';
}
function date_diff($current,$past) { 
	$seconds = strtotime($current) - strtotime($past);    
	$min = $seconds/60; 
	$hours = $min/60; 
	$days = floor($hours/24); 
	$hours = floor($hours-($days*24)); 
	$min = floor($min-($days*60*24)-($hours*60)); 
	$seconds = floor($seconds-($days*60*60*24)-($hours*60*60)-($min*60)); 
	return array('days' => $days,'hours' => $hours,'minutes' => $min,'seconds' => $seconds); 
}
function now() {
	return strftime('%Y-%m-%d %T');
}
function addzero($str) {
	if(strlen($str) == '1') $str = '0'.$str;
	return $str;
}
function stripzero($str) {
	if(substr($str, 0, 1) == '0') $str = substr($str, 1, 1);
	return $str;
}
function execSt($end = 0, $notset = 0) {
	global $start;
	if(!$end) {
		list($usec, $sec) = explode(" ",microtime());
		if(!$notset) $start = ((float)$usec + (float)$sec);
		else return ((float)$usec + (float)$sec);
	} else {
		echo substr((execSt(0, 1) - $start),0,10);
	}
}
function secureINS($str) {
	return addslashes($str);
}
function secureOUT($str, $nl = false) {
	return ($nl?nl2br(stripslashes(htmlentities($str))):stripslashes(htmlentities($str)));
}
function safeOUT($str, $nl = true) {
	return ($nl?stripslashes(nl2br($str)):stripslashes($str));
}
function extOUT($str, $class = '') {
	return nl2br(doURL(doMailto(stripslashes($str), $class), $class));
}
function is_md5($str) {
	if(!empty($str) && preg_match('/^[A-Fa-f0-9]{32}$/', $str))
		return true;
	else
		return false;
}
function l($type = 'main', $action = 'start', $id = '', $key = '') {
	#return 'index.php?type='.$type.'&action='.$action.($id?'&id='.$id:'');
	return '/'.$type.'/'.$action.($id != ''?'/'.$id.($key != ''?'/'.$key:''):'').'/';
}
function reloadACT($url) {
	header('Location: '.$url);
	exit;
}
function splashIACT($msg, $topic, $tc = 1, $class = '') {
	errorACT($msg, '', 'popup', '', 5000, $topic, $tc, $class);
}
function errorIACT($msg, $topic, $tc = 1, $class = '') {
	errorACT($msg, '', 'main', '', 5000, $topic, $tc, $class);
}
function errorTACT($msg, $url, $time) {
	errorACT($msg, $url, 'main', '', $time);
}
function errorACT($msg, $url = '', $type = 'main', $parent = '', $time = 5000, $topic = '', $tc = 1, $class = '') {
	eval(GLOBAL_STRING);
	if($type == 'main') {
		$page = 'error.php';
		if(!$l) $page = 'error_splash.php';
		if(!empty($url) && substr($url, 0, 1) != '1') {
			require(DESIGN.$page);
			require(DESIGN.'mv.php');
		} elseif(substr($url, 0, 1) == '1') {
			$url = substr($url, 1);
			require(DESIGN.$page);
		} else {
			require(DESIGN.$page);
		}
	} elseif($type == 'popup') {
		require(DESIGN.'error_popup.php');
	} elseif($type == 'popupbig') {
		require(DESIGN.'error_popupbig.php');
	} elseif($type == 'splash') {
		require(DESIGN.'error_splash.php');
		if(!empty($url)) {
			require(DESIGN.'mv.php');
		}
	}
	exit;
}
function popupLACT($msg, $cnt = false) {
	errorACT($msg, 1, 'popup', '', 5000, $cnt);
}
function bigpopupACT($msg) {
	errorACT($msg, '', 'popupbig', '', 0, '', 1, 'wht');
}
function popupACT($msg, $url = '', $parent = '', $time = 5000) {
	errorACT($msg, $url, 'popup', $parent, $time);
}
function splashLACT($msg, $url = '', $cnt = '') {
	errorACT($msg, $url, 'splash', '', 5000, $cnt);	
}
function splashACT($msg, $url = '', $time = 5000) {
	errorACT($msg, $url, 'splash', '', $time);
}
function loginACT() {
	errorACT('Du är inte inloggad! Logga in uppe i menyn eller registrera dig.');
}
function doADP($pos, $right = false) {
	$sizes = array('140_inside_main' => array(140, 350),'140_inside_profile' => array(140, 350),'140_static_right' => array(140, 350),'326_inside_highlight' => array(326, 185),'326_inside_nopic' => array(326, 185),'326_inside_noupgrade' => array(326, 185),'468_static_top' => array(468, 60),'468_inside_popup' => array(468, 60));
	if($right) {
		if(strpos($pos, gettxt('ad_enable')) !== false)
	echo '
		<table cellspacing="0" width="160">
		<tr><td class="right_header" style="background-image: url(\'./_img/head_ad.gif\');">&nbsp;</td></tr>
		</table>
		<div style="padding: 10px 0 10px 10px; background: #E2E2E2;"><iframe src="/_amsPOS/import.php?p='.$pos.'" frameborder="0" border="0" width="'.$sizes[$pos][0].'" height="'.$sizes[$pos][1].'"></iframe></div>
		';
	} else
		echo '<iframe src="/_amsPOS/import.php?p='.$pos.'" frameborder="0" border="0" width="'.$sizes[$pos][0].'" height="'.$sizes[$pos][1].'"></iframe>';
}
function checkBan($splash = 0) {
	global $sql;
	$check = $sql->queryResult("SELECT COUNT(*) as count FROM ".T."ban WHERE ban_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."'");
	if(!empty($check)) {
		if($splash) errorSPLASHACT("Tyvärr! Du är blockerad.");
		else errorACT("Tyvärr! Du är blockerad.");
	}
}
function cookieSET($id, $val, $expire = '') {
	if(empty($expire)) $expire = time() + 15 * 365 * 24 * 60 * 60;
	setcookie($id, $val, $expire, '/');
	return $val;
}
function paging($p = '1', $limit = 20) {
	if(empty($p) || !is_numeric($p)) {
		$p = 1;
	}
	return array('p' => $p, 'limit' => $limit, 'slimit' => ($p - 1) * $limit);
}
function doMailto($str, $class = '') {
    return preg_replace("/([\w\.\-_]+)(@)([\w\.\-_]+)/i", "<a href=\"mailto:$0\"".($class?' class="'.$class.'"':'').">$0</a>",$str);
}
function doURL($str, $class = '') {
	$p_match = array(
'/\[link\](h?t?t?p?:?\/?\/?)(.*)\[\/link\]/i',
"/(?<=^|\s)((https?|ftps?):\/\/)?(([-_a-zA-Z]+\.)+)([a-zA-Z]{2,6})(\/[^\]\[\s]*)?(?=\s|$)/e",
"/\[link=((https?|ftps?):\/\/)?(([-_a-zA-Z]+\.)*)([a-zA-Z]{2,6})(\/[^\]\[\s]*)?\]([^\]\[]+)\[\/link\]?/e"
);
	$class = ($class?' '.$class:'');
	$p_replace = array(
'<a href="http://\\2" target="_blank" class=\"bld $class \">\\1\\2<</a>',
"'$1'!=''? '<a href=\"$1$3$5$6\" target=\"_blank\" class=\"bld $class \">$3$5$6</a>':'<a href=\"http://$3$5$6\" target=\"_blank\" class=\"bld $class \">$3$5$6</a>'",
"'$1'!=''?'<a href=\"$1$3$5$6\" target=\"_blank\" class=\"bld $class \">$7</a>':'<a href=\"http://$3$5$6\" target=\"_blank\" class=\"bld $class \">$7</a>'"
);
	$str = preg_replace($p_match, $p_replace, $str);
    return $str;
}
function dopaging($paging, $url, $anchor = '', $width = 'med', $text = '&nbsp;', $vice = 1) {
	#if($width == 'big' || $width == 'bigmed') $max = 10; else $max = 5;
	$max = 10;
	$paging['s'] = $paging['slimit'];
	$pages = ceil($paging['co'] / $paging['limit']);
	if($pages > $max) {
		$paging['slimit'] = $paging['p'] - floor($max / 2);
		if($paging['slimit'] > ($pages - $max + 1)) {
			$paging['slimit'] = $pages - $max + 1;
		}
	} else {
		$paging['slimit'] = 1;
	}
	if($paging['slimit'] < 1) $paging['slimit'] = 1;
	$stop = $paging['slimit'] + $max - 1;
	if($stop > $pages) $stop = $pages;
	if(strpos($text, '%') !== false)
		$text = sprintf($text, (($paging['co'])?$paging['s']+1:0), ($paging['co'] > ($paging['s']+$paging['limit']))?$paging['s']+$paging['limit']:$paging['co'], $paging['co']);

	if($width == 'med' || $width == 'medmin' || $width == 'big' || $width == 'medbig' || $width == 'biggest') echo '<table cellspacing="0" style="font-family: Verdana; font-size: 10px; width: '.($width == 'medmin'?'596':($width == 'big'?'596':($width == 'medbig'?'596':($width == 'biggest'?'786':'596')))).'px;"><tr>
	<td class="pdg" nowrap="nowrap">'.$text.'</td>
	<td class="pdg" align="right" nowrap="nowrap">'.($paging['p'] > 1?'<a href="'.$url.($paging['p']-1).$anchor.'" class="bld">« '.(($vice)?'bakåt':'framåt').'</a>':'&nbsp;').'&nbsp;&nbsp;';
	if($paging['co'] > $paging['limit']) {
		echo 'sida';
		if($paging['slimit'] > 1) {
			echo ' ...';
		}
		if($stop) { for($i = $paging['slimit']; $i <= $stop; $i++) {
			if($paging['p'] != $i) {
				echo ' <a href="'.$url.$i.$anchor.'">'.$i.'</a>';
			} else {
				echo ' <b>'.$i.'</b>';
			}
		} } else echo '1';
		if($pages > $stop) {
			echo ' ... av '.$pages;
		}
	} else echo '&nbsp;';

	if($width == 'big' || $width == 'medbig' || $width == 'med' || $width == 'medmin' || $width == 'biggest') echo '&nbsp;&nbsp;'.($paging['p'] < $stop?'<a href="'.$url.($paging['p']+1).$anchor.'" class="bld">'.((!$vice)?'bakåt':'framåt').' »</a>':'&nbsp;').'</td>
	</tr></table>';
}






















/*
function dopaging($paging, $url, $type = 1, $vice = 1, $text = '&nbsp;', $rtext = '&nbsp;', $anchor = '') {
		$got = 0;
		if($paging['co'] < $paging['limit'] && $text == '&nbsp;') {
			return;
		}
		if($type == 1) {
?>
				<table cellspacing="0" class="bg brd" width="100%" style="margin-bottom: 4px;">
				<tr>
					<td class="pdg" style="width: 20%;" nowrap><?=$text?></td>
					<td class="pdg" align="right" style="width: 22%;"><?=($paging['p'] > 1)?'<a href="'.$url.($paging['p']-1).$anchor.'" class="bld">« '.(($vice)?'bakåt':'framåt').'</a>':'&nbsp;';?></td>
					<td class="pdg" align="center" style="width: 16%;"><nobr><?=($paging['co'] > $paging['limit'])?'sida '.$paging['p']:'&nbsp;';?></nobr></td>
					<td class="pdg" style="width: 22%;"><?=($paging['limit'] != 'X' && ($paging['co'] > ($paging['slimit'] + $paging['limit'])))?'<a href="'.$url.($paging['p']+1).$anchor.'" class="bld">'.((!$vice)?'bakåt':'framåt').' »</a>':'&nbsp;';?></td>
					<td class="pdg" style="width: 20%;" align="right"><nobr><?=$rtext?></nobr></td>
				</tr>
				</table>
<?
		} else {
	$max = 10;
	$paging['s'] = $paging['slimit'];
	$pages = ceil($paging['co'] / $paging['limit']);
	if($pages > $max) {
		$paging['slimit'] = $paging['p'] - floor($max / 2);
		if($paging['slimit'] > ($pages - $max + 1)) {
			$paging['slimit'] = $pages - $max + 1;
		}
	} else {
		$paging['slimit'] = 1;
	}
	if($paging['slimit'] < 1) $paging['slimit'] = 1;

	$stop = $paging['slimit'] + $max - 1;

	if($stop > $pages) $stop = $pages;
	if(strpos($text, '%') !== false)
		$text = sprintf($text, (($paging['co'])?$paging['s']+1:0), ($paging['co'] > ($paging['s']+$paging['limit']))?$paging['s']+$paging['limit']:$paging['co'], $paging['co']);
?>
				<table cellspacing="0" class="bg brd" width="100%" style="margin-bottom: 4px;">
				<tr>
					<td class="pdg" style="width: 20%;"><nobr><?=$text?></nobr></td>
					<td class="pdg" align="right" style="width: 22%;"><?=($paging['p'] > 1)?'<a href="'.$url.($paging['p']-1).$anchor.'" class="bld">« '.(($vice)?'bakåt':'framåt').'</a>':'&nbsp;';?></td>
					<td class="pdg" align="center" style="width: 16%;"><nobr>
<?
if($paging['co'] > $paging['limit']) {
?>
sida
<?
	if($paging['slimit'] > 1) {
		echo ' ...';
	}
	if($stop) { for($i = $paging['slimit']; $i <= $stop; $i++) {
		if($paging['p'] != $i) {
			echo ' <a href="'.$url.$i.$anchor.'">'.$i.'</a>';
		} else {
			echo ' <b>'.$i.'</b>';
		}
	} } else echo '1';
	if($pages > $stop) {
		echo ' ... '.$pages;
	}
} else echo '&nbsp;';
?>
					</nobr></td>
					<td class="pdg" style="width: 22%;"><?=($paging['p'] < $stop)?'<a href="'.$url.($paging['p']+1).$anchor.'" class="bld">'.((!$vice)?'bakåt':'framåt').' »</a>':'&nbsp;';?></td>
					<td class="pdg" style="width: 20%;" align="right"><nobr><?=$rtext?></nobr></td>
				</tr>
				</table>
<?
		}
}

function paging_limit($limit) {
	if(isset($limit) && is_numeric($limit)) {
		$array = array('X', 0, 60, 100);
		cookieSET("limit", (!empty($_COOKIE['limit']) && in_array($_COOKIE['limit'], $array))?$limit:60);
	} elseif(isset($_COOKIE['limit']) && is_numeric($_COOKIE['limit'])) {
		$array = array('X', 0, 60, 100);
		$limit = (in_array($_COOKIE['limit'], $array))?$_COOKIE['limit']:60;
	} else
		$limit = 60;
	return $limit;
}
function serfix($str) {
	return str_replace('\\', '', stripslashes($str));
}
function postit($txt = '') {
	#$fix = array("\r\n", "\n", "\r");
	#$replace = ' ';
	#$txt = str_replace($fix, $replace, $txt);
	$fix = array("'", '<', '>', '/', '\\');
	$replace = '';
	$txt = str_replace($fix, $replace, $txt);
	$txt = (!empty($txt))?stripslashes($txt):'';
	$txt = trim($txt);
	if(empty($txt)) return '';
	else return substr($txt, 0, 40);
}

function doAD($pos, $start = false) {#&r=\'+rnd+\'
if($start)
#echo '<script type="text/javascript"> rnd = new String(Math.random()); rnd = rnd.substring(2, 6); pos = \''.$pos.'\'; document.write(\'<\' + \'script type="text/javascript" src="amsPOS/?p=\'+pos+\'"></\' + \'script>\'); </script>';
echo '<script type="text/javascript" src="amsPOS/?p='.$pos.'"></script>';
else
echo '
<table cellspacing="0" width="160">
<tr><td class="right_header" style="background-image: url(\'./_img/head_ad.gif\');">&nbsp;</td></tr>
</table>
<div style="padding: 10px 0 10px 10px; background: #E2E2E2;"><script type="text/javascript">ADMS_INPUT(\''.$pos.'\');</script></div>
';
}

function doADP($pos, $right = false) {
	$sizes = array('140_inside_main' => array(140, 350),'140_inside_profile' => array(140, 350),'140_static_right' => array(140, 350),'326_inside_highlight' => array(326, 185),'326_inside_nopic' => array(326, 185),'326_inside_noupgrade' => array(326, 185),'468_static_top' => array(468, 60),'468_inside_popup' => array(468, 60));
	if($right) {
		if(strpos($pos, gettxt('ad_enable')) !== false)
	echo '
		<table cellspacing="0" width="160">
		<tr><td class="right_header" style="background-image: url(\'./_img/head_ad.gif\');">&nbsp;</td></tr>
		</table>
		<div style="padding: 10px 0 10px 10px; background: #E2E2E2;"><iframe src="amsPOS/import.php?p='.$pos.'" frameborder="0" border="0" width="'.$sizes[$pos][0].'" height="'.$sizes[$pos][1].'"></iframe></div>
		';
	} else
		echo '<iframe src="amsPOS/import.php?p='.$pos.'" frameborder="0" border="0" width="'.$sizes[$pos][0].'" height="'.$sizes[$pos][1].'"></iframe>';
}

function topic($id, $type = 'left', $colspan = '', $text = '&nbsp;', $link = '', $mrg = false) {
echo '		<tr>
			<td '.$colspan.'class="'.$type.'_header'.(($link)?'  cur" onclick="document.location.href = \''.$link.'\';':'').'" style="'.(($mrg)?'height: 46px; border-bottom: 6px solid #FFF; ':'').'background-image: url(\'./_img/head_'.$id.'.gif\');">'.(($type == 'right')?'<div style="overflow: hidden; width: 150px; height: 14px;">'.$text.'</div>':$text).'</a></td>
		</tr>
';
}

function date_diff($current,$past) { 
	$seconds = strtotime($current) - strtotime($past);    
	$min = $seconds/60; 
	$hours = $min/60; 
	$days = floor($hours/24); 
	$hours = floor($hours-($days*24)); 
	$min = floor($min-($days*60*24)-($hours*60)); 
	$seconds = floor($seconds-($days*60*60*24)-($hours*60*60)-($min*60)); 
	return array('days' => $days,'hours' => $hours,'minutes' => $min,'seconds' => $seconds); 
}

function reloadACT($location) {
	header('Location: '.$location);
	exit;
}
function loginACT($id = '') {
	global $NAME_TITLE;
	global $user;
	errorACT('Du måste vara inloggad!<br><br>Logga in eller registrera dig.<br><br>
		<form action="auth.php?login" name="login_p" method="post" target="_parent">
		<input type="hidden" name="redir" value="'.((is_md5($id))?$id:'').'">
		<table cellspacing="0" style="margin-bottom: 12px;">
			<tr><td colspan="2" class="pdg" style="padding-left: 0;"><input type="text" class="txt" name="a" onfocus="this.select();" value="'.((!empty($_COOKIE['a65']))?secureOUT($_COOKIE['a65']):'').'"></td></tr>
			<tr><td colspan="2" class="pdg" style="padding-left: 0; padding-top: 3px;"><input type="password" class="txt" name="p"></td></tr>
			<tr><td class="pdg btm" style="padding-left: 0; padding-top: 0;"><a href="auth.php?register" target="_parent">Registrera</a><br><a href="auth.php?forgot" target="_parent">Glömt info</a></td><td class="pdg rgt btm"><input type="submit" class="br" value="logga in"></td></tr>
		</table>
		</form>
<script type="text/javascript">(document.login_p.a.value.length > 0)?document.login_p.p.focus():document.login_p.a.focus();</script>
');
}
function errorACT($txt, $link = '', $time = '5000', $topic = '') {
	global $NAME_TITLE, $cities;
	global $l, $user, $menu_out, $menu_in, $start, $obj, $sql, $tab, $strings;
	require("./_tpl/notice.php");
	if(!empty($link)) {
		$link = array($link, $time);
		require("./_tpl/mv.php");
	}
	exit;
}

function popupACT($msg, $mv = '', $time = '5000', $parent = '') {
	global $NAME_TITLE;
	require("./_tpl/notice_popup.php");
	exit;
}

function splashACT($msg, $link = '', $time = '5000') {
	global $NAME_TITLE;
	require("./_tpl/notice_splash.php");
	if(!empty($link)) {
		$link = array($link, $time);
		require("./_tpl/mv.php");
	}
	exit;
}

function cookieSET($id, $val, $expire = '') {
	if(empty($expire)) $expire = time() + 15 * 365 * 24 * 60 * 60;
	setcookie($id, $val, $expire);
	return $val;
}

function gettxt($id, $sql = false, $opt = '0') {
	global $tab;
	if(!is_object($sql)) {
		$sql = &new sql();
	}
	$result = $sql->queryResult("SELECT text_cmt FROM {$tab['text']} WHERE main_id = '$id' AND option_id = '$opt' LIMIT 1");
	if(!$result) return false; else return $result;
}

function getset($id, $sql = false, $opt = 'r', $type = 's', $order = 'main_id DESC') {
	global $tab;
	if(!is_object($sql)) {
		$sql = &new sql();
	}
	if($type == 's') {
		$result = $sql->queryResult("SELECT text_cmt FROM {$tab['settings']} WHERE main_id = '$id' AND type_id = '$opt' LIMIT 1");
		if(!$result) return false; else return $result;
	} elseif($type == 'm') {
		$result = $sql->query("SELECT main_id, text_cmt FROM {$tab['settings']} WHERE type_id = '$opt'");
		if(!$result) return false; else return $result;
	} elseif($type == 'mo') {
		$result = $sql->query("SELECT main_id, text_cmt FROM {$tab['settings']} WHERE type_id = '$opt' ORDER BY $order");
		if(!$result) return false; else return $result;
	}
}

function gettxt_multi($id, $sql = false) {
	global $tab;
	if(!is_object($sql)) {
		$sql = &new sql();
	}
	$result = $sql->query("SELECT text_cmt FROM {$tab['text']} WHERE main_id = '$id'");
	if(!$result) return false; else return $result;
}
function getStat() {
	$stat = gettxt_multi('stat');
	$stat[0] = explode(":", $stat[0][0]);
	$stat[1] = explode(":", $stat[1][0]);
	$stat['info'] = array(0 => 'Besök', 1 => 'Bilder', 2 => 'Bildvisningar', 3 => 'Bildkommentarer', 4 => 'Tyck till', '5' => 'Filmer', '6' => 'Filmnedladdningar', '7' => 'Filmkommentarer');
	return $stat;
}
function doInt($str) {
	return str_replace(",", "&nbsp;", number_format($str));
}

function dofakepaging($text = '&nbsp;') {
echo '
				<table cellspacing="0" class="bg brd" width="100%" style="margin-bottom: 4px;">
				<tr>
					<td class="pdg" style="width: 20%; padding-bottom: 0;"><nobr>'.$text.'</nobr></td>
					<td class="pdg cnt" style="width: 60%; padding-bottom: 0;"><nobr>&nbsp;</nobr></td>
					<td class="pdg rgt" style="width: 20%; padding-bottom: 0;"><nobr>&nbsp;</nobr></td>
				</tr>
				</table>
';
}
function dopaging($paging, $url, $type = 1, $vice = 1, $text = '&nbsp;', $rtext = '&nbsp;', $anchor = '') {
		$got = 0;
		if($paging['co'] < $paging['limit'] && $text == '&nbsp;') {
			return;
		}
		if($type == 1) {
		#if($paging['p'] > 1 || $paging['co'] > ($paging['slimit'] + $paging['limit'])) {
?>
				<table cellspacing="0" class="bg brd" width="100%" style="margin-bottom: 4px;">
				<tr>
					<td class="pdg" style="width: 20%;"><nobr><?=$text?></nobr></td>
					<td class="pdg" align="right" style="width: 22%;"><?=($paging['p'] > 1)?'<a href="'.$url.($paging['p']-1).$anchor.'" class="bld">« '.(($vice)?'bakåt':'framåt').'</a>':'&nbsp;';?></td>
					<td class="pdg" align="center" style="width: 16%;"><nobr><?=($paging['co'] > $paging['limit'])?'sida '.$paging['p']:'&nbsp;';?></nobr></td>
					<td class="pdg" style="width: 22%;"><?=($paging['limit'] != 'X' && ($paging['co'] > ($paging['slimit'] + $paging['limit'])))?'<a href="'.$url.($paging['p']+1).$anchor.'" class="bld">'.((!$vice)?'bakåt':'framåt').' »</a>':'&nbsp;';?></td>
					<td class="pdg" style="width: 20%;" align="right"><nobr><?=$rtext?></nobr></td>
				</tr>
				</table>
<?
		} else {
	$max = 10;
	$paging['s'] = $paging['slimit'];
	$pages = ceil($paging['co'] / $paging['limit']);
	if($pages > $max) {
		$paging['slimit'] = $paging['p'] - floor($max / 2);
		if($paging['slimit'] > ($pages - $max + 1)) {
			$paging['slimit'] = $pages - $max + 1;
		}
	} else $paging['slimit'] = 1;
	if($paging['slimit'] < 1) $paging['slimit'] = 1;

	$stop = $paging['slimit'] + $max - 1;

	if($stop > $pages) $stop = $pages;
	if(strpos($text, '%') !== false)
		$text = sprintf($text, (($paging['co'])?$paging['s']+1:0), ($paging['co'] > ($paging['s']+$paging['limit']))?$paging['s']+$paging['limit']:$paging['co'], $paging['co']);
?>
				<table cellspacing="0" class="bg brd" width="100%" style="margin-bottom: 4px;">
				<tr>
					<td class="pdg" style="width: 20%;"><nobr><?=$text?></nobr></td>
					<td class="pdg" align="right" style="width: 22%;"><?=($paging['p'] > 1)?'<a href="'.$url.($paging['p']-1).$anchor.'" class="bld">« '.(($vice)?'bakåt':'framåt').'</a>':'&nbsp;';?></td>
					<td class="pdg" align="center" style="width: 16%;"><nobr>
<?
if($paging['co'] > $paging['limit']) {
?>
sida
<?
	if($paging['slimit'] > 1) {
		echo ' ...';
	}
	if($stop) { for($i = $paging['slimit']; $i <= $stop; $i++) {
		if($paging['p'] != $i) {
			echo ' <a href="'.$url.$i.$anchor.'">'.$i.'</a>';
		} else {
			echo ' <b>'.$i.'</b>';
		}
	} } else echo '1';
	if($pages > $stop) {
		echo ' ... av '.$pages;
	}
} else echo '&nbsp;';
?>
					</nobr></td>
					<td class="pdg" style="width: 22%;"><?=($paging['p'] < $stop)?'<a href="'.$url.($paging['p']+1).$anchor.'" class="bld">'.((!$vice)?'bakåt':'framåt').' »</a>':'&nbsp;';?></td>
					<td class="pdg" style="width: 20%;" align="right"><nobr><?=$rtext?></nobr></td>
				</tr>
				</table>
<?
		}
		#}
}

function time_format($time) {
	$sec = round(($time/1000)*10)/10;
	$min = intval($sec/60); if($min > 0) { $min = $min . ' min '; } else $min = '';
	$sec = ((round(fmod($sec, 60)*10))/10);
	if(strlen($sec) == 2) $sec = $sec.'.0';
	return str_replace(',', '.', $min . $sec . ' sek');
}

function doDate($date, $type = 1, $small = false) {
	if($type == '5') {
		if($small == '3')
			$return = ucfirst(strftime("%a", strtotime($date))).' '.stripzero(strftime("%d", strtotime($date))) . strftime(" %b", strtotime($date)).date(" Y",strtotime($date));
		elseif($small == '2')
			$return = ucfirst(strftime("%A", strtotime($date))).' '.stripzero(strftime("%d", strtotime($date))) . strftime(" %B", strtotime($date));
		elseif($small == '1')
			$return = ucfirst(strftime("%A", strtotime($date))).' '.stripzero(strftime("%d", strtotime($date))) . strftime(" %b", strtotime($date)).date(" Y",strtotime($date)).' kl '. date('H:i', strtotime($date));
		else
			$return = ucfirst(strftime("%A", strtotime($date))).' '.stripzero(strftime("%d", strtotime($date))) . strftime(" %b", strtotime($date)).date(" Y",strtotime($date));
	} elseif($type == '6') {
		if(date("Y-m-d") == date("Y-m-d", strtotime($date)))
			$return = "idag";
		elseif(date("Y-m-d", strtotime("-1 day")) == date("Y-m-d", strtotime($date)))
			$return = "igår";
		elseif (date("Y-m-d", strtotime("+1 day")) == date("Y-m-d", strtotime($date)))
			$return = "imorgon";
		else $return = ucfirst(strftime("%A", strtotime($date))).' '.stripzero(strftime("%d", strtotime($date))) . strftime(" %B", strtotime($date));
		if(date("Y") > date("Y",strtotime($date)))
			$return .= date(" Y, ",strtotime($date));
	} else {
		if(date("Y-m-d") == date("Y-m-d", strtotime($date))) {
			#$return = "idag ";
			$return = "";
		} elseif (date("Y-m-d", strtotime("-1 day")) == date("Y-m-d", strtotime($date))) {
			$return = "igår ";
		} elseif (date("Y-m-d", strtotime("+1 day")) == date("Y-m-d", strtotime($date))) {
			$return = "imorgon ";
		} else {
			if($type == '3')
				$return = stripzero(strftime("%d", strtotime($date))) . strftime(" %b ", strtotime($date));
			else
				$return = stripzero(strftime("%d", strtotime($date))) . strftime(((!$small)?" %B ":" %b "), strtotime($date));
	        }
		if($type == '1') {
			$str = ($small?'y':'Y');
			if(date($str) > date($str,strtotime($date))) {
				$return .= date($str.", ",strtotime($date));
			} else {
				$return .= 'kl ';
			}
			$return .= date('H:i', strtotime($date));
		}
	}
	return $return;
}

function specialDate($date, $dday = 0, $type = 1) {
	if($type) {
		if($dday) {
			$first_date = stripzero(date("d", strtotime($date)));
			$first_month = strftime("%B", strtotime($date));
			$sec_date = stripzero(date("d", strtotime($date.' +1 DAY')));
			if($sec_date < $first_date) {
	# månadsskifte
				$first_month = stripzero(date("m", strtotime($date)));
				$sec_month = strftime("%B", strtotime($date.' +1 DAY'));
				return "$first_date/$first_month & $sec_date $sec_month";
			} else {
				return "$first_date & $sec_date $first_month";
			}
		} else {
			if(date("Y", strtotime($date)) == date("Y")) {
				return stripzero(date("d", strtotime($date))).' '.strftime("%B", strtotime($date));
			} else {
				return stripzero(date("d", strtotime($date))).' '.strftime("%B", strtotime($date)).' '.date("Y", strtotime($date));
			}
		}
	} else {
		return strftime("%A", strtotime($date)).' '.stripzero(date("d", strtotime($date))).' '.strftime("%B", strtotime($date));
	}
}
function doMailto($str) {
    return preg_replace("/([\w\.\-_]+)(@)([\w\.\-_]+)/i", "<a href=\"mailto:$0\">$0</a>",$str);
}
function doURL($str) {
	$p_match = array(
'/\[link\](h?t?t?p?:?\/?\/?)(.*)\[\/link\]/i',
"/(?<=^|\s)((https?|ftps?):\/\/)?(([-_a-zA-Z]+\.)+)([a-zA-Z]{2,6})(\/[^\]\[\s]*)?(?=\s|$)/e",
"/\[link=((https?|ftps?):\/\/)?(([-_a-zA-Z]+\.)*)([a-zA-Z]{2,6})(\/[^\]\[\s]*)?\]([^\]\[]+)\[\/link\]?/e"
);
	$p_replace = array(
'<a href="http://\\2" target="_blank" class=\"bld\">\\1\\2<</a>',
"'$1'!=''? '<a href=\"$1$3$5$6\" target=\"_blank\" class=\"bld\">$3$5$6</a>':'<a href=\"http://$3$5$6\" target=\"_blank\" class=\"bld\">$3$5$6</a>'",
"'$1'!=''?'<a href=\"$1$3$5$6\" target=\"_blank\" class=\"bld\">$7</a>':'<a href=\"http://$3$5$6\" target=\"_blank\" class=\"bld\">$7</a>'"
);
	$str = preg_replace($p_match, $p_replace, $str);
    return $str;
}




*/
?>