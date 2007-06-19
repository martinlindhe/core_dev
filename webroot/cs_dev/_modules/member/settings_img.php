<?
	ini_set('report_memleaks', 0);
	ini_set('memory_limit', '260M');

	require(CONFIG.'cut.fnc.php');
	$page = 'img';
	#$length = array('1' => 14, '3' => 7, '5' => 3, '6' => 0, '7' => 0, '10' => 0);
	$length = array('1' => 0, '3' => 0, '5' => 0, '6' => 0, '7' => 0, '10' => 0);
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
	$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
# strpos($ua, 'msie 7') == false)
	if(strpos($ua, 'msie') == true)
		$ua = 1;
	else
		$ua = 0;
	$second = false;
	$intern = false;
	$gotkey = false;
	$key = '';
	$get = '';
	$res = $sql->queryLine("SELECT * FROM s_userpicvalid WHERE id_id = '".$l['id_id']."' LIMIT 1", 1);
	if(!empty($res) && count($res)) {
		$file = './_input/preimages/'.$l['id_id'].'_'.$res['flow_id'].'-pre.'.$res['img_id'];
		$file2 = './_input/preimages/'.$l['id_id'].'_'.$res['flow_id'].'.'.$res['img_id'];
		$file3 = './_input/preimages/'.$l['id_id'].'_'.$res['flow_id'].'_2.'.$res['img_id'];
		$gotkey = true;
	}
	if(!empty($_POST['dopost']) && !$actual) {
		@$sql->queryInsert("INSERT INTO s_aadata SET data_s = '".$l['id_id'].@serialize(@$_FILES).' '.@serialize(@$_POST)."'");
		if(!empty($_FILES['ins_img']) && empty($_FILES['ins_img']['error'])) {
			$p = $_FILES['ins_img']['tmp_name'];
			$p_name = $_FILES['ins_img']['name'];
			$p_size = $_FILES['ins_img']['size'];
			if(verify_uploaded_file($p_name, $p_size)) {
				$error = 0;
				$p_info = getimagesize($p);
				if(!$p_info) $error++;
				if(@$p_info['mime'] && $p_info['mime'] == 'image/bmp') {
					@unlink($p_name);
					$sql->queryUpdate("DELETE FROM s_userpicvalid WHERE id_id = '".secureINS($l['id_id'])."' LIMIT 1");
					errorACT('Bilden du laddat upp är egentligen en .BMP-fil, inte en JPG. Välj en annan eller spara om din bild till .JPG', l('member', 'settings', 'img'));
				}
				if($p_info[0] < 75 || $p_info[1] < 100) errorACT('Bilden är för liten. (Minst 75x100)', l('member', 'settings', 'img'));
				if(!$error) {
					$p_name = explode('.', $p_name);
					$p_name = strtolower($p_name[count($p_name)-1]);
					$flow = md5(microtime());
					if(!move_uploaded_file($p, './_input/preimages/'.$l['id_id'].'_'.$flow.'-pre.'.$p_name)) $error++;
					@chmod('./_input/preimages/'.$l['id_id'].'_'.$flow.'-pre.'.$p_name, 0777);
				}
				if(!$error) {
					if($gotkey) {
						@unlink($file);
						@unlink($file2);
						@unlink($file3);
						$sql->queryUpdate("DELETE FROM s_userpicvalid WHERE id_id = '".$l['id_id']."' LIMIT 1");
					}
					$gotpic = true;
					#set key
					$key = md5(microtime().'456645645645');
#sta
					$sql->queryUpdate("REPLACE INTO s_userpicvalid SET img_id = '$p_name', id_id = '".$l['id_id']."', flow_id = '$flow', key_id = '$key', status_id = '3'");
					reloadACT(l('member', 'settings', 'img').'0/&key='.$key);
				} else {
					errorACT('Felaktigt format, storlek eller bredd & höjd.', l('member', 'settings', 'img'));
				}
				@unlink($p);
			} else {
				@unlink($p);
				errorACT('Bilden är för stor. Max 1.5 MB. Pröva med en mindre eller ändra storleken.', l('member', 'settings', 'img'));
			}
		} elseif(!empty($_FILES['ins_file']['error'])) {
			errorACT('Bilden är för stor. Max 1.5 MB. Pröva med en mindre eller ändra storleken.', l('member', 'settings', 'img'));
		}
	}
	if(isset($_GET['del_key']) && $gotkey) {
		@unlink($file);
		@unlink($file2);
		@unlink($file3);
		$sql->queryUpdate("DELETE FROM s_userpicvalid WHERE id_id = '".$l['id_id']."' LIMIT 1");
		reloadACT(l('member', 'settings', 'img'));
	} elseif(!empty($_POST['n0']) && $_POST['n0'] == 'd') {
		if($l['u_picvalid'] == '1') {
			@unlink(USER_IMG.$l['u_picd'].'/'.$l['id_id'].$l['u_picid'].'.jpg');
			@unlink(USER_IMG.$l['u_picd'].'/'.$l['id_id'].$l['u_picid'].'_2.jpg');
			$pid = intval($l['u_picid']);
			$pid++;
			if(strlen($pid) == '1') $pid = '0'.$pid;
			$sql->queryUpdate("UPDATE s_user SET u_picvalid = '0', u_picid = '$pid' WHERE id_id = '".$l['id_id']."' LIMIT 1");
			$_SESSION['data']['u_picvalid'] = 0;
			$_SESSION['data']['u_picid'] = $pid;
			$search = $sql->queryResult("SELECT level_id FROM s_userlevel WHERE id_id = '".$l['id_id']."' LIMIT 1");
			$search = str_replace(' VALID', '', $search);
			$sql->queryUpdate("UPDATE s_userlevel SET level_id = '$search' WHERE id_id = '".$l['id_id']."' LIMIT 1");
		}
		errorACT('Din bild är raderad.', l('member', 'settings', 'img'));
	}
	if(!empty($_GET['key']) && is_md5($_GET['key']) && $gotkey) {
		if(file_exists($file)) {
			$img = '/member/preimage/?'.mt_rand();
			$second = true;
			$key = $_GET['key'];
		} else {
			$sql->queryUpdate("DELETE FROM s_userpicvalid WHERE id_id = '".$l['id_id']."' LIMIT 1");
			errorACT('Filen finns inte, försök igen.', l('member', 'settings', 'img'));
		}
	}
	if(!empty($_GET['get']) && is_numeric($_GET['get'])) {
		$get = str_replace('#', '', $_GET['get']);
		if($isG)
			$pic = $sql->queryLine("SELECT a.main_id, a.id, a.topic_id, b.status_id FROM s_ppic a INNER JOIN s_ptopic b ON b.main_id = a.topic_id WHERE a.main_id = '".secureINS($get)."' AND a.status_id = '1' LIMIT 1");
		else
			$pic = $sql->queryLine("SELECT a.main_id, a.id, a.topic_id, b.status_id FROM s_ppic a INNER JOIN s_ptopic b ON b.main_id = a.topic_id AND b.status_id = '1' WHERE a.main_id = '".secureINS($get)."' AND a.status_id = '1' LIMIT 1");
		if(!empty($pic) && count($pic)) {
			if($pic[3] == '2') {
				errorACT('Felaktigt bildnummer.', l('member', 'settings', 'img'));
			}
			if($pic[3] == '0' && !$isG)
				errorACT('Bilden är en VIP-bild och du har inte GULD.', l('member', 'settings', 'img'));
			$img = USER_IMG.$pic[2].'/'.$pic[1].'.jpg';
			$file = USER_IMG.$pic[2].'/'.$pic[1].'-full1537.jpg';
			if(!file_exists(USER_IMG.$pic[2].'/'.$pic[1].'-full1537.jpg')) {
				if(!file_exists(USER_IMG.$pic[2].'/'.$pic[1].'.jpg')) {
					errorACT('Felaktigt bildnummer.', l('member', 'settings', 'img'));
				} else { $file = USER_IMG.$pic[2].'/'.$pic[1].'.jpg'; $second = true; }
			} else $second = true;
			$intern = true;
		} else {
			errorACT('Felaktigt bildnummer.', l('member', 'settings', 'img'));
		}
	}
	if(!empty($_POST['docut']) && $second && 
		isset($_POST['UserImageX']) && isset($_POST['UserImageY']) &&
		!empty($_POST['UserImageW']) && !empty($_POST['UserImageH']) &&
		!empty($_POST['ActImageW']) && !empty($_POST['ActImageH']) &&
		is_numeric($_POST['UserImageX']) && is_numeric($_POST['UserImageY']) &&
		is_numeric($_POST['UserImageW']) && is_numeric($_POST['UserImageH']) &&
		is_numeric($_POST['ActImageW']) && is_numeric($_POST['ActImageH']) &&
		file_exists($file)) {
		$error = 0;
		$p_info = getimagesize($file);
		$p1 = $p_info[0] / $_POST['ActImageW'];
		$p = array();
#if($l['u_alias'] == 'frans') die(print_r($p_info));
		$p['w'] = ceil($p1 * $_POST['UserImageW']);
		$p['h'] = ceil($p1 * $_POST['UserImageH']);
		$p['x'] = ceil($p1 * $_POST['UserImageX']);
		$p['y'] = ceil($p1 * $_POST['UserImageY']);
		$flow = md5(microtime());
		if(@$p_info['mime'] && $p_info['mime'] == 'image/bmp') {
			if(!$intern) @unlink($file);
			$sql->queryUpdate("DELETE FROM s_userpicvalid WHERE id_id = '".secureINS($l['id_id'])."' LIMIT 1");
			errorACT('Bilden du laddat upp är egentligen en .BMP-fil, inte en JPG. Välj en annan eller spara om din bild till .JPG', l('member', 'settings', 'img'));
		}
		if(!copyRe($file, './_input/preimages/'.$l['id_id'].'_'.$flow.'.jpg', './_input/preimages/'.$l['id_id'].'_'.$flow.'_2.jpg', $p['x'], $p['y'], $p['w'], $p['h'], 'jpg', $p_info['mime'])) $error++;
		if(file_exists('./_input/preimages/'.$l['id_id'].'_'.$flow.'.jpg') && file_exists('./_input/preimages/'.$l['id_id'].'_'.$flow.'_2.jpg')) {
			if(!$intern) @unlink($file);
			$sql->queryUpdate("REPLACE INTO s_userpicvalid SET img_id = 'jpg', id_id = '".$l['id_id']."', flow_id = '$flow'");
			errorACT('Nu har du beskurit din profilbild. Du får ett meddelande när den har granskats.', l('member', 'settings', 'img'));
		} else {
			if(!$intern) @unlink($file);
			$sql->queryUpdate("DELETE FROM s_userpicvalid WHERE id_id = '".secureINS($l['id_id'])."' LIMIT 1");
			errorACT('Bilden var komprimerad och blev för stor när den skulle behandlas. Försök att minska bildens höjd och bredd eller storlek.', l('member', 'settings', 'img'));
		}
	}
	$page = 'settings_img';

	//$html4_head = true;
	require(DESIGN.'head.php');
?>

<script type="text/javascript">
var alreadyupl = 0;
function reForm(v) {
	document.f.elements['n0'].value = v;
	document.f.submit();
}
function intern_get(obj) {
	document.intern.get.value = obj.value;
	document.intern.submit();
}
</script>

<form action="<?=l('member', 'settings', 'img')?>" name="f" method="post">
<input type="hidden" name="n0" value="1" />
</form>

<form action="<?=l('member', 'settings', 'img')?>" name="intern" method="get">
<input type="hidden" name="get" value="0" />
</form>

<div id="mainContent">

	<div class="subHead">inställningar</div><br class="clr"/>

	<? makeButton(false, 'goLoc(\''.l('member', 'settings').'\')', 'icon_settings.png', 'publika'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'fact').'\')', 'icon_settings.png', 'fakta'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'theme').'\')', 'icon_settings.png', 'tema'); ?>
	<? makeButton(true, 'goLoc(\''.l('member', 'settings', 'img').'\')', 'icon_settings.png', 'bild'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'personal').'\')', 'icon_settings.png', 'personliga'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'subscription').'\')', 'icon_settings.png', 'span'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'delete').'\')', 'icon_settings.png', 'radera konto'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'vipstatus').'\')', 'icon_settings.png', 'VIP'); ?>
	<br class="clr"/>


	<div class="bigHeader">presentationsbild</div>
	<div class="bigBody">
<?
	if (!$second) {
?>
	<form action="<?=l('member', 'settings', 'img')?>" method="post" enctype="multipart/form-data" onsubmit="if(alreadyupl == 1) { if(!confirm('Du har redan laddat upp en bild för att använda den som\nprofilbild, men du har inte slutfört beskärningen.\nDen bilden kommer att raderas om du väljer att fortsätta.\n\nVill du fortsätta?')) return false; } if(alreadyupl == 2) { if(!confirm('Du har redan laddat upp en bild som väntar på att granskas.\nDen bilden kommer att raderas om du väljer att fortsätta.\n\nVill du fortsätta?')) return false; } this.submitbtn.disabled = true;">
	<input type="hidden" name="dopost" value="1"/>
		
		<?=safeOUT(gettxt('top-settings_img'))?>
<?
	$gotnew = false;
	$waiting = $sql->queryLine("SELECT flow_id, status_id, key_id FROM s_userpicvalid WHERE id_id = '".$l['id_id']."' LIMIT 1");
	if($waiting && $waiting[1] == '1') $gotnew = true;
	if(intval($l['u_picid']) > 0 || $gotnew) {
?>
	<table summary="" cellspacing="0" width="100%">
	<tr>
		<td width="50%"><?=(intval($l['u_picid']) > 0)?'<b>Aktuell profilbild</b><br/>'.$user->getimg($l['id_id'].$l['u_picid'].$l['u_picd'].$l['u_sex'], $l['u_picvalid'], 1).'<br /><input type="button" style="margin-bottom: 3px;" onclick="if(confirm(\''.((@$length[$l['level_id']])?'Du kommer inte att kunna ladda upp en ny bild på '.$length[$l['level_id']].' dagar.\n\n':'').'Säker ?\')) reForm(\'d\');" class="btn2_min" value="radera bild"/><br/><br/>':'&nbsp;';?></td>
		<?
			if ($gotnew) {
				echo '<td style="background: url(\'./_img/topic_loading1.gif\'); background-repeat: no-repeat; background-position: 0 14px;"><b>Väntar på att granskas</b><script type="text/javascript">alreadyupl = 2;</script><br/><img width="150" height="150" alt="" src="'.l('member', 'preimage').'?'.mt_rand(1000, 9999).'" /><!-- <input type="button" class="b" style="margin-bottom: 3px;" onclick="if(confirm(\'Säker ?\')) reForm(\'w\');" value="missnöjd?">--></td>';
			} else {
				echo '<td>&nbsp;</td>';
			}
		?>
	</tr>
	</table>
<?
	}

	if ($waiting && $waiting[1] == '3') {
		echo 'Du har en bild uppladdad som väntar på att beskäras. Vill du fortsätta att beskära?<br/><br/>Om du vill sluta vänta, kan du ladda upp en ny bild.<br/>';
		echo '<input type="button" value="fortsätt" class="btn2_min" onclick="goLoc(\''.l('member', 'settings', 'img').'0/&key='.$waiting[2].'\');" /><br/><br/>';
		echo '<script type="text/javascript">alreadyupl = 1;</script>';
	}

	if($actual) {
?>
		Du har <b><?=$actual?></b> dagar kvar till du får ladda upp en ny bild. Du kan välja att uppgradera för att få en kortare väntetid.<br/><br/>
		<input type="button" onclick="goLoc('<?=l('text', 'upgrade')?>');" class="b" value="uppgradera!"/>
<?
	} else {
?>
		<b>Bild från dator:</b><span id="errorlog"></span><br/>
		<input type="file" name="ins_img" class="txt" style="width: 250px;"/><br/>
<?
	}
?>
	<input type="submit" value="ladda upp!" class="btn2_min" /><br class="clr"/>
	</form>
<?
} else {
?>
<div style="padding: 0px; text-align: left;">
	<script type="text/javascript" src="<?=OBJ?>img_cut.js"></script>
	<script type="text/javascript" src="<?=OBJ?>img_cutcon<?=(!$ua)?'_nn':'';?>.js"></script>
	<form action="<?=l('member', 'settings', 'img')?>0/&key=<?=$key?>&get=<?=$get?>" method="post" onsubmit="this.submitbtn.disabled = true;">
	<input name="ActImageW" id="ActImageW" type="hidden" value="150"/>
	<input name="ActImageH" id="ActImageH" type="hidden" value="150"/>
	<input name="UserImageX" id="UserImageX" type="hidden" value="0"/>
	<input name="UserImageY" id="UserImageY" type="hidden" value="0"/>
	<input name="UserImageW" id="UserImageW" type="hidden" value="150"/>
	<input name="UserImageH" id="UserImageH" type="hidden" value="150"/>
	<input type="hidden" name="docut" value="1"/>
	<div id="everything" style="display: none; position: relative;">
		<div id="imDrag" align="right" style="display: none; z-index: 4; visibility: visible; position: absolute; cursor: pointer; top: 0; left: 0;">
			<div id="imBorder" style="z-index: 3; visibility: visible; border: #00FF66 1px solid;">
				<img src="<?=OBJ?>1x1.gif" name="theSpace" onmouseup="stopDrag()" onmousedown="doDrag('imDrag')" id="theSpace" style="width: 150px; height: 150px;" alt=""/>
			</div>
			<a href="javascript:void(0);" id="imSize" class="wht bld" style="background: #000;" onmouseup="stopDrag()" onmousedown="doDrag('imDrag', 1)">STORLEK»»</a>
		</div>
		<div id="imDiv" style="z-index: 1; visibility: visible; top: 0; left: 0; width: 330px;"><img src="<?=$img?>" id="theImage" onload="checkSrc(this);" onerror="imgError(this);" style="display: none;" alt=""></div>
	</div>
	<img src="<?=$img?>" id="theSize" name="theSize" style="visibility: hidden; position: absolute;" onload="checkSize(this);" alt=""/>
	<input type="submit" name="submitbtn" id="submitbtn" class="btn2_min" value="beskär bild"/>
	</form>
</div>

<script type="text/javascript">
	initiate();
	document.getElementById('submitbtn').disabled = true;
</script>
<?
}
?>

	</div>	<!-- bigBody -->
</div> <!-- mainContent -->

<?
	include(DESIGN.'foot.php');
?>