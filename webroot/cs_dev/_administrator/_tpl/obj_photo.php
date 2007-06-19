<?
	if(!function_exists('notallowed') || notallowed()) {
		header("Location: ./");
		exit;
	}
	$thispage = 'obj.php?status=photo';
$reasons = array(
'A' => '.',
'R' => ' på grund av: <b>Reklambudskap i bild.</b>',
'AB' => ' på grund av: <b>Stötande och/eller olämpligt material.</b>');

	if(!empty($_POST['validate'])) {
		$doall = false;
		if(!empty($_POST['main_id:all']) && is_numeric($_POST['main_id:all'])) {
			$doall = true;
			$type = $_POST['main_id:all'];
		}
		foreach($_POST as $key => $val) {
			if(strpos($key, 'status_id') !== false) {
				$kid = explode(':', $key);
				$kid = $kid[1];
				$res = $sql->queryLine("SELECT picd, pht_name, pht_cmt, hidden_id, hidden_value, user_id FROM s_userphoto WHERE main_id = '".$kid."' LIMIT 1");
				if(isset($_POST['status_id:' . $kid]) && !empty($res) && count($res)) {
					if($doall && empty($_POST['status_id:' . $kid])) {
// alla-knapp
						if($type == '1') {
# godkänn blanka
#echo '<b>god bla</b>';
							$line = $sql->queryUpdate("UPDATE s_userphoto SET view_id = '1' WHERE main_id = '".$kid."' AND view_id = '0' LIMIT 1");
							addALog(@$_SESSION['u_i'].' godkände '.$res[5].':'.$kid);
						} elseif($type == '2') {
# neka blanka
#echo '<b>neka bla</b>';
							$un = md5(microtime());
							$line = $sql->queryUpdate("UPDATE s_userphoto SET view_id = '1', status_id = '2' AND hidden_value = '".$un."' WHERE main_id = '".$kid."' LIMIT 1");
							if($res[3])
								@rename(ADMIN_PHOTO_DIR.$res[0].'/'.$kid.'_'.$res[4].'.'.$res[1], './user_photo_off342/'.$kid.'_'.$un.'.jpg');
							else
								@rename(ADMIN_PHOTO_DIR.$res[0].'/'.$kid.'.'.$res[1], './user_photo_off342/'.$kid.'_'.$un.'.jpg');
							#if(!empty($_POST['reason_id:' . $kid]))
							#	$user->spy($res[5], 'PIC', 'MSG', array('Ditt foto: <b>'.secureOUT($res[2]).'</b> har nekats'.$reasons[$_POST['reason_id:' . $kid]]));
							#else
							#	$user->spy($res[5], 'PIC', 'MSG', array('Ditt foto: <b>'.secureOUT($res[2]).'</b> har nekats.'));
							addALog(@$_SESSION['u_i'].' nekade '.$res[5].':'.$kid);
						}
					} else {
						if($_POST['status_id:' . $kid] == '1') {
# godkänn specifik
#echo '<b>god spec</b>';
							$line = $sql->queryUpdate("UPDATE s_userphoto SET view_id = '1' WHERE main_id = '".$kid."' AND view_id = '0' LIMIT 1");
							addALog(@$_SESSION['u_i'].' godkände '.$res[5].':'.$kid);
						} elseif($_POST['status_id:' . $kid] == '2') {
# neka specifik
#echo '<b>neka spec</b>'.$res[5].'Ditt foto: <b>'.secureOUT($res[2]).'</b> har blivit nekat.';
							$un = md5(microtime());
							$line = $sql->queryUpdate("UPDATE s_userphoto SET view_id = '1', status_id = '2' AND hidden_value = '".$un."' WHERE main_id = '".$kid."' LIMIT 1");
							if($res[3])
								@rename(ADMIN_PHOTO_DIR.$res[0].'/'.$kid.'_'.$res[4].'.'.$res[1], './user_photo_off342/'.$kid.'_'.$un.'.jpg');
							else
								@rename(ADMIN_PHOTO_DIR.$res[0].'/'.$kid.'.'.$res[1], './user_photo_off342/'.$kid.'_'.$un.'.jpg');
							#if(!empty($_POST['reason_id:' . $kid]))
							#	$user->spy($res[5], 'PIC', 'MSG', array('Ditt foto: <b>'.secureOUT($res[2]).'</b> har nekats'.$reasons[$_POST['reason_id:' . $kid]]));
							#else
							#	$user->spy($res[5], 'PIC', 'MSG', array('Ditt foto: <b>'.secureOUT($res[2]).'</b> har nekats.'));
							addALog(@$_SESSION['u_i'].' nekade '.$res[5].':'.$kid);
						}
					}
				}
			}
		}
		header('Location: '.$thispage);
		exit;
	} elseif(!empty($_GET['del'])) {
		$un = md5(microtime());
		$res = $sql->queryLine("SELECT picd, hidden_id, hidden_value, pht_name, main_id, pht_cmt, user_id FROM s_userphoto WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		if($res[1])
			@rename(ADMIN_PHOTO_DIR.$res[0].'/'.$res[4].'_'.$res[2].'.'.$res[3], './user_photo_off342/'.$res[4].'_'.$un.'.jpg');
		else
			@rename(ADMIN_PHOTO_DIR.$res[0].'/'.$res[4].'.'.$res[3], './user_photo_off342/'.$res[4].'_'.$un.'.jpg');
		$sql->queryUpdate("UPDATE s_userphoto SET view_id = '1' AND status_id = '2' WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		#if(!empty($_GET['reason']))
		#	$user->spy($res[6], 'PIC', 'MSG', array('Ditt foto: <b>'.secureOUT($res[5]).'</b> har nekats'.$reasons[$_GET['reason']]));
		#else
		#	$user->spy($res[6], 'PIC', 'MSG', array('Ditt foto: <b>'.secureOUT($res[5]).'</b> har nekats.'));
		addALog(@$_SESSION['u_i'].' nekade '.$res[6].':'.$res[4]);
		header("Location: ".$thispage);
		exit;
	}
	$view_cmt = 0;
	if(!empty($_GET['all'])) {
		$view_cmt = 1;
	}
	$view_arr = array(
			$sql->queryResult("SELECT COUNT(*) as count FROM s_userphoto a INNER JOIN s_user u ON u.id_id = a.user_id AND u.status_id = '1' WHERE a.view_id = '0' AND a.status_id = '1'"),
			$sql->queryResult("SELECT COUNT(*) as count FROM s_userphoto a INNER JOIN s_user u ON u.id_id = a.user_id AND u.status_id = '1' WHERE a.view_id = '1' AND a.status_id = '1'"));
	$paging = paging(@$_GET['p'], 50);
$id = (!empty($_GET['id'])?$_GET['id']:false);
	if(!$view_cmt && !$id) {
		$list = $sql->query("SELECT a.main_id, a.view_id, a.picd, a.pht_name, a.hidden_id, a.hidden_value, a.pht_cmt, u.u_alias, u.id_id, u.level_id, u.u_sex, u.u_birth, a.status_id FROM s_userphoto a INNER JOIN s_user u ON u.id_id = a.user_id AND u.status_id = '1' WHERE a.view_id = '0' AND a.status_id = '1' LIMIT {$paging['slimit']}, {$paging['limit']}");
	} elseif(!$id) {
		$list = $sql->query("SELECT a.main_id, a.view_id, a.picd, a.pht_name, a.hidden_id, a.hidden_value, a.pht_cmt, u.u_alias, u.id_id, u.level_id, u.u_sex, u.u_birth, a.status_id FROM s_userphoto a INNER JOIN s_user u ON u.id_id = a.user_id AND u.status_id = '1' WHERE a.view_id = '1' AND a.status_id = '1' ORDER BY a.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}");
	} else {
		$list = $sql->query("SELECT a.main_id, a.view_id, a.picd, a.pht_name, a.hidden_id, a.hidden_value, a.pht_cmt, u.u_alias, u.id_id, u.level_id, u.u_sex, u.u_birth, a.status_id FROM s_userphoto a INNER JOIN s_user u ON u.id_id = a.user_id AND u.status_id = '1' WHERE a.main_id = '".secureINS($id)."' LIMIT 1");
	}
	require("./_tpl/obj_head.php");

?>
			<input type="radio" class="inp_chk" name="view" value="0" id="view_0" onclick="document.location.href = '<?=$thispage?>';"<?=(!$view_cmt)?' checked':'';?>><label for="view_0" class="txt_bld txt_look">Icke granskade</label> [<?=$view_arr[0]?>]
			<input type="radio" class="inp_chk" name="view" value="1" id="view_1" onclick="document.location.href = '<?=$thispage?>&all=1';"<?=($view_cmt)?' checked':'';?>><label for="view_1" class="txt_bld txt_look">Granskade</label> [<?=$view_arr[1]?>]
			<form name="upd" method="post" action="./<?=$thispage?>">
			<input type="hidden" name="main_id:all" id="main_id" value="0">
			<input type="hidden" name="validate" value="1">

			<input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 5px 2px 10px 0;">
			<input type="button" class="inp_realbtn" value="Neka blanka" style="width: 85px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '2'; this.form.submit();">
			<input type="button" class="inp_realbtn" value="Godkänn blanka" style="width: 100px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '1'; this.form.submit();">
<br /><input type="text" class="inp_nrm" style="width: 80px; margin-right: 5px;" id="id_id"><input type="button" class="inp_realbtn" style="margin: 0;" onclick="document.location.href = '<?=$thispage?>&id=' + document.getElementById('id_id').value.replace('#', '');" value="Hämta foto" />
		<br><br>
			<hr /><div class="hr"></div>
<?
	if(isset($paging) && ($paging['p'] > 1 || $view_arr[$view_cmt] > $paging['slimit'] + $paging['limit'])) {
?>
					<table width="100%">
					<tr>
						<?=($paging['p'] > 1)?'<td><a href="'.$thispage.'&all='.$view_cmt.'" class="txt_look txt_bld">tillbaka</a></td>':'';?>
						<td align="right" valign="center">
<?
	$pm1 = $paging['p'] - 1;
	$pp1 = $paging['p'] + 1;
		if($paging['p'] > 1) {
			echo '<a href="'.$thispage.'&all='.$view_cmt.'&p='.$pm1.'" class="txt_look txt_bld">framåt</a>&nbsp;';
		}
		if($view_arr[$view_cmt] > $paging['slimit'] + $paging['limit']) {
			echo '<a href="'.$thispage.'&all='.$view_cmt.'&p='.$pp1.'" class="txt_look txt_bld">bakåt</a>&nbsp;';
		}
?>
						</td>
					</tr>
					</table>
<?	} else echo '<div>&nbsp;</div>';
?>
<script type="text/javascript">
function denyAns(val, id) {
	if(confirm('Säker ?'))
		document.location.href = '<?=$thispage?>&del=' + id + '&reason=' + val;
}
</script>
			<hr /><div class="hr"></div>
<?
	if(count($list) && !empty($list)) {
		echo '<table cellspacing="2">';
	#	echo '<tr><th>Bild</th><th>Användare</th><th>Mobilnummer</th><th>Bildnummer</th><th>Hämtningskod</th><th>Antal hämtningar</th><th>Datum</th></tr>';
		$nl = true;
		$i = 0;
if(!$view_cmt) {
		foreach($list as $row) {
			$i++;
			echo '<tr><td class="pdg cnt"><input type="hidden" name="status_id:'.$row[0].'" id="status_id:'.$row[0].'" value="0"><img src="./_img/status_none.gif" style="margin: 0 1px -1px 2px;" id="1:'.$row[0].'" onclick="changeStatus(\'status\', this.id);"><img src="./_img/status_none.gif" style="margin: 0 0 -1px 1px;" id="2:'.$row[0].'" onclick="document.getElementById(\'reason_reason:'.$row[0].'\').style.display = \'\'; document.getElementById(\'re_re:'.$row[0].'\').style.display = \'none\'; changeStatus(\'status\', this.id);"> | 
<a href="javascript:void(0);" onclick="document.getElementById(\'reason_reason:'.$row[0].'\').style.display = \'none\'; document.getElementById(\'re_re:'.$row[0].'\').style.display = \'\';" onclick="return confirm(\'Säker ?\');">NEKA DIREKT</a>
<div id="re_re:'.$row[0].'" style="display: none;">
<input type="radio" onclick="denyAns(this.value, \''.$row[0].'\');" value="R" id="re_id:'.$row[0].':R"><label for="re_id:'.$row[0].':R">Reklam</label>
<input type="radio" onclick="denyAns(this.value, \''.$row[0].'\');" value="AB" id="re_id:'.$row[0].':AB"><label for="re_id:'.$row[0].':AB">Stötande</label>
</div>
<div id="reason_reason:'.$row[0].'" style="display: none;">
<input type="radio" name="reason_id:'.$row[0].'" value="R" id="reason_id:'.$row[0].':R"><label for="reason_id:'.$row[0].':R">Reklam</label>
<input type="radio" name="reason_id:'.$row[0].'" value="AB" id="reason_id:'.$row[0].':AB"><label for="reason_id:'.$row[0].':AB">Stötande</label>
</div>
<br><img style="margin-top: 3px;" src="'.ADMIN_PHOTO_DIR.$row[2].'/'.$row[0].(($row[4])?'_'.$row[5]:'').'.'.$row[3].'" /><br>'.secureOUT($row[6]).'<br><a href="user.php?t&id='.$row[8].'"><b>'.secureOUT($row[7]).'</b></a></td></tr>';
			echo '<tr><td><hr /><div class="hr"></div></td></tr>';
		}
} else {
		foreach($list as $row) {
			$i++;
			echo '<tr><td class="pdg cnt"><input type="hidden" name="status_id:'.$row[0].'" value="'.$row[12].'"><img src="./_img/status_'.(($row[12] == '1')?'green':'none').'.gif" style="margin: 0 1px -1px 2px;" id="1:'.$row[0].'" onclick="changeStatus(\'status\', this.id);"><img src="./_img/status_'.(($row[12] == '2')?'red':'none').'.gif" style="margin: 0 0 -1px 1px;" id="2:'.$row[0].'" onclick="document.getElementById(\'reason_reason:'.$row[0].'\').style.display = \'\'; document.getElementById(\'re_re:'.$row[0].'\').style.display = \'none\'; changeStatus(\'status\', this.id);"> | 
<a href="javascript:void(0);" onclick="document.getElementById(\'reason_reason:'.$row[0].'\').style.display = \'none\'; document.getElementById(\'re_re:'.$row[0].'\').style.display = \'\';" onclick="return confirm(\'Säker ?\');">NEKA DIREKT</a>
<div id="re_re:'.$row[0].'" style="display: none;">
<input type="radio" onclick="denyAns(this.value, \''.$row[0].'\');" value="R" id="re_id:'.$row[0].':R"><label for="re_id:'.$row[0].':R">Reklam</label>
<input type="radio" onclick="denyAns(this.value, \''.$row[0].'\');" value="AB" id="re_id:'.$row[0].':AB"><label for="re_id:'.$row[0].':AB">Stötande</label>
</div>
<div id="reason_reason:'.$row[0].'" style="display: none;">
<input type="radio" name="reason_id:'.$row[0].'" value="R" id="reason_id:'.$row[0].':R"><label for="reason_id:'.$row[0].':R">Reklam</label>
<input type="radio" name="reason_id:'.$row[0].'" value="AB" id="reason_id:'.$row[0].':AB"><label for="reason_id:'.$row[0].':AB">Stötande</label>
</div>
<br><img style="margin-top: 3px;" src="'.ADMIN_PHOTO_DIR.$row[2].'/'.$row[0].(($row[4])?'_'.$row[5]:'').'.'.$row[3].'" /><br>'.secureOUT($row[6]).'<br><a href="user.php?t&id='.$row[8].'"><b>'.secureOUT($row[7]).'</b></a></td></tr>';
			echo '<tr><td><hr /><div class="hr"></div></td></tr>';
		}
}
		echo '</table>';
	}
	if(isset($paging) && ($paging['p'] > 1 || $view_arr[$view_cmt] > $paging['slimit'] + $paging['limit'])) {
?>
					<table width="100%">
					<tr>
						<?=($paging['p'] > 1)?'<td><a href="'.$thispage.'&all='.$view_cmt.'" class="txt_look txt_bld">tillbaka</a></td>':'';?>
						<td align="right" valign="center">
<?
	$pm1 = $paging['p'] - 1;
	$pp1 = $paging['p'] + 1;
		if($paging['p'] > 1) {
			echo '<a href="'.$thispage.'&all='.$view_cmt.'&p='.$pm1.'" class="txt_look txt_bld">framåt</a>&nbsp;';
		}
		if($view_arr[$view_cmt] > $paging['slimit'] + $paging['limit']) {
			echo '<a href="'.$thispage.'&all='.$view_cmt.'&p='.$pp1.'" class="txt_look txt_bld">bakåt</a>&nbsp;';
		}
?>
						</td>
					</tr>
					</table>
<?	} else echo '<div>&nbsp;</div>';
?>
			<hr /><div class="hr"></div>
			<input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 5px 2px 10px 0;">
			<input type="button" class="inp_realbtn" value="Neka blanka" style="width: 85px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '2'; this.form.submit();">
			<input type="button" class="inp_realbtn" value="Godkänn blanka" style="width: 100px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '1'; this.form.submit();">

			</form>
		</td>
	</tr>
	</table>
</body>
</html>