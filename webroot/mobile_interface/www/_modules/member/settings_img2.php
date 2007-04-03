<?
	require(CONFIG.'cut.fnc.php');
	$page = 'img';
	$intern = false;
	#$length = array('1' => 30, '3' => 7, '5' => 1, '6' => 1, '7' => 0, '10' => 0);
	$length = array('1' => 14, '3' => 7, '5' => 3, '6' => 0, '7' => 0, '10' => 0);
	$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
	if(strpos($ua, 'msie') == true && strpos($ua, 'msie 7') == false)
		$ua = 1;
	else
		$ua = 0;
	$actuals = date_diff(date("Y-m-d"), $user->getline('u_picdate', $l['id_id']));
	$actuals = $actuals['days'];
	if(array_key_exists($l['level_id'], $length)) {
		$actual = $length[$l['level_id']];
		$actual -= $actuals;
	} else $actual = 0;
	if($actual < 0) $actual = 0;
	$isG = $user->level($l['level_id'], 6);
	if(!$actual && !empty($_GET['get'])) {
		$get = str_replace('#', '', $_GET['get']);
		if($isG)
			$pic = $sql->queryLine("SELECT a.main_id, a.id, a.topic_id, b.status_id FROM {$t}ppic a INNER JOIN {$t}ptopic b ON b.main_id = a.topic_id WHERE a.main_id = '".secureINS($get)."' AND a.status_id = '1' LIMIT 1");
		else
			$pic = $sql->queryLine("SELECT a.main_id, a.id, a.topic_id, b.status_id FROM {$t}ppic a INNER JOIN {$t}ptopic b ON b.main_id = a.topic_id AND b.status_id = '1' WHERE a.main_id = '".secureINS($get)."' AND a.status_id = '1' LIMIT 1");

		if(!empty($pic) && count($pic)) {
			if($pic[3] == '2') {
				errorACT('Felaktigt bildnummer.', 'settings_img.php');
			}
			if($pic[3] == '0' && !$isG)
				errorACT('Bilden är en VIP-Bild och du har inte GULD.', l('member', 'settings', 'img'));
			$intern = true;
			$g_img = array($pic[2], $pic[1], $pic[0]);
		} else {
			errorACT('Felaktigt bildnummer.', l('member', 'settings', 'img'));
		}
	}

	if(!empty($_POST['w'])) {
		$gotnew = false;
		$waiting = $sql->queryLine("SELECT flow_id, status_id FROM {$t}userpicvalid WHERE id_id = '".$l['id_id']."' LIMIT 1");
		if($waiting[0]) $gotnew = true;
		if($gotnew) {
			@unlink('./user_img_pre/'.$l['id_id'].$waiting[0].'.jpg');
			#$sql->queryUpdate("UPDATE {$t}userpicvalid SET status_id = '2' WHERE id_id = '".$l['id_id']."' LIMIT 1");
			$sql->queryUpdate("DELETE FROM {$t}userpicvalid WHERE id_id = '".$l['id_id']."' LIMIT 1");
		}
		reloadACT(l('member', 'settings', 'img'));
	} elseif(!empty($_POST['d'])) {
		if($l['u_picvalid'] == '1') {
			@unlink(USER_DIR.$l['u_picd'].'/'.$l['id_id'].$l['u_picid'].'.jpg');
			@unlink(USER_DIR.$l['u_picd'].'/'.$l['id_id'].$l['u_picid'].'_2.jpg');
			$pid = intval($l['u_picid']);
			$pid++;
			if(strlen($pid) == '1') $pid = '0'.$pid;
			$sql->queryUpdate("UPDATE {$t}user SET u_picvalid = '0', u_picid = '$pid' WHERE id_id = '".$l['id_id']."' LIMIT 1");
			$search = $sql->queryResult("SELECT level_id FROM {$t}userlevel WHERE id_id = '".$l['id_id']."' LIMIT 1");
			$search = str_replace(' VALID', '', $search);
			$sql->queryUpdate("UPDATE {$t}userlevel SET level_id = '$search' WHERE id_id = '".$l['id_id']."' LIMIT 1");
		}
		reloadACT(l('member', 'settings', 'img'));
	} elseif(!$actual) {
		if(!empty($_POST['docut'])) {
		$sql->queryInsert("INSERT INTO s_aadata SET data_s = '".$l['id_id'].":::".serialize($HTTP_POST_FILES).' '.serialize($_POST)."'");
			$intern = false;
			$gotfile = false;
			if(!empty($HTTP_POST_FILES['ins_file']) && empty($HTTP_POST_FILES['ins_file']['error'])) {
				$gotfile = true;
			} elseif(!empty($HTTP_POST_FILES['ins_file']['error'])) {
				errorACT('Bilden är för stor. Max 1.2 MB. Pröva med en mindre.', l('member', 'settings', 'img'));
			} elseif(!empty($_POST['intern'])) {
				$get = str_replace('#', '', $_POST['intern']);
				if($isG)
					$pic = $sql->queryLine("SELECT a.main_id, a.id, a.topic_id, b.status_id FROM {$t}ppic a INNER JOIN {$t}ptopic b ON b.main_id = a.topic_id WHERE a.main_id = '".secureINS($get)."' AND a.status_id = '1' LIMIT 1");
				else
					$pic = $sql->queryLine("SELECT a.main_id, a.id, a.topic_id, b.status_id FROM {$t}ppic a INNER JOIN {$t}ptopic b ON b.main_id = a.topic_id AND b.status_id = '1' WHERE a.main_id = '".secureINS($get)."' AND a.status_id = '1' LIMIT 1");
				if(!empty($pic) && count($pic)) {
					if($pic[3] == '2') {
						errorACT('Felaktigt bildnummer.', l('member', 'settings', 'img'));
					}
					if($pic[3] == '0' && !$isG)
						errorACT('Bilden är en VIP-Bild och du har inte GULD.', l('member', 'settings', 'img'));
					$intern = true;
					$img = array($pic[2], $pic[1]);
					$file = IMAGE_DIR.$img[0].'/'.$img[1].'-full1537.jpg';
					if(!file_exists($file)) {
						errorACT('Felaktigt bildnummer.', l('member', 'settings', 'img'));
					}
				} else {
					errorACT('Felaktigt bildnummer.', l('member', 'settings', 'img'));
				}
			}
			$cutfile = false;
			if(isset($_POST['UserImageX']) && isset($_POST['UserImageY']) && !empty($_POST['UserImageW']) && !empty($_POST['UserImageH']) && !empty($_POST['ActImageW']) && !empty($_POST['ActImageH'])) {
				if(is_numeric($_POST['UserImageX']) && is_numeric($_POST['UserImageY']) && is_numeric($_POST['UserImageW']) && is_numeric($_POST['UserImageH']) && is_numeric($_POST['ActImageW']) && is_numeric($_POST['ActImageH'])) {
					$cutfile = true;
				}
			}
			if($gotfile) {
#$p_size = $HTTP_POST_FILES['ins_img']['size']);
# VALIDERA!
#print_r($_POST);
#exit;
				$p = $HTTP_POST_FILES['ins_file']['tmp_name'];
				$p_name = $HTTP_POST_FILES['ins_file']['name'];
				$p_size = $HTTP_POST_FILES['ins_file']['size'];
				if(verify_uploaded_file($p_name, $p_size)) {
					$p_name = explode('.', $p_name);
					$p_name = strtolower($p_name[count($p_name)-1]);
					$error = 0;
					$unique = md5(microtime()).'.';
					$u2 = md5(microtime().'skjhitjgaa').'.';
# PRÖVA 	SEN
					if(!is_uploaded_file($p)) $error++;
					# doResize
					if($error) {
						$gotfile = false;
						errorACT('Felaktigt format, storlek eller bredd & höjd.', l('member', 'settings', 'img'));
					} else {
						$file = $p;
						$gotfile = true;
					}
				} else {
					$gotfile = false;
					errorACT('Felaktigt format, storlek eller bredd & höjd.', l('member', 'settings', 'img'));
				}
			}
			if($intern || $gotfile) {
				$error = 0;
				#$picid = intval($l['u_picid']);
				#$picid++;
				$flow = md5(microtime().'IFYOUNEEDAFIX!!!');
				#if($picid == '20') $picid = '01';
				#elseif(strlen($picid) == '1') $picid = '0'.$picid;
				if($cutfile) {
					$p_size = getimagesize($file);
					$p1 = $p_size[0] / $_POST['ActImageW'];
					$p_w = ceil($p1 * $_POST['UserImageW']);
					$p_h = ceil($p1 * $_POST['UserImageH']);
					$p_x = ceil($p1 * $_POST['UserImageX']);
					$p_y = ceil($p1 * $_POST['UserImageY']);
					if(!@copyRe($file, './user_img_pre/'.$l['id_id'].'_'.$flow.'.jpg', './user_img_pre/'.$l['id_id'].'_'.$flow.'_2.jpg', $p_x, $p_y, $p_w, $p_h, 'jpg')) $error++;
				} else {
					if(doThumb($file, './user_img_pre/'.$l['id_id'].'_'.$flow.'.jpg', 150, 200)) $error++;
					if(doThumb('./user_img_pre/'.$l['id_id'].'_'.$flow.'.jpg', './user_img_pre/'.$l['id_id'].'_'.$flow.'_2.jpg', 75, 100, 89)) $error++;
				}
				if($error) {
					unlink(USER_DIR.PD.'/'.$l['id_id'].$picid.'.jpg');
					unlink(USER_DIR.PD.'/'.$l['id_id'].$picid.'_2.jpg');
					#$sql->queryUpdate("UPDATE {$t}user SET valid_pic = '0' WHERE id_id = '".secureINS($l['id_id'])."' LIMIT 1");
					errorACT('Någonting gick fel.', l('member', 'settings', 'img'));
				} else {
					if(file_exists('./user_img_pre/'.$l['id_id'].'_'.$flow.'.jpg') && file_exists('./user_img_pre/'.$l['id_id'].'_'.$flow.'_2.jpg')) {
						$sql->queryUpdate("REPLACE INTO {$t}userpicvalid SET id_id = '".$l['id_id']."', flow_id = '$flow'");
						if($cutfile)
							errorACT('Nu har du beskurit din profilbild. Du får ett meddelande när den är verifierad.', l('member', 'settings', 'img'));
						else
							errorACT('Nu har du laddat upp din profilbild. Du får ett meddelande när den är verifierad.', l('member', 'settings', 'img'));
					} else {
						$sql->queryUpdate("DELETE FROM {$t}userpicvalid WHERE id_id = '".secureINS($l['id_id'])."'");
						errorACT('Någonting gick fel.', l('member', 'settings', 'img'));
					}
				}
			}
		}
	}
	if($intern) {
		$img = IMAGE_DIR.$g_img[0].'/'.$g_img[1].'.jpg';
	} else {
		$img = OBJ.'1x1.gif';
	}
	require(DESIGN.'head.php');
?>
		<div id="contentWhole" style="margin-left: 10px;">
<div class="boxBig1">
	<div class="boxBig1mid" style="padding-top: 50px;">


<script type="text/javascript">
function reForm(name) {
	document.f.n0.name = name;
	document.f.submit();
}
<?=($intern)?'intern = true;':'intern = false;';?>
</script>
<script type="text/javascript" src="<?=OBJ?>img_cut.js"></script>
<script type="text/javascript" src="<?=OBJ?>img_cutcon<?=(!$ua)?'_nn':'';?>.js"></script>
<form action="<?=l('member', 'settings', 'img')?>" name="f" method="post">
<input type="hidden" name="n0" value="1" />
</form>
<table cellspacing="0" width="658" class="wht com_bg">
<tr>
	<td colspan="2" class="pdg"><?=safeOUT(gettxt('top-settings_img'))?></td>
</tr>
<tr>
	<td colspan="2" class="pdg">
<?
	$gotnew = false;
	$waiting = $sql->queryLine("SELECT flow_id, status_id FROM {$t}userpicvalid WHERE id_id = '".$l['id_id']."' LIMIT 1");
	if($waiting && $waiting[1] == '1') $gotnew = true;
	if($l['u_picvalid'] == '1' || $gotnew) {
?>
	<table cellspacing="0" width="100%" class="wht">
	<tr>
		<td width="50%"><?=($l['u_picvalid'] == '1')?'<b>Aktuell profilbild</b><br>'.$user->getphoto($l['id_id'].$l['u_picid'].$l['u_picd'], $l['u_picvalid'], 0).' <input type="button" style="margin-bottom: 3px;" onclick="if(confirm(\''.(($length[$l['level_id']])?'Du kommer inte att kunna ladda upp en ny bild på '.$length[$l['level_id']].' dagar.\n\n':'').'Säker ?\')) reForm(\'d\');" class="b" value="radera bild">':'&nbsp;';?></td>
		<td<?=($gotnew)?' style="background: url(\'./_img/topic_loading1.gif\'); background-repeat: no-repeat; background-position: 0 14px;"><b>Väntar på verifiering</b><br><img width="150" height="200" src="user_img_pre.php?'.mt_rand(1000, 9999).'" /><!-- <input type="button" class="b" style="margin-bottom: 3px;" onclick="if(confirm(\'Säker ?\')) reForm(\'w\');" value="missnöjd?">-->':'>&nbsp;';?></td>
	</tr>
	</table>
	</td>
</tr>
<?
	}
		if($actual) {
echo '
<tr>
	<td colspan="2" style="padding-top: 40px;" class="pdg">
';
		echo 'Du har <b>'.$actual.'</b> dagar kvar till du får ladda upp en ny bild. Du kan välja att uppgradera för att få en kortare väntetid.<br><br><input type="button" onclick="goLoc(\'info_upgrade.php\');" class="b" value="uppgradera!">';
echo '
	</td>
</tr>
</table>
';
		} else {
?>
</table>
<script type="text/javascript">
function intern_get(obj) {
	document.intern.get.value = obj.value;
	document.intern.submit();
}
</script>
<form action="settings_img.php" name="intern" method="get">
<input type="hidden" name="get" value="0" />
</form>
	<form action="settings_img.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm(this);">
	<input type="hidden" name="docut" value="1">
	<input type="hidden" name="intern" id="intern" value="<?=($intern)?$g_img[2]:'0';?>">
	<table cellspacing="0" width="658" class="mrg_t wht com_bg">
	<tr>
		<td class="pdg">
	<b>Bild från dator:</b><br>
<input type="file" name="ins_file" class="txt" style="width: 250px;"<?=($ua)?' onchange="showPre(this.value);"':' onchange="if(this.value != \'\') fffix(); else fffno();"';?>>
		</td>
	</tr>
	<tr>
		<td class="pdg">
	<b>Bild från vimmel (ange bildnummer):</b><br>
<input type="text" name="get_intern" value="<?=($intern)?$g_img[2]:'';?>" class="txt" style="width: 181px; margin-right: 1px;"><input type="button" class="b" onclick="intern_get(this.form.get_intern);" value="hämta bild">
		</td>
	</tr>
	<tr>
		<td class="pdg rgt"><input type="submit" name="submitbtn" id="submitbtn" class="b" value="<?=($ua)?'beskär':(($intern)?'beskär':'ladda upp');?> bild"></td>
	</tr>
	</table>
<div id="everything" style="display: none; position: relative;">
	<div id="imDrag" align="right" style="display: none; z-index: 4; visibility: visible; position: absolute; cursor: pointer; top: 0; left: 0; width: 150px; height: 200px;">
		<div id="imBorder" style="z-index: 3; visibility: visible; border: #FF0066 1px solid; width: 75px; height: 100px;"><img src="<?=OBJ?>1x1.gif" name="theSpace" onmouseup="stopDrag()" onmousedown="doDrag('imDrag')" id="theSpace" style="width: 150px; height: 200px;"></div>
		<a href="javascript:void(0);" id="imSize" class="wht bld" style="background: #000;" onmouseup="stopDrag()" onmousedown="doDrag('imDrag', 1)">STORLEK»»</a>
	</div>
	<div id="imDiv" style="z-index: 1; visibility: visible; top: 0; left: 0; width: 330px;"><img src="<?=$img?>" id="theImage" id="theImage" onload="checkSrc(this);" onerror="imgError(this);" style="display: none;" alt=""></div>
	</div>
<?
	if($ua) {
?>
	<input name="ActImageW" id="ActImageW" type="hidden" value="150">
	<input name="ActImageH" id="ActImageH" type="hidden" value="200">
	<input name="UserImageX" id="UserImageX" type="hidden" value="0">
	<input name="UserImageY" id="UserImageY" type="hidden" value="0">
	<input name="UserImageW" id="UserImageW" type="hidden" value="150">
	<input name="UserImageH" id="UserImageH" type="hidden" value="200">
<?
	}
?>
	<img src="<?=$img?>" id="theSize" name="theSize" style="visibility: hidden; position: absolute;" onload="checkSize(this);">
</div>
<?=($intern)?'<script type="text/javascript">initiate(); document.getElementById(\'submitbtn\').disabled = true;</script>':'<script type="text/javascript">shutdown(); '.(($ua)?'document.getElementById(\'submitbtn\').disabled = true;':'').'</script>';?>
<?
		}
?>
	</div>
</div>

<?
	include(DESIGN.'foot.php');
?>