<?
	require_once('find_config.php');

	if (!$isCrew) errorNEW('Ingen behörighet.');

	$page = 'NYHETER';
	$menu = $menu_NEWS;
	$change = false;
	$types = array('0' => 'Startsidan', '1' => 'Spel', '2' => 'Pollbild', '3' => 'Splash', '5' => 'Splash-topp', '6' => 'Bonus');
	$status_id = '1';
	if(!empty($_GET['status']) && is_numeric($_GET['status'])) {
		$status_id = $_GET['status'];
	} elseif($change) $status_id = $row['status_id'];


	if(!empty($_POST['donews']) && !empty($_POST['ins_head'])) {

		$status = (!empty($_POST['status_id']) && is_numeric($_POST['status_id']))?$_POST['status_id']:'0';
		if(!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$sql->queryUpdate("UPDATE s_news SET
			ad_img = '".secureINS($_POST['ins_info'])."',
			ad_name = '".secureINS($_POST['ins_head'])."',
			status_id = '$status',
			ad_url = '".secureINS($_POST['ins_url'])."',
			ad_level = '".@secureINS($_POST['ins_level'])."',
			city_id = '".secureINS(@implode(',', $_POST['ins_city']))."',
			ad_start = '".secureINS($_POST['ins_start'])."',
			ad_stop = '".secureINS($_POST['ins_stop'])."',
			ad_hidden = '".@secureINS($_POST['ins_hidden'])."',
			ad_type = '".secureINS($_POST['ins_type'])."'
			WHERE main_id = '".secureINS($_POST['id'])."' LIMIT 1");
			$d_id = $_POST['id'];
		} else {
			$d_id = $sql->queryInsert("INSERT INTO s_news SET
			ad_img = '".secureINS($_POST['ins_info'])."',
			ad_url = '".secureINS($_POST['ins_url'])."',
			ad_name = '".secureINS($_POST['ins_head'])."',
			city_id = '".secureINS(@implode(',', $_POST['ins_city']))."',
			ad_start = '".secureINS($_POST['ins_start'])."',
			ad_stop = '".secureINS($_POST['ins_stop'])."',
			ad_hidden = '".@secureINS($_POST['ins_hidden'])."',
			ad_level = '".@secureINS($_POST['ins_level'])."',
			ad_type = '".secureINS($_POST['ins_type'])."',
			status_id = '$status'");
		}
		$n_id = $d_id;
		$gotpic = false;
		foreach($_FILES as $key => $val) {
			if(strpos($key, 'file') !== false) {
				$kid = explode(":", $key);
				$kid = $kid[1];
				if(!$_FILES['file:'. $kid]['error']) {
					$p = $_FILES['file:'. $kid]['tmp_name'];
					$p_name = $_FILES['file:'. $kid]['name'];
					$p_size = $_FILES['file:'. $kid]['size'];
					#if(verify_uploaded_file($p_name, $p_size)) {
						$unique = md5(microtime());
						$p_name = explode('.', $p_name);
						$p_name = $p_name[count($p_name)-1];
						$error = 0;
						if(move_uploaded_file($p, ADMIN_NEWS.$n_id.'_'.$unique.'.'.$p_name)) {
							$gotpic = true;
							if($_POST['ins_type'] == 'swf') {
								$swf = gettxt('swf');
								$swf_w = @$_POST['ins_nW'];
								$swf = str_replace('[w]', $swf_w, $swf);
								$swf_h = @$_POST['ins_nH'];
								$swf = str_replace('[h]', $swf_h, $swf);
								$swf_f = secureINS($n_id.'_'.$unique.'.'.$p_name);
								$swf = str_replace('[f]', ADMIN_NEWS.$swf_f, $swf);
								$sql->queryUpdate("UPDATE s_news SET ad_img = '".$swf."' WHERE main_id = '".$db->escape($d_id)."' LIMIT 1");
							} else {
								$sql->queryUpdate("UPDATE s_news SET ad_img = '".$db->escape($n_id.'_'.$unique.'.'.$p_name)."' WHERE main_id = '".$db->escape($d_id)."' LIMIT 1");
							}
						} else {
							$msg = 'Felaktigt format, storlek eller bredd & höjd.';
							$js_mv = 'news.php';
							require("./_tpl/notice_admin.php");
							exit;
						}
					/*} else {
						$msg = 'Felaktig bild.';
						$js_mv = 'news.php';
						require("./_tpl/notice_admin.php");
						exit;
					}*/
				}
			}
		}
/*
if(!empty($_POST['SPY'])) {
	$sql = &new sql();
	$user = &new user($sql);
	foreach($_POST['ins_city'] as $city) {
		$res = mysql_query("SELECT id_id FROM s_user WHERE status_id = '1' AND city_id = '".$city."'");
		while($row = mysql_fetch_row($res)) {
			$user->spy($row[0], 'NEWS', 'NEW', array($city));
		}
		$res = mysql_query("SELECT id_id FROM s_user WHERE status_id = '1' AND level_id = '10'");
		while($row = mysql_fetch_row($res)) {
			$user->spy($row[0], 'NEWS', 'NEW', array($city));
		}
	}
}
*/
		if($gotpic) {
			header("Location: news.php?id=$d_id&status=$status_id");
			exit;
		}

		header("Location: news.php?status=$status_id");
		exit;
	}

	if(!empty($_POST['doupd'])) {
		foreach($_POST as $key => $val) {
			if(strpos($key, 'status_id') !== false) {
				$kid = explode(":", $key);
				$kid = $kid[1];
				if(isset($_POST['status_id:' . $kid])) {
					$sql->queryUpdate("UPDATE s_news SET status_id = '".$db->escape($_POST['status_id:' . $kid])."', ad_pos = '".secureINS($_POST['order_id:' . $kid])."' WHERE main_id = '".secureINS($kid)."' LIMIT 1");
				}
			}
		}
		header("Location: news.php?status=$status_id");
		exit;
	}
	$change = false;
	if(!empty($_GET['del']) && is_numeric($_GET['del'])) {
		$row = $sql->query("SELECT ad_img FROM s_news WHERE main_id = '".$db->escape($_GET['del'])."' LIMIT 1");
		if(count($row) > 0) {
			@unlink(ADMIN_NEWS_DIR.$row[0][0]);
			$sql->queryUpdate("DELETE FROM s_news WHERE main_id = '".$db->escape($_GET['del'])."' LIMIT 1");
			
		}
		header("Location: news.php?status=$status_id");
		exit;
	}

	if(!empty($_GET['del_pic']) && is_numeric($_GET['del_pic'])) {
		$row = $sql->queryResult("SELECT ad_img FROM s_news WHERE main_id = '".$db->escape($_GET['del_pic'])."' LIMIT 1");
		if(!empty($row)) {
			@unlink(ADMIN_NEWS_DIR.$row);
			$sql->queryUpdate("UPDATE s_news SET ad_img = '' WHERE main_id = '".$db->escape($_GET['del_pic'])."' LIMIT 1");
		}
		header("Location: news.php?status=$status_id");
		exit;
	}

	if(!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$row = $db->getOneRow("SELECT * FROM s_news WHERE main_id = '".$db->escape($_GET['id'])."' LIMIT 1", 1);
		if(!count($row)) {
			$change = false;
		} else {
			$change = true;
		}
	}

			$view_arr = array(
				"1" => $db->getOneItem("SELECT COUNT(*) FROM s_news WHERE ad_start < NOW() AND ad_stop > NOW() AND status_id = '1'"),
				"2" => $db->getOneItem("SELECT COUNT(*) FROM s_news WHERE status_id = '2'"),
				"3" => $db->getOneItem("SELECT COUNT(*) FROM s_news WHERE (ad_start > NOW() OR ad_stop < NOW()) AND status_id = '1'"));

#	$list = $sql->query("SELECT main_id, p_date, p_dday, p_name FROM s_ptopic WHERE status_id = '1' ORDER BY p_date DESC", 0, 1);
	require('admin_head.php');
?>
	<script type="text/javascript" src="flashcreate.js"></script>
	<script type="text/javascript" src="fnc_adm.js"></script>
	<script type="text/javascript">
var allowedext = Array("jpg", "jpeg", "gif", "png");
function showError(obj) { obj.src = './_img/status_none.gif'; }
function showPre(val, id) {
	var picpre = document.getElementById(id);
	if(val != '') {
		var showimg = false;
		ext = val.split(".");
		ext = ext[ext.length - 1].toLowerCase();
		for(var i = 0; i <= allowedext.length; i++)
			if(allowedext[i] == ext) 
				showimg = true;
	
		if(showimg) {
			previewpic = val;
			picpre.src = 'file://' + val.replace(/\\/g,'/');
		} else
			showError(picpre);
	} else
			showError(picpre);
}
function checkIf(val) {
	if(val == 'swf') {
		document.getElementById('swf_size').style.display = '';
	} else {
		document.getElementById('swf_size').style.display = 'none';
	}
}
	</script>
	<table height="100%">
	<tr><td colspan="2" height="25" class="nobr"><?makeMenuAdmin($page, $menu, 0);?></td></tr>
	<tr>
		<td width="50%" style="padding: 0 10px 0 0;">
			<form name="news" method="post" action="./news.php?status=<?=$status_id?>" ENCTYPE="multipart/form-data">
			<input type="hidden" name="donews" value="1">
<?=($change)?'<input type="hidden" name="id" value="'.$row['main_id'].'">':'';?>
			<input type="hidden" name="status_id" id="status_id:X" value="<?=($change)?$row['status_id']:'2';?>">
			<table width="100%">
			<tr>
				<td height="35">Namn<br><input type="text" name="ins_head" class="inp_nrm" style="width: 270px;" tabindex="1" value="<?=($change)?secureOUT($row['ad_name']):'';?>" /><img src="./_img/status_<?=($change && $row['status_id'] == '1')?'green':'none';?>.gif" style="margin: 0 1px 2px 10px;" id="1:X" onclick="changeStatus('status', this.id);"><img src="./_img/status_<?=(($change && $row['status_id'] != '1') || !$change)?'red':'none';?>.gif" style="margin: 0 0 2px 1px;" id="2:X" onclick="changeStatus('status', this.id);"></td>
				<td align="right" style="padding: 10px 0 0 0; width: 80px;"><input type="submit" class="inp_realbtn" tabindex="3" value="Uppdatera" style="width: 80px; margin: 5px 0 0 10px;"></td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 5px 0 10px 0;"><input type="checkbox" class="inp_chk" name="SPY" value="1" id="spy_tell"><label for="spy_tell"> [BEVAKNING] Meddela om nyhet</label><br /><br />Position:<br><select class="inp_nrm" name="ins_level" style="width: 180px;">
<option value="0"<?=($change && !$row['ad_level'])?' selected':'';?>>Startsidan</option>
<option value="1"<?=($change && $row['ad_level'] == '1')?' selected':'';?>>Spel</option>
<!--<option value="2"<?=($change && $row['ad_level'] == '2')?' selected':'';?>>Pollbild (Poll-bilderna)</option>
<option value="5"<?=($change && $row['ad_level'] == '5')?' selected':'';?>>Splash-topp (Syns på allra första sidan, högst upp)</option>
<option value="3"<?=($change && $row['ad_level'] == '3')?' selected':'';?>>Splash (Syns på allra första sidan, i mitten)</option>
<option value="6"<?=($change && $row['ad_level'] == '6')?' selected':'';?>>Bonus</option>-->
</td>
			</tr>
			<tr>
				<td colspan="2">Stad<br /><select name="ins_city[]" size="6" multiple=1 class="inp_nrm" style="height: 56px; width: 180px;">
<?
	foreach($cities as $key => $val) {
		$select = ($change && strpos($row['city_id'], strval($key)) !== false)?' selected':'';
		echo '<option value="'.$key.'"'.$select.'>'.$val.'</option>';
	}
?>
			</select>
</td>
		</tr>
			<tr>
				<td colspan="2" style="padding: 5px 0 0 0;">Start:<br><input type="text" style="width: 110px;" onfocus="this.select();" class="inp_nrm" name="ins_start" value="<?=($change)?plainDate($row['ad_start'], 0):plainDate(date("Y-m-d H:i:s"), 0);?>"><!-- - <a href="#">Idag</a>--></td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 5px 0 0 0;">Stopp:<br><input type="text" class="inp_nrm" style="width: 110px;" onfocus="this.select();" name="ins_stop" value="<?=($change)?plainDate($row['ad_stop'], 0):plainDate(date("Y-m-d H:i:s", strtotime('+7 DAYS')), 0);?>"><!-- - <a href="#">Start + 1 vecka</a>--></td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 5px 0 0 0;">Data<textarea name="ins_info" class="inp_nrm" style="width: 100%; height: 250px;"><?=($change)?secureOUT($row['ad_img']):'';?></textarea></td>
			</tr>
			<tr>
				<td colspan="2"><select name="ins_type" value="1" class="inp_nrm" id="inp_c" onchange="checkIf(this.value);">
<option value="pic"<?=($change && $row['ad_type'] == 'pic')?' selected':'';?>>PIC</option>
<option value="swf"<?=($change && $row['ad_type'] == 'swf')?' selected':'';?>>SWF</option>
<option value="event"<?=($change && $row['ad_type'] == 'event')?' selected':'';?>>EVENT</option>
				</td>
			</tr>
			<tr id="swf_size"<?=($change && $row['ad_type'] == 'swf')?'':' style="display: none;"';?>>
				<td colspan="2" style="padding: 5px 0 0 0;"><b>Ange ny storlek</b> (Fungerar bara och måste anges om du laddar upp en ny fil)<br>Bredd i pixlar:<br><input type="text" class="inp_nrm" style="width: 100px;" onfocus="this.select();" name="ins_nW"><br>Höjd i pixlar:<br><input type="text" class="inp_nrm" style="width: 100px;" onfocus="this.select();" name="ins_nH"></td>
			</tr>
			<tr>
				<td colspan="2"><input type="checkbox" name="ins_hidden" value="1" class="inp_chk" id="inp_sa" style="margin: 0 0 -2px 0;"<?=(($change && $row['ad_hidden'] == '1'))?' checked':'';?>><label for="inp_sa">Dold.</label></td>
			</tr>
			<tr>
				<td colspan="2">
<br>Länk<br>
<input type="text" name="ins_url" id="ins_url" class="inp_nrm" style="width: 270px; margin-bottom: 4px;" value="<?=($change)?secureOUT($row['ad_url']):'';?>"><br>
<select style="width: 100%;" name="ins_lnk" onchange="if(this.value.length > 0) document.getElementById('ins_url').value = this.value;">
	<option value="">välj</option>
<?
/*	echo '<optgroup label="Vimmel">';
	foreach($list as $list_row) {
		$selected = ($change && $row['ad_url'] == 'gallery_multi.php?id='.$list_row['main_id'])?' selected':'';
		echo '<option value="gallery_multi.php?id='.$list_row['main_id'].'"'.$selected.'>'.specialDate($list_row['p_date'], $list_row['p_dday']).'</option>';
	}
	echo '</optgroup>';
*/
?>

</select>
				</td>
			</tr>
			</table>
			<table cellspacing="0" width="100%" style="margin: 10px 0 0 0;">
<?
	if($change && !empty($row['ad_img'])) {

		print '<tr><td style="padding-bottom: 10px;">'.(($row['ad_type'] == 'pic')?'<img src="'.ADMIN_NEWS.$row['ad_img'].'" alt="'.strtoupper($row['ad_name']).'">':str_replace('./', '../', safeOUT($row['ad_img']))).'<br><a href="news.php?del_pic='.$row['main_id'].'&status='.$status_id.'">RADERA</a></td></tr>';
	}

	$i = 1;
	if($change) $i = $i;
	for($i = $i; $i <= 1; $i++) {
?>
			<tr>
				<td><?=($change && file_exists(ADMIN_NEWS.$row['ad_img']) && is_file(ADMIN_NEWS.$row['ad_img']))?' Skriv över aktuell bild':'Ladda upp bild';?><br><div style="float: left; margin-top: 1px; height: 22px; width: 24px;"><img src="./_img/status_none.gif" id="photopre<?=$i?>" onmouseoout="showSml(this)" onerror="showError(this);" name="photopre<?=$i?>" style="height: 22px; width: 24px;" alt=""></div><input type="file" name="file:<?=$i?>" id="photo<?=$i?>" class="inp_nrm" size="26" style="width: 180px;" dir="rtl" onchange="showPre(this.value, 'photopre<?=$i?>');" onclick="showPre(this.value, 'photopre<?=$i?>');"></td>
			</tr>
<?
	}
?>
			<tr><td align="right"><input type="submit" class="inp_realbtn" tabindex="3" value="Uppdatera" style="width: 80px; margin: 5px 0 0 10px;"></td></tr>
			</table>
			</form>
		</td>
		<td style="padding: 0 0 0 10px; background: url('_img/brd_h.gif'); background-repeat: repeat-y;">
					<form action="news.php" method="post">
					<input type="hidden" name="doupd" value="1">
			<input type="radio" class="inp_chk" value="1" id="view_1" onclick="document.location.href = 'news.php?status=' + this.value;"<?=($status_id == '1')?' checked':'';?>><label for="view_1" class="txt_bld txt_look">Aktiva</label> [<?=$view_arr[1]?>]
			<input type="radio" class="inp_chk" value="3" id="view_3" onclick="document.location.href = 'news.php?status=' + this.value;"<?=($status_id == '3')?' checked':'';?>><label for="view_3" class="txt_bld txt_look">Kommande/Har varit</label> [<?=$view_arr[3]?>]
			<input type="radio" class="inp_chk" value="2" id="view_2" onclick="document.location.href = 'news.php?status=' + this.value;"<?=($status_id == '2')?' checked':'';?>><label for="view_2" class="txt_bld txt_look">Dolda</label> [<?=$view_arr[2]?>]
					<br><input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 11px 2px 0 0;">
					<table style="margin: 5px 0 10px 0; width: 660px;">
<?
	if($status_id == '2') {
		$lpsdl = $db->getArray("SELECT ad_img, ad_url, ad_type, main_id, ad_name, ad_start, ad_stop, status_id, ad_pos, ad_level, city_id FROM s_news WHERE status_id = '2' ORDER BY ad_level ASC, ad_start ASC");
	} elseif($status_id == '3') {
		$lpsdl = $db->getArray("SELECT ad_img, ad_url, ad_type, main_id, ad_name, ad_start, ad_stop, status_id, ad_pos, ad_level, city_id FROM s_news WHERE (ad_start > NOW() OR ad_stop < NOW()) AND status_id = '1' ORDER BY ad_level ASC, ad_pos ASC");
	} else {
		$lpsdl = $db->getArray("SELECT ad_img, ad_url, ad_type, main_id, ad_name, ad_start, ad_stop, status_id, ad_pos, ad_level, city_id FROM s_news WHERE status_id = '1' ORDER BY ad_level ASC, ad_pos ASC");
	}

	$nl = true;
	$ol = 0;
	$old = '';
	$matches = array('./', '<input type="image"', '<form', '</form', '[BILD]', '[FREDAG]', '[FREDAG-DATUM]');
	foreach($lpsdl as $row) {
		if ($status_id != '1' || ($status_id == '1' && strtotime($row['ad_start']) < time() && strtotime($row['ad_stop']) > time())) {
		if (!empty($row['ad_img'])) $gotpic = true; else $gotpic = false;
		echo '<input type="hidden" name="status_id:'.$row['main_id'].'" id="status_id:'.$row['main_id'].'" value="'.$row['status_id'].'">';
		echo '<tr class="bg_gray">
			<td style="width: 80px; padding: 0 0 0 4px;" class="nobr">
				<img src="./_img/status_'.(($row['status_id'] == '1')?'green':'none').'.gif" style="margin: 4px 1px 0 0;" id="1:'.$row['main_id'].'" onclick="changeStatus(\'status\', this.id);">
				<img src="./_img/status_'.(($row['status_id'] == '2')?'red':'none').'.gif" style="margin: 4px 0 0 1px;" id="2:'.$row['main_id'].'" onclick="changeStatus(\'status\', this.id);">
				<input type="text" name="order_id:'.$row['main_id'].'" value="'.$row['ad_pos'].'" style="width: 24px; padding: 0; margin-bottom: 4px; line-height: 9px; height: 11px; size: 10px;" onfocus="this.select();" maxlength="3" class="inp_nrm">
			</td>
			<td class="cur" onclick="document.location.href = \'news.php?id='.$row['main_id'].'&status='.$status_id.'\';"'.(($gotpic)?' onmouseover="document.getElementById(\'tr:'.$row['main_id'].'\').style.display = \'\';" onmouseout="document.getElementById(\'tr:'.$row['main_id'].'\').style.display = \'none\';"':'').' style="width: 350px; padding: 4px 4px 4px 0;">'.$row['city_id'].' <b>'.$row['ad_name'].'</b> (#'.$row['main_id'].')</td>
			<td style="padding: 4px;">'.$types[$row['ad_level']].(($row['ad_type'] == 'event')?' <a target="_blank" href="news_extract.php?id='.$row['main_id'].'">event</a>':'').'</td>
			<td style="padding: 4px;" class="nobr">'.plainDate($row['ad_start'], 0).'</td>
			<td style="padding: 4px;" class="nobr">'.plainDate($row['ad_stop'], 0).'</td>
			<td style="padding: 4px;" align="right" class="nobr"><a href="news.php?id='.$row['main_id'].'&status='.$status_id.'">ÄNDRA</a> | <a href="news.php?del='.$row['main_id'].'&status='.$status_id.'" onclick="return confirm(\'Säker ?\');">RADERA</a></td>
		</tr>';
		if($gotpic) echo '<tr id="tr:'.$row['main_id'].'" style="display: none;"><td colspan="6">'.(($row['ad_type'] == 'swf' || $row['ad_type'] == 'event')?stripslashes(str_replace("./", "../", $row['ad_img'])):'<img src="'.ADMIN_NEWS.$row['ad_img'].'">').'</td></tr>';
		}
	}
?>
					</table>
					</form>
		</td>
	</tr>
	</table>
</body>
</html>
