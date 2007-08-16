<?
	//returns a text field. $id is a text string, $opt is a enum
	function gettxt($id, $opt = '0', $ext = false)
	{
		global $db;

		$result = $db->getOneItem('SELECT text_cmt FROM s_text WHERE main_id = "'.$db->escape($id).'" AND option_id = "'.$db->escape($opt).'" LIMIT 1');

		if (!$ext) return $result;
		return extOUT($result);
	}

	//returns the last $limit users who has logged in
	function getLastLoggedIn($limit = 10)
	{
		global $db;
		if (!is_numeric($limit)) return false;

		$q = 'SELECT u.id_id, u.u_alias, u.u_sex, u.u_birth, u.level_id, u.account_date, u_picid, u.u_picvalid, u.u_picd FROM s_userlogin s '.
			'INNER JOIN s_user u ON u.id_id = s.id_id AND u.status_id = "1" ORDER BY s.main_id DESC LIMIT '.$limit;
		return $db->getArray($q);
	}

	//returns the last $limit images uploaded to the public galleries
	function getLastGalleryUploads($limit = 5)
	{
		global $db;
		if (!is_numeric($limit)) return false;

		$q = 'SELECT main_id, picd, pht_name, pht_cmt FROM s_userphoto WHERE view_id = "1" AND status_id = "1" AND hidden_id = "0" ORDER BY main_id DESC LIMIT '.$limit;
		return $db->getArray($q);
	}

	function secureOUT($str, $nl = false)
	{
		return ($nl?nl2br(stripslashes(htmlentities($str))):stripslashes(htmlentities($str)));
	}

	function extOUT($str, $class = '')
	{
		return nl2br(doURL(doMailto(stripslashes($str), $class), $class));
	}


	function doURL($str, $class = '')
	{
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

	function doMailto($str, $class = '')
	{
	    return preg_replace("/([\w\.\-_]+)(@)([\w\.\-_]+)/i", "<a href=\"mailto:$0\"".($class?' class="'.$class.'"':'').">$0</a>",$str);
	}

	function l($type = 'main', $action = 'start', $id = '', $key = '')
	{
		#return 'index.php?type='.$type.'&action='.$action.($id?'&id='.$id:'');
		return '/'.$type.'/'.$action.($id != ''?'/'.$id.($key != ''?'/'.$key:''):'').'/';
	}

	function checkBan($splash = 0)
	{
		global $db;
		
		$q = 'SELECT COUNT(*) FROM s_ban WHERE ban_ip = "'.$db->escape($_SERVER['REMOTE_ADDR']).'"';
		if ($db->getOneItem($q)) {
			if($splash) errorSPLASHACT("Tyvärr! Du är blockerad.");
			else errorACT("Tyvärr! Du är blockerad.");
		}
	}

	function makeButton($bool, $js, $img, $text, $number = false)
	{
		global $config;
		echo '<div class="'.($bool?'btnSelected':'btnNormal').'"'.($js?'onclick="'.$js.'"':'').'>';
		echo '<table summary="" cellpadding="0" cellspacing="0">';
		echo '<tr>';
			echo '<td width="3"><img src="'.$config['web_root'].'_gfx/themes/btn_c1.png" alt=""/></td>';
			echo '<td style="background: url(\''.$config['web_root'].'_gfx/themes/btn_head.png\');"></td>';
			echo '<td width="3"><img src="'.$config['web_root'].'_gfx/themes/btn_c2.png" alt=""/></td>';
		echo '</tr>';

		echo '<tr style="height: 18px">';
			echo '<td width="3" style="background: url(\''.$config['web_root'].'_gfx/themes/btn_left.png\');"></td>';
			echo '<td style="padding-left: 19px; padding-right: 4px; padding-top: 1px;">';
			if ($img) echo '<img src="'.$config['web_root'].'_gfx/'.$img.'" style="position: absolute; top: 5px; left: 4px;" alt=""/> ';
			echo $text;
			if ($number !== false) echo '&nbsp;&nbsp;'.$number;
			echo '</td>';
			echo '<td width="3" style="background: url(\''.$config['web_root'].'_gfx/themes/btn_right.png\');"></td>';
		echo '</tr>';

		echo '<tr>';
			echo '<td width="3"><img src="'.$config['web_root'].'_gfx/themes/btn_c3.png" alt=""/></td>';
			echo '<td style="background: url(\''.$config['web_root'].'_gfx/themes/btn_foot.png\');"></td>';
			echo '<td width="3"><img src="'.$config['web_root'].'_gfx/themes/btn_c4.png" alt=""/></td>';
		echo '</tr>';

		echo '</table>';
		echo '</div>';
	}

	function makeMenu($sel, $arr)
	{
		$i = false;
		$ret = '';
		foreach($arr as $key => $val) {
			if(!$i) $i = true; else $ret .= ' - ';
			$ret .= '<a class="wht" href="'.$val[0].'">'.(strtolower($key) == strtolower($sel)?'»'.$val[1].'«':$val[1]).'</a>';
		}
		return $ret;
	}

	function nicedate($date, $type = 1)
	{
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

	function stripzero($str)
	{
		if(substr($str, 0, 1) == '0') $str = substr($str, 1, 1);
		return $str;
	}

	//läser settings
	function getset($id, $opt = 'r', $type = 's', $order = 'main_id DESC')
	{
		global $db;
		if (!is_numeric($id)) return false;

		if($type == 's') {
			$q = 'SELECT text_cmt FROM s_textsettings WHERE main_id = '.$id.' AND type_id = "'.$db->escape($opt).'" LIMIT 1';
			return $db->getOneItem($q);
		}
		if($type == 'm') {
			$q = 'SELECT main_id, text_cmt FROM s_textsettings WHERE type_id = "'.$db->escape($opt).'"';
			return $db->getOneRow($q);
		}
		if($type == 'mo') {
			$q = 'SELECT main_id, text_cmt FROM s_textsettings WHERE type_id = "'.$db->escape($opt).'"';
			return $db->getOneRow($q);
		}
	}

	function date_diff($current, $past)
	{
		$seconds = strtotime($current) - strtotime($past);    
		$min = $seconds/60; 
		$hours = $min/60; 
		$days = floor($hours/24); 
		$hours = floor($hours-($days*24)); 
		$min = floor($min-($days*60*24)-($hours*60)); 
		$seconds = floor($seconds-($days*60*60*24)-($hours*60*60)-($min*60)); 
		return array('days' => $days,'hours' => $hours,'minutes' => $min,'seconds' => $seconds); 
	}

	function paging($p = '1', $limit = 20)
	{
		if(empty($p) || !is_numeric($p)) {
			$p = 1;
		}
		return array('p' => $p, 'limit' => $limit, 'slimit' => ($p - 1) * $limit);
	}

	function dopaging($paging, $url, $anchor = '', $width = 'med', $text = '&nbsp;', $vice = 1)
	{
		$max = 10;
		$paging['s'] = $paging['slimit'];
		$pages = ceil($paging['co'] / $paging['limit']);
		if ($pages > $max) {
			$paging['slimit'] = $paging['p'] - floor($max / 2);
			if($paging['slimit'] > ($pages - $max + 1)) {
				$paging['slimit'] = $pages - $max + 1;
			}
		} else {
			$paging['slimit'] = 1;
		}

		if ($paging['slimit'] < 1) $paging['slimit'] = 1;
		$stop = $paging['slimit'] + $max - 1;
		if ($stop > $pages) $stop = $pages;
		if (strpos($text, '%') !== false)
			$text = sprintf($text, (($paging['co'])?$paging['s']+1:0), ($paging['co'] > ($paging['s']+$paging['limit']))?$paging['s']+$paging['limit']:$paging['co'], $paging['co']);

		if ($width == 'med' || $width == 'medmin' || $width == 'big' || $width == 'medbig' || $width == 'biggest')
			echo '<table cellspacing="0" summary="" style="font-family: Verdana; font-size: 10px; width: '.($width == 'medmin'?'596':($width == 'big'?'596':($width == 'medbig'?'596':($width == 'biggest'?'786':'596')))).'px;"><tr>
			<td class="pdg" nowrap="nowrap">'.$text.'</td>
			<td class="pdg" align="right" nowrap="nowrap">'.($paging['p'] > 1?'<a href="'.$url.($paging['p']-1).$anchor.'" class="bld">« '.(($vice)?'bakåt':'framåt').'</a>':'&nbsp;').'&nbsp;&nbsp;';

		if ($paging['co'] > $paging['limit']) {
			echo 'sida';
			if ($paging['slimit'] > 1) {
				echo ' ...';
			}
			if ($stop) {
				for($i = $paging['slimit']; $i <= $stop; $i++) {
					if($paging['p'] != $i) {
						echo ' <a href="'.$url.$i.$anchor.'">'.$i.'</a>';
					} else {
						echo ' <b>'.$i.'</b>';
					}
				}
			} else echo '1';
			if ($pages > $stop) {
				echo ' ... av '.$pages;
			}
		} else echo '&nbsp;';

		if ($width == 'big' || $width == 'medbig' || $width == 'med' || $width == 'medmin' || $width == 'biggest')
			echo '&nbsp;&nbsp;'.($paging['p'] < $stop?'<a href="'.$url.($paging['p']+1).$anchor.'" class="bld">'.((!$vice)?'bakåt':'framåt').' »</a>':'&nbsp;').'</td></tr></table>';
	}


	//*******************************************
	//unused/not-yet-cleaned-up functions below:
	//*******************************************
/*
	function createGenerated($img, $txt1 = '', $txt2 = '')
	{
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

	function getGenerated($img, $txt1 = '', $txt2 = '')
	{
		if (!file_exists('./_output/generated/'.md5($img.$txt1.$txt2).'.png')) {
			createGenerated($img, $txt1, $txt2);
		}
		return '/_output/generated/'.md5($img.$txt1.$txt2).'.png';
	}

	function array_unshift_ref(& $ioArray, $iValueWrappedInAnArray)
	{ 
	   $lNewArray = false; 
	   foreach (array_keys ($ioArray) as $lKey) 
	       $lNewArray[$lKey+1] = & $ioArray[$lKey]; 
	   $ioArray = array (& $iValueWrappedInAnArray[0]); 
	   if ($lNewArray) 
	       foreach (array_keys ($lNewArray) as $lKey) 
	             $ioArray[] = & $lNewArray[$lKey]; 
	   return count($ioArray); 
	} 

	function paging_limit($limit)
	{
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

	function specialDate($date, $dday = 0, $type = 1)
	{
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

	function doInt($str)
	{
		return str_replace(",", "&nbsp;", number_format($str));
	}

	function flash($s)
	{
		return urlencode(utf8_encode($s));
	}

	function headline($id = '')
	{
		return CS.'_objects/_heads/head_'.$id.'.gif';
	}

	function addzero($str)
	{
		if(strlen($str) == '1') $str = '0'.$str;
		return $str;
	}

	function execSt($end = 0, $notset = 0)
	{
		global $start;
		if(!$end) {
			list($usec, $sec) = explode(" ",microtime());
			if(!$notset) $start = ((float)$usec + (float)$sec);
			else return ((float)$usec + (float)$sec);
		} else {
			echo substr((execSt(0, 1) - $start),0,10);
		}
	}

	function secureINS($str)
	{
		return addslashes($str);
	}

	function safeOUT($str, $nl = true)
	{
		return ($nl?stripslashes(nl2br($str)):stripslashes($str));
	}

	function is_md5($str)
	{
		if(!empty($str) && preg_match('/^[A-Fa-f0-9]{32}$/', $str))
			return true;
		else
			return false;
	}

	function reloadACT($url)
	{
		header('Location: '.$url);
		exit;
	}

	function splashIACT($msg, $topic, $tc = 1, $class = '')
	{
		errorACT($msg, '', 'popup', '', 5000, $topic, $tc, $class);
	}

	function errorIACT($msg, $topic, $tc = 1, $class = '')
	{
		errorACT($msg, '', 'main', '', 5000, $topic, $tc, $class);
	}

	function errorTACT($msg, $url, $time)
	{
		errorACT($msg, $url, 'main', '', $time);
	}

	function errorACT($msg, $url = '', $type = 'main', $parent = '', $time = 5000, $topic = '', $tc = 1, $class = '')
	{
		global $sql, $user, $start, $t, $l;

		if($type == 'main') {
			$page = 'error.php';
			if(!$l) $page = 'error_splash.php';
			
			if(!empty($url) && substr($url, 0, 1) != '1') {
				require(dirname(__FILE__).'/../_design/'.$page);
				require(dirname(__FILE__).'/../_design/mv.php');
			} elseif(substr($url, 0, 1) == '1') {
				$url = substr($url, 1);
				require(dirname(__FILE__).'/../_design/'.$page);
			} else {
				require(dirname(__FILE__).'/../_design/'.$page);
			}
		} elseif($type == 'popup') {
			require(dirname(__FILE__).'/../_design/error_popup.php');
		} elseif($type == 'popupbig') {
			require(dirname(__FILE__).'/../_design/error_popupbig.php');
		} elseif($type == 'splash') {
			require(dirname(__FILE__).'/../_design/error_splash.php');
			if(!empty($url)) {
				require(dirname(__FILE__).'/../_design/mv.php');
			}
		}
		die;
	}

	function popupLACT($msg, $cnt = false)
	{
		errorACT($msg, 1, 'popup', '', 5000, $cnt);
	}

	function bigpopupACT($msg)
	{
		errorACT($msg, '', 'popupbig', '', 0, '', 1, 'wht');
	}

	function popupACT($msg, $url = '', $parent = '', $time = 5000)
	{
		errorACT($msg, $url, 'popup', $parent, $time);
	}

	function splashLACT($msg, $url = '', $cnt = '')
	{
		errorACT($msg, $url, 'splash', '', 5000, $cnt);	
	}

	function splashACT($msg, $url = '', $time = 5000)
	{
		errorACT($msg, $url, 'splash', '', $time);
	}

	function loginACT()
	{
		errorACT('Du är inte inloggad! Logga in uppe i menyn eller registrera dig.');
	}

	function doADP($pos, $right = false)
	{
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

	function cookieSET($id, $val, $expire = '')
	{
		if(empty($expire)) $expire = time() + 15 * 365 * 24 * 60 * 60;
		setcookie($id, $val, $expire, '/');
		return $val;
	}
*/
?>
