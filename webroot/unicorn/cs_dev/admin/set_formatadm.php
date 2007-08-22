<?
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
