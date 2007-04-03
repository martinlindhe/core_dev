<?
define("NRMSTR", '<strong><b><blockquote><font><p><br><hr><a><img><li><ol><ul><u><em><strike><b>');
define("ADMNRMSTR", '<strong><b><blockquote><font><p><br><hr><a><img><li><ol><ul><u><em><strike><b>');
function makeNR($str, $alias, $date, $to) {
	return "<br><br>>Från: ".$alias."<br>>Till: ".$to."<br>>Skickat: ".$date."<br>>".str_replace("<BR>", "<BR>>", trim($str));
}
function formatText($str, $is = 0, $isVip = true, $user_id = '') {
@set_time_limit(0);
	if($isVip) {
		$arr = array('&lt;', '&gt;');
		$rep = array('&#60;', '&#62;');
		$str = str_replace($arr, $rep, $str);
		$str = preg_replace("#javascript\:#is", "java script:", $str);
		$str = preg_replace("#vbscript\:#is", "vb script:", $str);
		$str = str_replace("`", "`", $str);
		$str = preg_replace("#moz\-binding:#is", "moz binding:", $str);
		$str = html_entity_decode($str);
		$str = preg_replace('#(&\#*\w+)[\x00-\x20]+;#U',"$1;",$str);
		$str = preg_replace('#(&\#x*)([0-9A-F]+);*#iu',"$1$2;",$str);
		$str = preg_replace('#(<[^>]+[\s\r\n\"\'])(on|xmlns)[^>]*>#iU',"$1>",$str);
		$str = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*)[\\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iU','$1=$2nojavascript',$str);
		$str = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iU','$1=$2novbscript',$str);
		$str = preg_replace('#</*\w+:\w[^>]*>#i',"",$str);
		do {
			$oldstr = $str;
			$str = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i',"",$str);
		} while ($oldstr != $str);
		$match = array("#\<img#");
		$replace = array('<img onload="if(this.width > 658) this.width = 658;" ');
		$str = strip_tags($str, ADMNRMSTR);
		$str = preg_replace($match, $replace, $str);
		$str = stripslashes($str);
	} else {
		$str = nl2br(secureOUT($str));
	}

	return $str;
}

function secureFormat($str) {
	$str = secureOUT($str);
	$str = preg_replace("#javascript\:#is", "java script:", $str);
	$str = preg_replace("#vbscript\:#is", "vb script:", $str);
	$str = str_replace("`"               , "`"       , $str);
	$str = preg_replace("#moz\-binding:#is", "moz binding:", $str);
	$str = preg_replace('#iframe#is', '&#105;frame', $str);
	return $str;
}

function icon($info, $isadmin = false, $own = false) {
	echo '<script type="text/javascript" src="js_icon.js"></script>';
	$icon = true;
	if(!empty($info)) {
		$info = explode(':', $info);
		# FIX!
		$info[3] = (!empty($info[3]))?$info[3]:1;
		$info[4] = (!empty($info[4]))?$info[4]:1;
	} elseif($own) {
		$info = array(200, 160, 1, 1, 1);
	} else $icon = false;
	#if($isadmin && $icon) $info[1] += 22;
	if($own) {
	echo '<div id="div_a" title="Klicka för att ändra!" onclick="if(this.childNodes[2].value == \'flytta\') doStart(this, \'a\'); else doStop();" style="z-index: 1000; position: absolute; width: 52px; height: 75px; left: '.$info[0].'px; top: '.$info[1].'px;"><img src="./_img/icon_arr'.addZero($info[4]).'-'.$info[3].'.gif" id="pic:a'.$info[3].':'.$info[4].'" class="cur"><br><input type="button" style="width: 53px; visibility: hidden;" id="btn_a" value="flytta"></div>';

echo '
<form name="floatit" method="post" action="user.php" target="commain">
<input type="hidden" name="pos" value="0:0:0">
</form>
';
	} elseif($icon) {
		echo '<div id="div_a" style="position: absolute; width: 52px; height: 75px; left: '.$info[0].'px; top: '.$info[1].'px; z-index: '.($info[2]+100).';"><img src="./_img/icon_arr'.addZero($info[4]).'-'.$info[3].'.gif" id="pic:a'.$info[3].'"></div>';
	}
}

function iconSave($sql, $id, $pos = '', $isadmin = false) {
	global $tab;
	$ver_count = 3;
	if(!empty($pos)) {
		$value = '';
		$pos = @explode(':', $pos);
		if(count($pos) == '5') {
			if(intval($pos[0]) > 2 && intval($pos[0]) < (688-52) && intval($pos[1]) > 0 && intval($pos[1]) < 5000 && is_numeric($pos[2]) && $pos[3] > 0 && $pos[3] <= 4 && is_numeric($pos[3]) && is_numeric($pos[4]) && $pos[4] >= 1 && $pos[4] <= $ver_count) {
				#if($isadmin) $pos[1] -= 22;
				$res = $sql->queryUpdate("UPDATE {$tab['obj']} SET content = '".secureINS(intval($pos[0]).':'.intval($pos[1]).':'.intval($pos[2]).':'.intval($pos[3]).':'.intval($pos[4]))."' WHERE owner_id = '".secureINS($id)."' AND content_type = 'user_pres_icon' LIMIT 1");
				if(!$res) {
					$i = $sql->queryInsert("INSERT INTO {$tab['obj']} SET content = '".secureINS(intval($pos[0]).':'.intval($pos[1]).':'.intval($pos[2]).':'.intval($pos[3]).':'.intval($pos[4]))."', content_type = 'user_pres_icon', owner_id = '".secureINS($id)."'");
					$sql->queryInsert("INSERT INTO {$tab['rel']} SET object_id = '$i', content_type = 'user_profile', owner_id = '".secureINS($id)."'");
				}
			}
		}
	}
}
?>