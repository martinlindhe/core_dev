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
	$change = false;
	$status_id = '1';
	if(!empty($_GET['status']) && is_numeric($_GET['status'])) {
		$status_id = $_GET['status'];
	} elseif($change) $status_id = $row['status_id'];
	$levels = array('0' => 'Snurran', '1' => 'Om SBN', '2' => 'Crew', '3' => 'Biljettförsäljare');


	if(!empty($_POST['donews']) && !empty($_POST['ins_name'])) {

		$status = (!empty($_POST['status_id']) && is_numeric($_POST['status_id']))?$_POST['status_id']:'0';
		if(!empty($_POST['id']) && is_numeric($_POST['id'])) {

			mysql_query("UPDATE {$tab['extra']} SET
			ad_name = '".secureINS($_POST['ins_name'])."',
			status_id = '$status',
			ad_cell = '".secureINS($_POST['ins_cell'])."',
			ad_pos = '".@secureINS($_POST['ins_pos'])."',
			ad_extra = '".@secureINS($_POST['ins_extra'])."',
			ad_email = '".secureINS($_POST['ins_email'])."'
			WHERE main_id = '".secureINS($_POST['id'])."' LIMIT 1");
			$d_id = $_POST['id'];
		} else {
			mysql_query("INSERT INTO {$tab['extra']} SET
			ad_name = '".secureINS($_POST['ins_name'])."',
			ad_cell = '".secureINS($_POST['ins_cell'])."',
			ad_pos = '".@secureINS($_POST['ins_pos'])."',
			ad_extra = '".@secureINS($_POST['ins_extra'])."',
			ad_email = '".secureINS($_POST['ins_email'])."',
			status_id = '$status'");
			$d_id = mysql_insert_id();
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
					if(verify_uploaded_file($p_name, $p_size)) {
						$unique = md5(microtime());
						$p_name = explode('.', $p_name);
						$p_name = $p_name[count($p_name)-1];
						$error = 0;

						if(move_uploaded_file($p, ADMIN_EXTRA_DIR.$d_id.'_'.$unique.'.'.$p_name)) {
							$gotpic = true;
							mysql_query("UPDATE {$tab['extra']} SET ad_img = '".secureINS($d_id.'_'.$unique.'.'.$p_name)."' WHERE main_id = '".secureINS($d_id)."' LIMIT 1");
						} else {
							$msg = 'Felaktigt format, storlek eller bredd & höjd.';
							$js_mv = 'extra.php';
							require("./_tpl/notice_admin.php");
							exit;
						}
					} else {
						$msg = 'Felaktig bild.';
						$js_mv = 'extra.php';
						require("./_tpl/notice_admin.php");
						exit;
					}
				}
			}
		}
		if($gotpic) {
			header("Location: extra.php?status=$status");
			exit;
		}

		header("Location: extra.php?status=$status");
		exit;
	}

	if(!empty($_POST['doupd'])) {
		foreach($_POST as $key => $val) {
			if(strpos($key, 'status_id') !== false) {
				$kid = explode(":", $key);
				$kid = $kid[1];
				if(isset($_POST['status_id:' . $kid])) {
					mysql_query("UPDATE {$tab['extra']} SET status_id = '".secureINS($_POST['status_id:' . $kid])."', order_id = '".secureINS($_POST['order_id:' . $kid])."' WHERE main_id = '".secureINS($kid)."' LIMIT 1");
				}
			}
		}
		header("Location: extra.php?status=$status_id");
		exit;
	}
	$change = false;
	if(!empty($_GET['del']) && is_numeric($_GET['del'])) {
		$sql = mysql_query("SELECT * FROM {$tab['extra']} WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		if(mysql_num_rows($sql) > 0) {
			$row = mysql_fetch_assoc($sql);
			@unlink(ADMIN_EXTRA_DIR.$row['ad_img']);
			mysql_query("DELETE FROM {$tab['extra']} WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
			
		}
		header("Location: extra.php?status=$status_id");
		exit;
	}

	if(!empty($_GET['del_pic']) && is_numeric($_GET['del_pic'])) {
		$sql = mysql_query("SELECT * FROM {$tab['extra']} WHERE main_id = '".secureINS($_GET['del_pic'])."' LIMIT 1");
		if(mysql_num_rows($sql) > 0) {
			$row = mysql_fetch_assoc($sql);
			@unlink(ADMIN_EXTRA_DIR.$row['ad_img']);
			mysql_query("UPDATE {$tab['extra']} SET ad_img = '' WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		}
		header("Location: extra.php?status=$status_id");
		exit;
	}

	if(!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$sql = mysql_query("SELECT * FROM {$tab['extra']} WHERE main_id = '".secureINS($_GET['id'])."' LIMIT 1");
		if(mysql_num_rows($sql) != '1') {
			$change = false;
		} else {
			$row = mysql_fetch_assoc($sql);
			$change = true;
		}
	}

			$view_arr = array(
				"1" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$tab['extra']} WHERE status_id = '1'"), 0, 'count'),
				"2" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$tab['extra']} WHERE status_id = '2'"), 0, 'count'));

	if($status_id != '2') {
		$extra = mysql_query("SELECT * FROM {$tab['extra']} WHERE status_id = '$status_id' ORDER BY ad_pos ASC, order_id ASC, main_id DESC");
	} else {
		$extra = mysql_query("SELECT * FROM {$tab['extra']} WHERE status_id = '2' ORDER BY ad_pos ASC, main_id DESC");
	}

	require("./_tpl/admin_head.php");
?>
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
	</script>
	<table height="100%">
	<tr><td height="25"><a href="extra.php">Personal</a></td></tr>
	<tr>
		<td width="50%" style="padding: 0 10px 0 0;">
			<form name="news" method="post" action="./extra.php?status=<?=$status_id?>" ENCTYPE="multipart/form-data">
			<input type="hidden" name="donews" value="1">
<?=($change)?'<input type="hidden" name="id" value="'.$row['main_id'].'">':'';?>
			<input type="hidden" name="status_id" id="status_id:X" value="<?=($change)?$row['status_id']:'2';?>">
			<table width="100%">
			<tr>
				<td height="35">Namn:<br><input type="text" name="ins_name" class="inp_nrm" style="width: 270px;" tabindex="1" value="<?=($change)?secureOUT($row['ad_name']):'';?>" /><img src="./_img/status_<?=($change && $row['status_id'] == '1')?'green':'none';?>.gif" style="margin: 0 1px 2px 10px;" id="1:X" onclick="changeStatus('status', this.id);"><img src="./_img/status_<?=(($change && $row['status_id'] != '1') || !$change)?'red':'none';?>.gif" style="margin: 0 0 2px 1px;" id="2:X" onclick="changeStatus('status', this.id);"></td>
				<td align="right" style="padding: 10px 0 0 0; width: 80px;"><input type="submit" class="inp_realbtn" tabindex="3" value="Uppdatera" style="width: 80px; margin: 5px 0 0 10px;"></td>
			</tr>
			<tr>
				<td colspan="2">Mobilnummer:<br><input type="text" name="ins_cell" class="inp_nrm" style="width: 270px;" value="<?=($change)?secureOUT($row['ad_cell']):'';?>" /></td>
			</tr>
			<tr>
				<td colspan="2">E-post:<br><input type="text" name="ins_email" class="inp_nrm" style="width: 270px;" value="<?=($change)?secureOUT($row['ad_email']):'';?>" /></td>
			</tr>
			<tr>
				<td colspan="2">Skola:<br><input type="text" name="ins_extra" class="inp_nrm" style="width: 270px;" value="<?=($change)?secureOUT($row['ad_extra']):'';?>" /></td>
			</tr>
			<tr>
				<td colspan="2">Arbetsposition:<br>
<select class="inp_nrm" name="ins_pos">
<option value="0"<?=($change && !$row['ad_pos'])?' selected':'';?>>Snurran (Listas i rullisten i Crew)</option>
<option value="1"<?=($change && $row['ad_pos'] == '1')?' selected':'';?>>Vi (Listas under Om SBN)</option>
<option value="2"<?=($change && $row['ad_pos'] == '2')?' selected':'';?>>Crew (Listas överst i Crew)</option>
<option value="3"<?=($change && $row['ad_pos'] == '3')?' selected':'';?>>Biljettförsäljare</option>
</select>
				</td>
			</tr>
			</table>
			<table cellspacing="0" width="100%" style="margin: 10px 0 0 0;">
<?
	if($change && file_exists(ADMIN_EXTRA_DIR.$row['ad_img']) && is_file(ADMIN_EXTRA_DIR.$row['ad_img'])) {
		print '<tr><td style="padding-bottom: 10px;"><img src="'.ADMIN_EXTRA_DIR.$row['ad_img'].'" alt="'.strtoupper($row['ad_head']).'"><br><a href="extra.php?del_pic='.$row['main_id'].'&status='.$status_id.'">RADERA</a></td></tr>';
	}

	$i = 1;
	if($change) $i = $i;
	for($i = $i; $i <= 1; $i++) {
?>
			<tr>
				<td><?=($change && file_exists(ADMIN_EXTRA_DIR.$row['ad_img']) && is_file(ADMIN_EXTRA_DIR.$row['ad_img']))?' Skriv över aktuell bild':'Ladda upp bild';?><br><div style="float: left; margin-top: 1px; height: 22px; width: 24px;"><img src="./_img/status_none.gif" id="photopre<?=$i?>" onmouseoout="showSml(this)" onerror="showError(this);" name="photopre<?=$i?>" style="height: 22px; width: 24px;" alt=""></div><input type="file" name="file:<?=$i?>" id="photo<?=$i?>" class="inp_nrm" size="26" style="width: 180px;" dir="rtl" onchange="showPre(this.value, 'photopre<?=$i?>');" onclick="showPre(this.value, 'photopre<?=$i?>');"></td>
			</tr>
<?
	}
?>
			</table>
			</form>
		</td>
		<td style="padding: 0 0 0 10px; background: url('_img/brd_h.gif'); background-repeat: repeat-y;">
					<form action="extra.php" method="post">
					<input type="hidden" name="doupd" value="1">
			<input type="radio" class="inp_chk" value="1" id="view_1" onclick="document.location.href = 'extra.php?status=' + this.value;"<?=($status_id == '1')?' checked':'';?>><label for="view_1" class="txt_bld txt_look">Exponerade</label> [<?=$view_arr[1]?>]
			<input type="radio" class="inp_chk" value="2" id="view_2" onclick="document.location.href = 'extra.php?status=' + this.value;"<?=($status_id == '2')?' checked':'';?>><label for="view_2" class="txt_bld txt_look">Dolda</label> [<?=$view_arr[2]?>]
					<br><input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 11px 2px 0 0;">
					<table style="margin: 5px 0 10px 0; width: 545px;">
<?
	$nl = true;
	$ol = 0;
	$old = 0;
	while($row = mysql_fetch_assoc($extra)) {
		if(!empty($row['ad_img']) && file_exists(ADMIN_EXTRA_DIR.$row['ad_img'])) $gotpic = true; else $gotpic = false;
		echo '<tr class="bg_gray">
			<td style="width: 40px; padding: 1px 5px 0 4px;" class="nobr"><input type="hidden" name="status_id:'.$row['main_id'].'" id="status_id:'.$row['main_id'].'" value="'.$row['status_id'].'"><img src="./_img/status_'.(($row['status_id'] == '1')?'green':'none').'.gif" style="margin: 4px 1px 0 0;" id="1:'.$row['main_id'].'" alt="Ivägskickad" onclick="changeStatus(\'status\', this.id);"><img src="./_img/status_'.(($row['status_id'] == '2')?'red':'none').'.gif" style="margin: 4px 0 0 1px;" id="2:'.$row['main_id'].'" onclick="changeStatus(\'status\', this.id);"><input type="text" name="order_id:'.$row['main_id'].'" value="'.$row['order_id'].'" style="width: 24px; padding: 0; margin-bottom: 2px; margin-left: 4px; line-height: 9px; height: 11px; size: 10px;" onfocus="this.select();" maxlength="3" class="inp_nrm"></td>
			<td class="pdg cur nobr" style="width: 350px;" onclick="document.location.href = \'extra.php?id='.$row['main_id'].'\';"'.(($gotpic)?' onmouseover="document.getElementById(\'tr:'.$row['main_id'].'\').style.display = \'\';" onmouseout="document.getElementById(\'tr:'.$row['main_id'].'\').style.display = \'none\';"':'').'>'.secureOUT($row['ad_name']).'</b></td>
			<td class="pdg nobr">'.$levels[$row['ad_pos']].'</b></td>
			<td class="pdg nobr">'.$row['ad_email'].'</b></td>
			<td class="pdg nobr">'.$row['ad_cell'].'</b></td>
			<td class="pdg nobr" style="width: 100px;" align="right"><a href="extra.php?id='.$row['main_id'].'">ÄNDRA</a> | <a href="extra.php?del='.$row['main_id'].'" onclick="return confirm(\'Säker ?\');">RADERA</a></td>
		</tr>';
		if($gotpic) echo '<tr id="tr:'.$row['main_id'].'" style="display: none;"><td colspan="6"><img src="'.ADMIN_EXTRA_DIR.$row['ad_img'].'"></td></tr>';
	}
?>
					</table>
					</form>
		</td>
	</tr>
	</table>
</body>
</html>