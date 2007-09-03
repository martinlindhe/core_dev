<?
session_start();
ob_start();
    ob_implicit_flush(0);
    ob_start('ob_gzhandler');
	ini_set("max_execution_time", 0);
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	require("./set_tmb.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	if(!$isCrew) errorNEW('Ingen behörighet.');
	$page = 'ANNONSER';
	$menu = $menu_NEWS;
	$sql = &new sql();
	$change = false;
	$types = array('pic', 'swf', 'event');

	$status_id = '1';
	if(!empty($_GET['status']) && is_numeric($_GET['status'])) {
		$status_id = $_GET['status'];
	} elseif($change) $status_id = $row['status_id'];

	if(!empty($_POST['donews']) && !empty($_POST['ins_head'])) {
		$status = (!empty($_POST['status_id']) && is_numeric($_POST['status_id']))?$_POST['status_id']:'0';
		$code = (!empty($_POST['code']) && $_POST['code'] == '1')?'event':'pic';
		if(!empty($_POST['code']) && $_POST['code'] == '2') $code = 'swf';
		if(!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$sql->queryUpdate("UPDATE {$t}ad SET
			ad_img = '".secureINS($_POST['ins_cmt'])."',
			ad_name = '".secureINS($_POST['ins_head'])."',
			ad_pos = '".secureINS($_POST['ins_pos'])."',
			city_id = '".secureINS(implode(',', $_POST['ins_city']))."',
			ad_target = '".secureINS($_POST['ins_target'])."',
			status_id = '$status',
			ad_url = '".secureINS($_POST['ins_url'])."',
			ad_size_x = '".@secureINS($_POST['ins_nW'])."',
			ad_size_y = '".@secureINS($_POST['ins_nH'])."',
			ad_showlimit = '".@secureINS($_POST['ins_showlimit'])."',
			ad_clicklimit = '".@secureINS($_POST['ins_clicklimit'])."',
			ad_url = '".secureINS($_POST['ins_url'])."',
			ad_start = '".secureINS($_POST['ins_start'])."',
			ad_stop = '".secureINS($_POST['ins_stop'])."',
			ad_type = '".$code."'
			WHERE main_id = '".secureINS($_POST['id'])."' LIMIT 1");
			$sql->queryUpdate("UPDATE {$t}ad SET ad_order = '0' WHERE ad_pos = '".$_POST['ins_pos']."'");
			$d_id = $_POST['id'];
		} else {
			$d_id = $sql->queryInsert("INSERT INTO {$t}ad SET
			ad_img = '".secureINS($_POST['ins_cmt'])."',
			city_id = '".secureINS(implode(',', $_POST['ins_city']))."',
			ad_name = '".secureINS($_POST['ins_head'])."',
			ad_start = '".secureINS($_POST['ins_start'])."',
			ad_pos = '".secureINS($_POST['ins_pos'])."',
			ad_target = '".secureINS($_POST['ins_target'])."',
			ad_url = '".secureINS($_POST['ins_url'])."',
			ad_size_x = '".@secureINS($_POST['ins_nW'])."',
			ad_size_y = '".@secureINS($_POST['ins_nH'])."',
			ad_showlimit = '".@secureINS($_POST['ins_showlimit'])."',
			ad_clicklimit = '".@secureINS($_POST['ins_clicklimit'])."',
			ad_stop = '".secureINS($_POST['ins_stop'])."',
			ad_type = '".$code."',
			status_id = '$status'");
			$sql->queryUpdate("UPDATE {$t}ad SET ad_order = '0' WHERE ad_pos = '".$_POST['ins_pos']."'");
		}

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

						if(move_uploaded_file($p, ADMIN_AD_DIR.$d_id.'_'.$unique.'.'.$p_name)) {
							$gotpic = true;
							if($code == 'swf') {
								$swf = gettxt('swf_ad');
								$swf_w = @$_POST['ins_nW'];
								$swf = str_replace('[w]', $swf_w, $swf);
								$swf_h = @$_POST['ins_nH'];
								$swf = str_replace('[h]', $swf_h, $swf);
								$swf_f = secureINS($d_id.'_'.$unique.'.'.$p_name);
								$swf = str_replace('[f]', AD_DIR.$swf_f, $swf);
								$sql->queryUpdate("UPDATE {$t}ad SET ad_img = '".$swf."' WHERE main_id = '".secureINS($d_id)."' LIMIT 1");
							} else {
								$sql->queryUpdate("UPDATE {$t}ad SET ad_img = '".secureINS($d_id.'_'.$unique.'.'.$p_name)."' WHERE main_id = '".secureINS($d_id)."' LIMIT 1");
							}
						} else {
							$msg = 'Felaktigt format, storlek eller bredd & höjd.';
							$js_mv = 'adver.php';
							require("./_tpl/notice_admin.php");
							exit;
						}
					/*} else {
						$msg = 'Felaktig bild.';
						$js_mv = 'adver.php';
						require("./_tpl/notice_admin.php");
						exit;
					}*/
				}
			}
		}
		if($gotpic) {
			header("Location: adver.php?id=$d_id&status=$status_id");
			exit;
		}

		header("Location: adver.php?status=$status_id");
		exit;
	}

	if(!empty($_POST['doupd'])) {
		foreach($_POST as $key => $val) {
			if(strpos($key, 'status_id') !== false) {
				$kid = explode(":", $key);
				$kid = $kid[1];
				if(isset($_POST['status_id:' . $kid])) {
					#$pos = explode(':', $_POST['order_id:' . $kid]);
					$sql->queryUpdate("UPDATE {$t}ad SET status_id = '".secureINS($_POST['status_id:' . $kid])."' WHERE main_id = '".secureINS($kid)."' LIMIT 1");
				}
			}
		}
		header("Location: adver.php?status=$status_id");
		exit;
	}
	$change = false;
	if(!empty($_GET['del']) && is_numeric($_GET['del'])) {
		$row = $sql->query("SELECT ad_img FROM {$t}ad WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		if(count($row) > 0) {
			@unlink(ADMIN_AD_DIR.$row[0][0]);
			$sql->queryUpdate("DELETE FROM {$t}ad WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
			
		}
		header("Location: adver.php?status=$status_id");
		exit;
	}

	if(!empty($_GET['del_pic']) && is_numeric($_GET['del_pic'])) {
		$row = $sql->queryLine("SELECT ad_img, ad_type FROM {$t}ad WHERE main_id = '".secureINS($_GET['del_pic'])."' LIMIT 1", 1);
		if(!empty($row['ad_img'])) {
			if($row['ad_type'] == 'pic')
				@unlink(ADMIN_AD_DIR.$row['ad_img']);
			$sql->queryUpdate("UPDATE {$t}ad SET ad_img = '' WHERE main_id = '".secureINS($_GET['del_pic'])."' LIMIT 1");
		}
		header("Location: adver.php?id=".$_GET['del_pic']."&status=$status_id");
		exit;
	}

	if(!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$row = $sql->queryAssoc("SELECT * FROM {$t}ad WHERE main_id = '".secureINS($_GET['id'])."' LIMIT 1");
		if(!count($row)) {
			$change = false;
		} else {
			$change = true;
		}
	}

			$view_arr = array(
				"1" => $sql->queryResult("SELECT COUNT(*) as count FROM {$t}ad WHERE ad_start < NOW() AND ad_stop > NOW() AND status_id = '1'"),
				"2" => $sql->queryResult("SELECT COUNT(*) as count FROM {$t}ad WHERE status_id = '2'"));

	if($status_id != '2') {
		$lpsdl = $sql->query("SELECT ad_img, ad_url, ad_type, main_id, ad_name, ad_start, ad_stop, status_id, ad_pos, ad_order, ad_show, ad_click, ad_tclick, city_id FROM {$t}ad WHERE status_id = '1' ORDER BY ad_pos ASC, ad_order ASC");
	} else {
		$lpsdl = $sql->query("SELECT ad_img, ad_url, ad_type, main_id, ad_name, ad_start, ad_stop, status_id, ad_pos, ad_order, ad_show, ad_click, ad_tclick, city_id FROM {$t}ad WHERE status_id = '2' ORDER BY ad_start ASC");
	}

#	$listmv = mysql_query("SELECT a.m_id, b.p_date, b.p_dday, b.p_name FROM {$tab['movie']} a, $topic_tab b WHERE a.status_id = '1' AND a.topic_id = b.main_id AND b.status_id = '1' ORDER BY b.p_date DESC");
	require("./_tpl/admin_head.php");
?>
	<script type="text/javascript" src="fnc_adm.js"></script>
	<script type="text/javascript" src="flashcreate.js"></script>
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
	if(val == '2') {
		document.getElementById('swf_size').style.display = '';
	} else {
		document.getElementById('swf_size').style.display = 'none';
	}
}
function loadtop() {
	if(parent.head)
	parent.head.show_active('info');
}
<?=(isset($_GET['t']))?'loadtop();':'';?>
	</script>
	<table height="100%">
	<tr><td colspan="2" height="25"><?makeMenuAdmin($page, $menu, 0);?></td></tr>
	<tr>
		<td width="50%" style="padding: 0 10px 0 0;">
			<form name="news" method="post" action="./adver.php?status=<?=$status_id?>" ENCTYPE="multipart/form-data">
			<input type="hidden" name="donews" value="1">
<?=($change)?'<input type="hidden" name="id" value="'.$row['main_id'].'">':'';?>
			<input type="hidden" name="status_id" id="status_id:X" value="<?=($change)?$row['status_id']:'2';?>">
			<table width="100%">
			<tr>
				<td height="35">Namn<br><input type="text" name="ins_head" class="inp_nrm" style="width: 270px;" tabindex="1" value="<?=($change)?secureOUT($row['ad_name']):'';?>" /><img src="./_img/status_<?=($change && $row['status_id'] == '1')?'green':'none';?>.gif" style="margin: 0 1px 2px 10px;" id="1:X" onclick="changeStatus('status', this.id);"><img src="./_img/status_<?=(($change && $row['status_id'] != '1') || !$change)?'red':'none';?>.gif" style="margin: 0 0 2px 1px;" id="2:X" onclick="changeStatus('status', this.id);"></td>
				<td align="right" style="padding: 10px 0 0 0; width: 80px;"><input type="submit" class="inp_realbtn" tabindex="3" value="Uppdatera" style="width: 80px; margin: 5px 0 0 10px;"></td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 5px 0 0 0;">Position:<br><select name="ins_pos" style="width: 167px;">
<?
	$positions = getEnumOptions($tab['ad'], 'ad_pos');
	sort($positions);
	#$positions = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
	foreach($positions as $p) {
		$p = strtoupper($p);
		echo '<option value="'.$p.'"'.(($change && strtoupper($row['ad_pos']) == $p)?' selected':'').'>'.$p.'</option>';
	}
?>
				</select>&nbsp;&nbsp;&nbsp;<a href="../reklamposition.pdf">LADDA HEM POSITIONSDOKUMENT</a><br />
Stad<br /><select name="ins_city[]" size="6" multiple=1 class="inp_nrm" style="height: 56px; width: 180px;">
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
				<td colspan="2" style="padding: 5px 0 0 0;">Målfönster:<br><select name="ins_target" style="width: 127px;">
<option value="_blank"<?=($change && $row['ad_target'] == '_blank')?' selected':'';?>>NYTT FÖNSTER</option>
<option value="commain"<?=($change && $row['ad_target'] == 'commain')?' selected':'';?>>HUVUDFÖNSTER</option>
				</select></td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 5px 0 0 0;">Start:<br><input type="text" style="width: 110px;" onfocus="this.select();" class="inp_nrm" name="ins_start" value="<?=($change)?plainDate($row['ad_start'], 0):plainDate(date("Y-m-d H:i:s"), 0);?>"><!-- - <a href="#">Idag</a>--></td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 5px 0 0 0;">Stopp:<br><input type="text" class="inp_nrm" style="width: 110px;" onfocus="this.select();" name="ins_stop" value="<?=($change)?plainDate($row['ad_stop'], 0):plainDate(date("Y-m-d H:i:s", strtotime('+7 DAYS')), 0);?>"><!-- - <a href="#">Start + 1 vecka</a>--></td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 5px 0 0 0;">Max antal visningar (0 = obegränsat):<br><input type="text" class="inp_nrm" style="width: 110px;" onfocus="this.select();" name="ins_showlimit" value="<?=($change)?$row['ad_showlimit']:'0';?>"></td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 5px 0 0 0;">Max antal klick (0 = obegränsat):<br><input type="text" class="inp_nrm" style="width: 110px;" onfocus="this.select();" name="ins_clicklimit" value="<?=($change)?$row['ad_clicklimit']:'0';?>"></td>
			</tr>
<?=($change && $row['ad_type'] != 'pic')?'
			<tr>
				<td colspan="2"><label>För att logga statistik för objektet, använd denna länk innuti objektet, och skriv slutdestination för länken i fältet <b>Länk</b> här under.</label><p>'.HOST.'click.php?id='.$row['main_id'].'</p></td>
			</tr>':'';?></td>
			<tr>
				<td colspan="2" style="padding-top: 5px;">Länk<br>
<input type="text" name="ins_url" id="ins_url" class="inp_nrm" style="width: 270px; margin-bottom: 4px;" value="<?=($change)?secureOUT($row['ad_url']):'';?>"><br>
<select style="width: 100%;" name="ins_lnk" onchange="if(this.value.length > 0) document.getElementById('ins_url').value = this.value;">
	<option value="">välj</option>
<?
/*
	echo '<optgroup label="Vimmel">';
	while($list_row = mysql_fetch_assoc($list)) {
		$selected = ($change && $row['ad_url'] == 'gallery_multi.php?id='.$list_row['main_id'])?' selected':'';
		echo '<option value="gallery_multi.php?id='.$list_row['main_id'].'"'.$selected.'>'.specialDate($list_row['p_date'], $list_row['p_dday']).'</option>';
	}
	echo '</optgroup>';
	echo '<optgroup label="Film">';
	while($list_row = mysql_fetch_assoc($listmv)) {
		$selected = ($change && $row['ad_url'] == 'gallery_movie.php?id='.$list_row['m_id'])?' selected':'';
		echo '<option value="gallery_movie.php?id='.$list_row['m_id'].'"'.$selected.'>'.specialDate($list_row['p_date'], $list_row['p_dday']).'</option>';
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
		print '<tr><td style="padding-bottom: 10px;">'.(($row['ad_type'] == 'pic')?'<img src="'.AD_DIR.$row['ad_img'].'" alt="'.strtoupper($row['ad_name']).'">':safeOUT($row['ad_img'])).'<br><a href="adver.php?del_pic='.$row['main_id'].'&status='.$status_id.'">RADERA</a></td></tr>';
	}

	$i = 1;
?>
			<tr>
				<td><?=($change && !empty($row['ad_img']))?'Skriv över aktuell fil':'Ladda upp fil';?><br><div style="float: left; margin-top: 1px; height: 22px; width: 24px;"><img src="./_img/status_none.gif" id="photopre<?=$i?>" onmouseoout="showSml(this)" onerror="showError(this);" name="photopre<?=$i?>" style="height: 22px; width: 24px;" alt=""></div><input type="file" name="file:<?=$i?>" id="photo<?=$i?>" class="inp_nrm" size="26" style="width: 180px;" dir="rtl" onchange="showPre(this.value, 'photopre<?=$i?>');" onclick="showPre(this.value, 'photopre<?=$i?>');"></td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 5px 0 0 0;">Data: (Lämna ifred om du inte vet vad du gör)<br><textarea name="ins_cmt" wrap="off" class="inp_nrm" tabindex="2" style="width: 400px; overflow: scroll; height: <?=($change && $row['ad_type'] != 'pic')?'130':'60';?>px;"><?=($change)?secureOUT($row['ad_img']):'';?></textarea></td>
			</tr>
			<tr id="swf_size"<?=($change && $row['ad_type'] == 'swf')?'':' style="display: none;"';?>>
				<td colspan="2" style="padding: 5px 0 0 0;"><b>Ange ny storlek</b> (Fungerar bara och måste anges om du laddar upp en ny fil)<br>Bredd i pixlar:<br><input type="text" class="inp_nrm" style="width: 100px;" onfocus="this.select();" name="ins_nW"><br>Höjd i pixlar:<br><input type="text" class="inp_nrm" style="width: 100px;" onfocus="this.select();" name="ins_nH"></td>
			</tr>
			<tr>
				<td colspan="2"><select name="code" value="1" class="inp_nrm" id="inp_c" onchange="checkIf(this.value);">
<option value="0"<?=($change && $row['ad_type'] == 'pic')?' selected':'';?>>PIC</option>
<option value="2"<?=($change && $row['ad_type'] == 'swf')?' selected':'';?>>SWF</option>
<option value="1"<?=($change && $row['ad_type'] == 'event')?' selected':'';?>>EVENT</option>
				</td>
			</tr>
			</table>
			<input type="submit" class="inp_realbtn" tabindex="3" value="Uppdatera" style="float: right; width: 80px; margin: 5px 0 0 10px;">
			</form>
		</td>
		<td style="padding: 0 0 0 10px; background: url('_img/brd_h.gif'); background-repeat: repeat-y;">
					<form action="adver.php" method="post">
					<input type="hidden" name="doupd" value="1">
			<input type="radio" class="inp_chk" value="1" id="view_1" onclick="document.location.href = 'adver.php?status=' + this.value;"<?=($status_id == '1')?' checked':'';?>><label for="view_1" class="txt_bld txt_look">Aktiva</label> [<?=$view_arr[1]?>]
			<input type="radio" class="inp_chk" value="2" id="view_2" onclick="document.location.href = 'adver.php?status=' + this.value;"<?=($status_id == '2')?' checked':'';?>><label for="view_2" class="txt_bld txt_look">Inaktiva</label> [<?=$view_arr[2]?>]
					<br><input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 11px 2px 0 0;">
					<table style="margin: 5px 0 10px 0; width: 660px;">
<?
	$nl = true;
	$ol = 0;
	$old = '';
	$matches = array('./', '<input type="image"', '<form', '</form', '[BILD]', '[FREDAG]', '[FREDAG-DATUM]');
	foreach($lpsdl as $row) {
		if(!empty($row[0])) $gotpic = true; else $gotpic = false;
		#$row[0] = str_replace("[CELL]", $s->info[3], $row[0]);
		echo '<input type="hidden" name="status_id:'.$row[3].'" id="status_id:'.$row[3].'" value="'.$row[7].'">';
		echo '<tr class="bg_gray">
			<td style="width: 80px; padding: 0 0 0 4px;" class="nobr"><img src="./_img/status_'.(($row[7] == '1')?'green':'none').'.gif" style="margin: 4px 1px 0 0;" id="1:'.$row[3].'" onclick="changeStatus(\'status\', this.id);"><img src="./_img/status_'.(($row[7] == '2')?'red':'none').'.gif" style="margin: 4px 0 0 1px;" id="2:'.$row[3].'" onclick="changeStatus(\'status\', this.id);"> <input type="text" name="order_id:'.$row[3].'" value="'.$row[8].':'.$row[9].'" disabled style="width: 24px; padding: 0; margin-bottom: 4px; line-height: 9px; height: 11px; size: 10px;" onfocus="this.select();" class="inp_nrm"></td>
			<td class="cur nobr" onclick="document.location.href = \'adver.php?status='.$status_id.'&id='.$row[3].'\';"'.(($gotpic)?' onmouseover="document.getElementById(\'tr:'.$row[3].'\').style.display = \'\';" onmouseout="document.getElementById(\'tr:'.$row[3].'\').style.display = \'none\';"':'').' style="width: 350px; padding: 4px;">'.$row[13].' <b>'.$row[4].'</b> (#'.$row[3].')</td>
			<td class="cur" onclick="document.location.href = \'adver.php?status='.$status_id.'&id='.$row[3].'\';" style="padding: 4px;">'.$row[2].'</td>
			<td class="cur nobr" onclick="document.location.href = \'adver.php?status='.$status_id.'&id='.$row[3].'\';" style="padding: 4px;">[ '.$row[10].' visningar | '.$row[11].' ('.$row[12].') besök ]</td>
			<td class="cur nobr" onclick="document.location.href = \'adver.php?status='.$status_id.'&id='.$row[3].'\';" style="padding: 4px;">'.((strtotime($row[5]) <= time() && strtotime($row[6]) >= time())?plainDate($row[5], 0).' '.plainDate($row[6], 0):'<strike>'.plainDate($row[5], 0).' '.plainDate($row[6], 0).'</strike>').'</td>
			<td class="cur nobr" style="padding: 4px;" align="right"><a href="adver.php?status='.$status_id.'&id='.$row[3].'">ÄNDRA</a> | <a href="adver.php?del='.$row[3].'" onclick="return confirm(\'Säker ?\');">RADERA</a></td>
		</tr>';
		if($gotpic) echo '<tr id="tr:'.$row[3].'" style="display: none;"><td colspan="6">'.(($row[2] == 'swf' || $row[2] == 'event')?stripslashes($row[0]):'<img src="'.AD_DIR.$row[0].'">').'</td></tr>';
	}
?>
					</table>
					</form>
		</td>
	</tr>
	</table>
</body>
</html>