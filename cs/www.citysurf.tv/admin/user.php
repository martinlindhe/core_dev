<?
	require_once('find_config.php');

	if(!$isCrew) errorNEW('Ingen behörighet.');
	
	if (!empty($_POST['a'])) {
		//visar info om en enskild användare
		$res = $db->getOneItem("SELECT id_id FROM s_user WHERE u_alias = '".$db->escape($_POST['a'])."' LIMIT 1");
		if ($res) {
			header('Location: user.php?id='.$res);
			die;
		}
	}

	function listUserDisabled($list) {
		global $status_id;
		echo '<table cellspacing="2" style="margin: 5px 0 10px 0;">';
		echo '<tr><th>Status</th><th>Alias</th><th>E-post</th><th>Namn</th></tr>';
		foreach ($list as $row) {
			echo '<tr class="bg_gray">
				<td style="width: 60px; padding: 2px 0 0 4px;" class="nobr"><img src="./_img/status_green.gif" style="margin: 4px 1px 0 0;"> <input type="text" disabled value="'.$row['level_id'].'" style="width: 24px; padding: 0; margin-bottom: 4px; line-height: 9px; height: 11px; size: 10px;" onfocus="this.select();" maxlength="5" class="inp_nrm"></td>
				<td class="pdg" style="width: 350px;"><a href="user.php?id='.$row['id_id'].'"><b>'.secureOUT($row['u_alias']).'</b></a></td>
				<td class="pdg">'.secureOUT($row['u_email']).'</td>
				<td class="pdg">'.secureOUT($row['u_fname'].' '.$row['u_sname']).'</td>
				<td class="pdg nobr" align="right"><a href="user.php?id='.$row['id_id'].'">VISA</a> | <a href="user.php?del='.$row['id_id'].'&status='.$status_id.'" onclick="return confirm(\'Proceed ?\');">DELETE</a></td>
			</tr>';
		} 
		echo '</table>';
	}

	$reasons = array(
		'A' => '.',
		'G' => ' på grund av: <b>Solglasögon.</b>',
		'S' => ' på grund av: <b>Oskärpa i bild.</b>',
		'M' => ' på grund av: <b>Flera i personer i bild.</b>',
		'R' => ' på grund av: <b>Reklambudskap i bild.</b>',
		'F' => ' på grund av: <b>Felaktig bild.</b>',
		'AB' => ' på grund av: <b>Stötande och/eller olämpligt material.</b>',
		'TS' => ' på grund av: <b>För litet ansikte.</b>',
		'TSB' => ' på grund av: <b>För litet ansikte, beskär annorlunda.</b>',
		'TD' => ' på grund av: <b>Bilden är för mörk.</b>',
		'TL' => ' på grund av: <b>Bilden är för ljus.</b>',
		'NF' => ' på grund av: <b>Ej rakt framifrån.</b>'
	);

	$change = false;
	$types = array('jpeg', 'swf', 'event');
	$status_id = 0;
	if (isset($_GET['status']) && is_numeric($_GET['status'])) {
		$status_id = $_GET['status'];
	} elseif(!empty($_GET['status']) && $_GET['status'] == 'N') $status_id = 'N';


	//spara ändringar på användarprofil
	if (!empty($_POST['doupd'])) {
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			if ($_POST['alias'] != $_POST['oldalias']) {
				$res = $db->getOneRow("SELECT status_id, u_alias FROM s_user WHERE u_alias = '".$db->escape($_POST['alias'])."' LIMIT 1");
				if (!empty($res)) {
					if ($res['status_id'] == '1' || $res['status_id'] == '3' || $res['status_id'] == 'F') errorACT('Aliaset finns redan. ( '.$res['u_alias'].' )', 'user.php?id='.$_POST['id']);
				}
			}
			if ($_POST['email'] != $_POST['oldemail']) {
				$res = $db->getOneItem("SELECT u_alias FROM s_user WHERE u_email = '".$db->escape($_POST['email'])."' AND status_id = '1' LIMIT 1");
				if ($res) errorNEW('E-postadressen finns redan. (Används av '.$res.' )', 'user.php?id='.$_POST['id']);
			}
			$row = $db->getOneRow("SELECT u.id_id, u.level_enddate, u.level_pending, u.level_id, l.level_id AS search, status_id FROM s_user u LEFT JOIN s_userlevel l ON l.id_id = u.id_id WHERE u.id_id = '".$_POST['id']."'");

			if (!empty($row['search'])) {
				if (strpos($row['search'], 'LEVEL'.$row['level_id'])) {
					$row['search'] = str_replace('LEVEL'.$row['level_id'], 'LEVEL'.$_POST['level'], $row['search']);
				}
				if (strpos($row['search'], 'SEXM')) {
					$row['search'] = str_replace('SEXM', 'SEX'.($_POST['sex'] == 'M'?'M':'F'), $row['search']);
				}
				if (strpos($row['search'], 'SEXF')) {
					$row['search'] = str_replace('SEXF', 'SEX'.($_POST['sex'] == 'M'?'M':'F'), $row['search']);
				}
			}
			$db->update("UPDATE s_userlevel SET level_id = '{$row['search']}' WHERE id_id = '".$row['id_id']."' LIMIT 1");
			if (!empty($row['status_id']) && $row['status_id'] != $_POST['status']) {
				if ($row['status_id'] == '1' && ($_POST['status'] == '2' || $_POST['status'] == '3')) {
					$res = $db->getOneItem("SELECT level_id FROM s_userlevel WHERE id_id = '".$_POST['id']."' LIMIT 1");
					if ($res) $db->replace("REPLACE INTO s_userlevel_off SET id_id = '".$_POST['id']."', level_id = '".$res."'");
					$db->delete("DELETE FROM s_userlevel WHERE id_id = '".$_POST['id']."' LIMIT 1");
				} elseif (($row['status_id'] == '2' || $row['status_id'] == '3') && $_POST['status'] == '1') {
					$res = $db->getOneItem("SELECT level_id FROM s_userlevel_off WHERE id_id = '".$_POST['id']."' LIMIT 1");
					if ($res) $db->replace("REPLACE INTO s_userlevel SET id_id = '".$_POST['id']."', level_id = '".$res."'");
					$db->delete("DELETE FROM s_userlevel_off WHERE id_id = '".$_POST['id']."' LIMIT 1");					
				}
			}
			$db->update("UPDATE s_user SET
				u_alias = '".$db->escape($_POST['alias'])."',
				u_email = '".$db->escape($_POST['email'])."',
				location_id = '".$db->escape($_POST['city'])."',
				level_id = '".$db->escape($_POST['level'])."',
				level_enddate = '".$db->escape($_POST['enddate'])."',
				level_pending = '".$db->escape($_POST['pending'])."',
				level_oldlevel = '".$db->escape($_POST['oldlevel'])."',
				status_id = '".$db->escape($_POST['status'])."',
				u_picdate = '".$db->escape($_POST['picdate'])."',
				u_pass = '".$db->escape($_POST['pass'])."',
				u_pstort = '".$db->escape($_POST['pstort'])."',
				u_sex = '".$db->escape($_POST['sex'])."',
				u_birth = '".$db->escape($_POST['birth'])."'
				WHERE id_id = '".$db->escape($_POST['id'])."' LIMIT 1");
			$db->update("UPDATE s_userinfo SET
				u_fname = '".$db->escape($_POST['fname'])."',
				u_sname = '".$db->escape($_POST['sname'])."',
				u_street = '".$db->escape($_POST['street'])."',
				u_pstnr = '".$db->escape($_POST['pstnr'])."',
				beta = '".$db->escape($_POST['beta'])."',
				msg_count = '".$db->escape($_POST['msg_count'])."',
				money_count = '".$db->escape($_POST['money_count'])."',
				u_cell = '".$db->escape($_POST['cell'])."'
				WHERE id_id = '".$db->escape($_POST['id'])."' LIMIT 1");

			if (!empty($_POST['domail'])) {
				require("../_set/set_mail.php");
				if($_POST['domail'] == 'blocked') {
					require("../_tpl/email_block.php");
					$msg = sprintf($msg, $_POST['alias'], $_POST['block_reason'], $_POST['block_disc']);
					$titl = 'Din profil är blockerad!';
					$type = 'BLOCKERAD';
					$sql->queryUpdate("UPDATE s_user SET
					lastlog_date = NOW()
					WHERE id_id = '".secureINS($_POST['id'])."' LIMIT 1");
 				} elseif($_POST['domail'] == 'unblocked') {
					require("../_tpl/email_unblock.php");
					$msg = sprintf($msg, $_POST['alias'], $_POST['unblock_reason']);
					$titl = 'Din profil är öppnad igen!';
					$type = 'ÖPPNAD';
				}
				doMail($_POST['email'], $titl.' (mottagare: '.$_POST['email'].')', $msg);
				doMail($member_email, $type.': '.$_POST['email'], $msg);
			}
			header('Location: user.php?id='.$_POST['id']);
			die;
		} else {
			//åter-skapa aktiveringskod-mail och skicka ut
			foreach ($_POST as $key => $val) {
				if (strpos($key, 'status_id') === false) continue;
				$kid = explode(":", $key);
				$kid = $kid[1];
				if (isset($_POST['status_id:' . $kid])) {
					$db->update("UPDATE s_user SET status_id = '".$db->escape($_POST['status_id:' . $kid])."', level_id = '".$db->escape($_POST['level_id:' . $kid])."', view_id = '1' WHERE id_id = '".$db->escape($kid)."' LIMIT 1");
				}
			}
			foreach ($_POST as $key => $val) {
				if (strpos($key, 'status:') === false) continue;
				$kid = explode(":", $key);
				$kid = $kid[1];
				if (isset($_POST['code:' . $kid]) && !empty($_POST['code:' . $kid])) {
					$db->update("UPDATE s_userinfo SET reg_code = '".$db->escape($_POST['code:' . $kid])."' WHERE id_id = '".$db->escape($kid)."' LIMIT 1");
				}
				if(isset($_POST['email:' . $kid]) && !empty($_POST['email:' . $kid])) {
					$got = $db->getOneItem("SELECT COUNT(*) FROM s_user WHERE u_email = '".$db->escape($_POST['email:' . $kid])."' WHERE status_id = '1' LIMIT 1");
					if (!$got) $db->update("UPDATE s_user SET u_email = '".$db->escape($_POST['email:' . $kid])."' WHERE id_id = '".$db->escape($kid)."' LIMIT 1");
				}
				if (isset($_POST['sendemail:' . $kid]) && !empty($_POST['sendemail:' . $kid])) {
					$inf = $db->getOneRow("SELECT i.reg_code, u.u_email FROM s_user u LEFT JOIN s_userinfo i ON i.id_id = u.id_id WHERE u.id_id = '".$kid."' LIMIT 1");
					if (!empty($inf) && count($inf)) {
						$msg = sprintf(gettxt('email_activate'), $inf['reg_code'], substr(P2B, 0, -1).l('member', 'activate', secureOUT(str_replace('@', '__at__', $inf['u_email'])), $inf['reg_code']));
						doMail(secureOUT($inf['u_email']), 'Din aktiveringskod: '.$inf['reg_code'], $msg);
						doMail('member@'.URL, secureOUT($inf['u_email']), $msg);
					}
				}
			}
			header('Location: user.php?status='.$_POST['status'].'&sort='.@$_GET['sort'].'&sorttype='.@$_GET['sorttype']);
			die;
		}
	}

	if(!empty($_GET['del']) && is_numeric($_GET['del'])) {
		$row = $db->getOneItem("SELECT status_id FROM s_user WHERE id_id = '".$_GET['del']."' LIMIT 1");
		if($row == 'F' || $row == '2') {
			$db->delete("DELETE FROM s_user WHERE id_id = '".$_GET['del']."' LIMIT 1");	
		} else {
			$db->update("UPDATE s_user SET status_id = '2' WHERE id_id = '".$_GET['del']."' LIMIT 1");
		}
		header('Location: user.php?status='.$status_id);
		die;
	}

	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$row = $db->getOneRow("SELECT u.*, i.* FROM s_user u LEFT JOIN s_userinfo i ON i.id_id = u.id_id WHERE u.id_id = '".$db->escape($_GET['id'])."' LIMIT 1", 1);
		if (!$row['id_id']) $row['id_id'] = $_GET['id']; //martin fulhack, id_id är inte alltid satt
		if (empty($row)) {
			$change = false;
		} else {
			$change = true;
		}
	}

	//neka presentationsbild
	if (!empty($_GET['del_pic']) && is_numeric($_GET['del_pic'])) {
		$res = $db->getOneRow("SELECT id_id, u_picd, u_picid FROM s_user WHERE id_id = '".$_GET['del_pic']."' LIMIT 1");
		if (!empty($res)) {
			@rename('../user_img/'.$res['u_picd'].'/'.$res['id_id'].$res['u_picid'].'.jpg', '../user_img_off/'.$res['id_id'].'_'.md5(microtime()).'.jpg');
			@unlink('../user_img/'.$res['u_picd'].'/'.$res['id_id'].$res['u_picid'].'_2.jpg');
		}
		$string = $db->getOneItem("SELECT level_id FROM s_userlevel WHERE id_id = '".$res['id_id']."' LIMIT 1");
		$string = str_replace('VALID', '', $string);
		$db->update("UPDATE s_userlevel SET level_id = '$string' WHERE id_id = '".$res['id_id']."' LIMIT 1");
		if (!empty($_GET['reason'])) {
			if (!empty($_GET['reasontext']) && $_GET['reason'] == 'X') {
				$user->spy($res['id_id'], 'ID', 'MSG', array('Din nya profilbild har nekats på grund av: <b>'.$_GET['reasontext'].'</b> Prova med en ny.'));
			} else {
				$user->spy($res['id_id'], 'ID', 'MSG', array('Din nya profilbild har nekats'.$reasons[$_GET['reason']].' Prova med en ny.'));
			}
		} else {
			$user->spy($res['id_id'], 'ID', 'MSG', array('Din profilbild har nekats. Prova igen'));
		}
		$db->update("UPDATE s_user SET u_picvalid = '0', u_picdate = '' WHERE id_id = '".$res['id_id']."' LIMIT 1");
		if ($change) {
			header('Location: user.php?id='.$row['id_id']);
		} else {
			header('Location: user.php?status=2');
		}
		die;
	}
	$page = 'USER';
	$menu = $menu_USER;
	$pics = array();
	$list = array();

	if (!$change) {
		if (!$status_id) {
			//nya användare.
			echo 'xx0';
		} else if ($status_id == 'N') {
			//ej granskade
			echo 'xx1';
		} else if ($status_id == '2') {
			$sex = false;
			if(!empty($_GET['sex'])) {
				$sex = $_GET['sex'];
				echo 'xx2';
				$pics = $db->getArray("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname, u_picvalid, u_picid, u_picd, u_sex FROM s_user u LEFT JOIN s_userinfo i ON i.id_id = u.id_id WHERE status_id = '1' AND u_picvalid = '1' AND u_sex = '$sex' ORDER BY u_regdate DESC");
			} else
				echo 'xx3';
				$pics = $db->getArray("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname, u_picvalid, u_picid, u_picd, u_sex FROM s_user u LEFT JOIN s_userinfo i ON i.id_id = u.id_id WHERE status_id = '1' AND u_picvalid = '1' ORDER BY u_regdate DESC");
		} else if ($status_id == '3') {
			//användare online
			echo 'xx4';
		} else if ($status_id == '5') {
			//raderade
			echo 'xx5';
		} else if ($status_id == '10') {
			//uppgraderade
			echo 'xx6';
		} else if ($status_id == '6') {
			//blockerade
			echo 'xx7';
			$list = $db->getArray("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname, u_picvalid, u_picid FROM s_user u LEFT JOIN s_userinfo i ON i.id_id = u.id_id WHERE status_id = '3' ORDER BY lastlog_date DESC");
		} else if ($status_id == '4') {
			$list = false;
			$pics = false;
		} else {
			//aktiva
			echo 'xx8';
			$list = $db->getArray("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname FROM s_user u LEFT JOIN s_userinfo i ON i.id_id = u.id_id WHERE view_id = '1' AND status_id = '1' ORDER BY u_regdate DESC");
		}
	} else {
		echo 'no chng';
	}
	require('admin_head.php');
?>
<script type="text/javascript">
var allowedext = Array("jpg", "jpeg", "gif", "png");
function showError(obj) { obj.src = './_img/status_none.gif'; }
function loadtop() {
	if(parent.<?=FRS?>head)
	parent.<?=FRS?>head.show_active('user');
}
function CSV() {
	document.csv.pass.value = document.getElementById('csv_pass').value;
	document.csv.level.value = document.getElementById('csv_level').value;
	document.csv.extype.value = (document.getElementById('csv_type1').checked)?'skv':'txt';
	document.getElementById('csv_pass').value = '';
	//document.getElementById('csv_level').selectedIndex = 0;
	document.csv.submit();
}
<?=(isset($_GET['t']))?'loadtop();':'';?>
</script>

<form name="csv" action="user_extract.php" method="post">
<input type="hidden" name="pass" value="0">
<input type="hidden" name="level" value="0">
<input type="hidden" name="extype" value="skv">
</form>
	<table height="100%">
<?makeMenuAdmin($page, $menu);?>
	<tr>
		<td width="100%" style="padding: 0 10px 0 0;">
			<form action="?=$_SERVER['PHP_SELF']?>" method="post">
			<br>Visa användare: <input type="text" name="a" value="" class="inp_nrm"> <input type="submit" class="inp_orgbtn" value="UPPDATERA" style="width: 70px; margin: 11px 20px 0 10px;">
			</form>
		
			<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<input type="hidden" name="doupd" value="1">
			<input type="hidden" name="status" value="<?=$status_id?>">
<nobr>
			<input type="radio" class="inp_chk" value="0" id="view_0" onclick="document.location.href = 'user.php?status=' + this.value;"<?=(!$status_id)?' checked':'';?>><label for="view_0" class="txt_bld txt_look">NYA</label>
			<input type="radio" class="inp_chk" value="N" id="view_N" onclick="document.location.href = 'user.php?status=' + this.value;"<?=(strval($status_id) == 'N')?' checked':'';?>><label for="view_N" class="txt_bld txt_look">EJ GRANSKADE</label>
			<input type="radio" class="inp_chk" value="1" id="view_1" onclick="document.location.href = 'user.php?status=' + this.value;"<?=($status_id == '1')?' checked':'';?>><label for="view_1" class="txt_bld txt_look">AKTIVA</label>
			<input type="radio" class="inp_chk" value="2" id="view_2" onclick="document.location.href = 'user.php?status=' + this.value;"<?=($status_id == '2')?' checked':'';?>><label for="view_2" class="txt_bld txt_look">PROFILBILDER</label>
			<input type="radio" class="inp_chk" value="3" id="view_3" onclick="document.location.href = 'user.php?status=' + this.value;"<?=($status_id == '3')?' checked':'';?>><label for="view_3" class="txt_bld txt_look">ONLINE</label>
			<input type="radio" class="inp_chk" value="6" id="view_6" onclick="document.location.href = 'user.php?status=' + this.value;"<?=($status_id == '6')?' checked':'';?>><label for="view_6" class="txt_bld txt_look">BLOCKERADE</label>
			<input type="radio" class="inp_chk" value="5" id="view_5" onclick="document.location.href = 'user.php?status=' + this.value;"<?=($status_id == '5')?' checked':'';?>><label for="view_5" class="txt_bld txt_look">RADERADE</label>
			<input type="radio" class="inp_chk" value="10" id="view_10" onclick="document.location.href = 'user.php?status=' + this.value;"<?=($status_id == '10')?' checked':'';?>><label for="view_10" class="txt_bld txt_look">UPPGRADERADE</label>
			<input type="radio" class="inp_chk" value="4" id="view_4" onclick="document.location.href = 'user.php?status=' + this.value;"<?=($status_id == '4')?' checked':'';?>><label for="view_4" class="txt_bld txt_look">STATS</label>
			<br/>
			<select name="csv_level" id="csv_level" class="inp_nrm">
<option value="0">Alla</option>
<option value="99">MEDLEMSLISTA</option>
<option value="2">MED profilbild</option>
<option value="3">UTAN profilbild</option>
<option value="22">JA till reklam via E-POST</option>
<option value="23">NEJ till reklam via E-POST</option>
<option value="33">JA till reklam via SMS</option>
<option value="34">NEJ till reklam via SMS</option>
<option value="10">Alla STANDARD</option>
<option value="30">Alla BRONS</option>
<option value="50">Alla SILVER</option>
<option value="60">Alla GULD</option>
<option value="100">Alla ADMIN</option>
<option value="4">Alla tjejer</option>
<option value="6">Alla killar</option>
</select> Lösenord: <input type="password" name="csv_pass" id="csv_pass" value="" style="width: 90px;" class="inp_nrm"> <input type="radio" name="csv_type" value="skv" checked id="csv_type1"><label for="csv_type1">SKV</label><input type="radio" id="csv_type2" name="csv_type" value="txt"><label for="csv_type2">TXT</label>
<input type="button" class="inp_orgbtn" style="width: 140px; margin-left: 10px;" value="HÄMTA LISTA" onclick="if(document.getElementById('csv_pass').value.length > 0) CSV();" style="width: 70px; margin: 11px 0 0 20px;">
</nobr>
</form>

<form action="user.php?sort=<?=@$sort?>&sorttype=<?=@$type?>" method="post" name="u_f">
	<input type="hidden" name="doupd" value="1">
	<input type="hidden" name="status" value="<?=$status_id?>">
	<input type="submit" class="inp_orgbtn" value="UPPDATERA" style="width: 70px; margin: 11px 0 0 20px;">
		<table cellspacing="2" style="margin: 5px 0 10px 0;">
<?
	if ($status_id == '4') {

		foreach ($cities as $key => $val) {
			$total = $db->getOneItem("SELECT COUNT(*) FROM s_user WHERE status_id = '1' AND location_id = '".$key."'");
			if (!empty($_GET['show'])) {
				if (is_numeric($_GET['show'])) {
					$list = $db->getArray("SELECT * FROM s_user WHERE status_id = '1' AND YEAR(u_birth) = '".$_GET['show']."' AND location_id = '".$key."'");
				} else if ($_GET['show'] == 'F') {
					$list = $db->getArray("SELECT * FROM s_user WHERE status_id = '1' AND u_sex = 'F' AND location_id = '".$key."'");
				} else if ($_GET['show'] == 'M') {
					$list = $db->getArray("SELECT * FROM s_user WHERE status_id = '1' AND u_sex = 'M' AND location_id = '".$key."'");
				}
			}

			echo '<tr><td colspan="8"><br><br><br><br><b>'.$val.'</b> - Antal i ålder:<br>';
			for ($t = 2000; $t >= 1930; $t--) {
				$t1 = $db->getOneItem("SELECT COUNT(*) FROM s_user WHERE status_id = '1' AND YEAR(u_birth) = '$t' AND location_id = '".$key."'");
				if ($t1) echo $t.': <a href="user.php?status=4&show='.$t.'"><b class="txt_chead">'.$t1.'</b></a><br> ';
				if (!empty($_GET['show']) && $_GET['show'] == $t) {
					listUserDisabled($list);
				}
			}
			echo '</tr>';
			
			$female = $db->getOneItem("SELECT COUNT(*) FROM s_user WHERE status_id = '1' AND u_sex = 'F' AND location_id = '".$key."'");
			echo '<tr><td colspan="8"><b>'.$val.'</b> - Antal tjejer: <a href="user.php?status=4&show=F"><b class="txt_chead">'.$female.'</b></a>.';

			if (!empty($_GET['show']) && $_GET['show'] == 'F') {
				listUserDisabled($list);
			}

			echo '</td></tr>';
			$male = $db->getOneItem("SELECT COUNT(*) FROM s_user WHERE status_id = '1' AND location_id = '".$key."'") - $female;
			echo '<tr><td colspan="8"><b>'.$val.'</b> - Antal killar: <a href="user.php?status=4&show=M"><b class="txt_chead">'.$male.'</b></a>.';

			if(!empty($_GET['show']) && $_GET['show'] == 'M') {
				listUserDisabled($list);
			}

			echo '<br>&nbsp;</td></tr>';
			$without = $db->getOneItem("SELECT COUNT(*) FROM s_user WHERE status_id = '1' AND u_picvalid = '1' AND location_id = '".$key."'");
			echo '<tr><td colspan="8"><b>'.$val.'</b> - Antal med profilbild: <b class="txt_chead">'.$without.'</b></a>.';
			echo '<tr><td colspan="8"><b>'.$val.'</b> - Antal utan profilbild: <b class="txt_chead">'. ($total-$without) .'</b></a>.';
			echo '<tr><td colspan="8"><b>'.$val.'</b> - Totalt: <b class="txt_chead">'.$total.'</b></a>.';
		}

		$total = $db->getOneItem("SELECT COUNT(*) FROM s_user WHERE status_id = '1'");
		if (!empty($_GET['show'])) {
			if (is_numeric($_GET['show'])) {
				$list = $sql->query("SELECT * FROM s_user WHERE status_id = '1' AND YEAR(u_birth) = '".$_GET['show']."'", 0, 1);
			} elseif($_GET['show'] == 'F') {
				$list = $sql->query("SELECT * FROM s_user WHERE status_id = '1' AND u_sex = 'F'", 0, 1);
			} elseif($_GET['show'] == 'M') {
				$list = $sql->query("SELECT * FROM s_user WHERE status_id = '1' AND u_sex = 'M'", 0, 1);
			}
		}
		
		echo '<tr><td colspan="8"><br><br><br><br><b>TOTALT</b> - Antal i ålder:<br>';

		for ($t = 2000; $t >= 1930; $t--) {
			$t1 = $db->getOneItem("SELECT COUNT(*) FROM s_user WHERE status_id = '1' AND YEAR(u_birth) = '$t'");
			if ($t1) echo $t.': <a href="user.php?status=4&show='.$t.'"><b class="txt_chead">'.$t1.'</b></a><br> ';
			if (!empty($_GET['show']) && $_GET['show'] == $t) {
				listUserDisabled($list);
			}
		}

		echo '</tr>';

		$female = $db->getOneItem("SELECT COUNT(*) FROM s_user WHERE status_id = '1' AND u_sex = 'F'");
		echo '<tr><td colspan="8"><b>TOTALT</b> - Antal tjejer: <a href="user.php?status=4&show=F"><b class="txt_chead">'.$female.'</b></a>.';

		if(!empty($_GET['show']) && $_GET['show'] == 'F') {
			listUserDisabled($list);
		}

		echo '</td></tr>';
		
		$male = $db->getOneItem("SELECT COUNT(*) FROM s_user WHERE status_id = '1'") - $female;
		echo '<tr><td colspan="8"><b>TOTALT</b> - Antal killar: <a href="user.php?status=4&show=M"><b class="txt_chead">'.$male.'</b></a>.';

		if(!empty($_GET['show']) && $_GET['show'] == 'M') {
			listUserDisabled($list);
		}

		echo '<br>&nbsp;</td></tr>';
		
		$without = $db->getOneItem("SELECT COUNT(*) FROM s_user WHERE status_id = '1' AND u_picvalid = '1'");
		
		echo '<tr><td colspan="8"><b>TOTALT</b> - Antal med profilbild: <b class="txt_chead">'.$without.'</b></a>.';
		echo '<tr><td colspan="8"><b>TOTALT</b> - Antal utan profilbild: <b class="txt_chead">'. ($total-$without) .'</b></a>.';
		echo '<tr><td colspan="8"><b>TOTALT</b> - Totalt: <b class="txt_chead">'.$total.'</b></a>.';
	} else {
		//fixme: antal listade är alltid 0 här för $list sätts senare

		echo '<tr><td colspan="8"><br>Antal listade: <b class="txt_chead">'.($list) ? count($list) : count($pics).'</b>.<br>&nbsp;</td></tr>';
	}

	$nl = true;
	$ol = 0;
	$old = '';
if ($status_id != '4') {
	if ($status_id) {
		if ($status_id == '10') {
			//uppgraderade
			$list = $db->getArray("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname, level_enddate, level_pending, location_id FROM s_user u LEFT JOIN s_userinfo i ON i.id_id = u.id_id WHERE status_id = '1' AND (level_pending = '1' OR level_pending = '0' AND level_id > 1) ORDER BY level_enddate ASC");
			echo '<tr><th>Status</th><th>Stad</th><th>Alias</th><th>Nivå</th><th>Längd</th></tr>';
			foreach ($list as $row) {
				@$days = @date_diff($row['level_enddate'].' 23:59:00', date("Y-m-d H:i"));
				#name="status_id:'.$row['id_id'].'"
				echo '<input type="hidden" id="status_id:'.$row['id_id'].'" value="'.$row['status_id'].'">';
				echo '<tr class="'.(($row['status_id'] == '2')?'bg_gray':'bg_gray').'">
					<td style="width: 60px; padding: 2px 0 0 4px;" class="nobr">
						<img src="./_img/status_'.(($row['status_id'] == '1')?'green':'none_1').'.gif" style="margin: 4px 1px 0 0;" id="1:'.$row['id_id'].'" onclick="changeStatus(\'status\', this.id);">
						<img src="./_img/status_'.(($row['status_id'] == '2')?'red':'none_2').'.gif" style="margin: 4px 0 0 1px;" id="2:'.$row['id_id'].'" onclick="changeStatus(\'status\', this.id);">
						<input type="text" readonly value="'.$row['level_id'].'" style="width: 24px; padding: 0; margin-bottom: 4px; line-height: 9px; height: 11px; size: 10px;" onfocus="this.select();" maxlength="5" class="inp_nrm">
					</td>
					<td class="pdg">'.@$cities[$row['location_id']].'</td>
					<td class="pdg" style="width: 350px;"><a href="user.php?id='.$row['id_id'].'"><b>'.secureOUT($row['u_alias']).'</b></a></td>
					<td class="pdg">'.@$levels[$row['level_id']].'</td>
					<td class="pdg">'.($row['level_pending'] == '0'?'<b>PENDLAR INTE!!!</b>':'<b class="up">'. $days['days'] .'</b> dag'.(($days['days'] == '1')?'':'ar').', <b class="up">'. $days['hours'] .'</b> timm'.(($days['hours'] == '1')?'e':'ar')).'</td>
					<td class="pdg nobr" align="right"><a href="user.php?id='.$row['id_id'].'">VISA</a> | <a href="user.php?del='.$row['id_id'].'&status='.$status_id.'" onclick="return confirm(\'Proceed ?\');">DELETE</a></td>
					</tr>';
			}
		} else if ($status_id == '5') {
			//raderade användare
			$list = $db->getArray("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname, u_picvalid, u_picid, lastlog_date, u_regdate FROM s_user u LEFT JOIN s_userinfo i ON i.id_id = u.id_id WHERE status_id = '2' ORDER BY lastlog_date DESC");
			echo '<tr><th>Status</th><th>Alias</th><th>Reggade</th><th>Avreggad</th><th>E-post</th><th>Namn</th></tr>';
			foreach ($list as $row) {
				echo '<input type="hidden" name="status_id:'.$row['id_id'].'" id="status_id:'.$row['id_id'].'" value="'.$row['status_id'].'">';
				echo '<tr class="'.(($row['status_id'] == '2')?'bg_gray':'bg_gray').'">
					<td style="width: 60px; padding: 2px 0 0 4px;" class="nobr">
						<img src="./_img/status_'.(($row['status_id'] == '1')?'green':'none_1').'.gif" style="margin: 4px 1px 0 0;" id="1:'.$row['id_id'].'" onclick="changeStatus(\'status\', this.id);">
						<img src="./_img/status_'.(($row['status_id'] == '2')?'red':'none_2').'.gif" style="margin: 4px 0 0 1px;" id="2:'.$row['id_id'].'" onclick="changeStatus(\'status\', this.id);">
						<input type="text" name="level_id:'.$row['id_id'].'" value="'.$row['level_id'].'" style="width: 24px; padding: 0; margin-bottom: 4px; line-height: 9px; height: 11px; size: 10px;" onfocus="this.select();" maxlength="5" class="inp_nrm">
					</td>
					<td class="pdg" style="width: 350px;"><a href="user.php?id='.$row['id_id'].'"><b>'.secureOUT($row['u_alias']).'</b></a></td>
					<td class="pdg nobr">'.niceDate($row['u_regdate']).'</td>
					<td class="pdg nobr">'.niceDate($row['lastlog_date']).'</td>
					<td class="pdg">'.secureOUT($row['u_email']).'</td>
					<td class="pdg">'.secureOUT($row['u_fname'].' '.$row['u_sname']).'</td>
					<td class="pdg nobr" align="right"><a href="user.php?id='.$row['id_id'].'">VISA</a> | <a href="user.php?del='.$row['id_id'].'&status='.$status_id.'" onclick="return confirm(\'Proceed ?\');">DELETE</a></td>
				</tr>';
			}
		} else if ($status_id == '3') {
			//användare online
			$sort = '';
			if (!empty($_GET['sort'])) $sort = $_GET['sort'];
			if (!empty($_POST['sort'])) $sort = $_POST['sort'];
			$type = 'date';
			if (!empty($_GET['sorttype'])) $type = $_GET['sorttype'];
			if (!empty($_POST['sorttype'])) $type = $_POST['sorttype'];
			$list = $db->getArray("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname, u_picvalid, u_picid, location_id, lastlog_date, lastonl_date, account_date FROM s_user u LEFT JOIN s_userinfo i ON i.id_id = u.id_id WHERE status_id = '1' AND account_date > '".$user->timeout('30 MINUTES')."' ORDER BY ".(!$sort?'u_regdate DESC':($type == 'date'?'location_id '.$sort.', u_alias ASC':'lastlog_date '.$sort)));

			echo '<tr><th><a href="user.php?status=3&sort='.($sort == 'ASC'?'DESC':'ASC').'">Stad</a></th><th>Alias</th><th><a href="user.php?status=3&sorttype=login&sort='.($sort == 'ASC'?'DESC':'ASC').'">Inloggningslängd</a></th></tr>';
			foreach ($list as $row) {
				$days = date_diff($row['account_date'], $row['lastonl_date']);
				#echo '<input type="hidden" name="status_id:'.$row[0].'" id="status_id:'.$row[0].'" value="'.$row[1].'">';
				echo '<tr class="'.(($row['status_id'] == '2')?'bg_gray':'bg_gray').'">
					<td class="pdg">'.@$cities[$row['location_id']].'</td>
					<td class="pdg" style="width: 350px;"><a href="user.php?id='.$row['id_id'].'"><b>'.secureOUT($row['u_alias']).'</b></a></td>
					<td class="pdg">'.($row['account_date'] != $row['lastonl_date']?'<b class="up">'. $days['days'] .'</b> dag'.(($days['days'] == '1')?'':'ar').', <b class="up">'. $days['hours'] .'</b> timm'.(($days['hours'] == '1')?'e':'ar').', <b class="up">'. $days['minutes'] .'</b> minut'.(($days['minutes'] == '1')?'':'er'):'<em>loggade in för mindre än 6 min sen.</em>').'</td>
				</tr>';
			}
		} else {
			//visar Ej granskade
			$list = $db->getArray("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname FROM s_user u LEFT JOIN s_userinfo i ON i.id_id = u.id_id WHERE status_id = '1' AND view_id = '0' ORDER BY u_regdate DESC");
			echo '<tr><th>Status</th><th>Alias</th><th>E-post</th><th>Namn</th></tr>';
			foreach ($list as $row) {
				echo '<input type="hidden" name="status_id:'.$row['id_id'].'" id="status_id:'.$row['id_id'].'" value="'.$row['status_id'].'">';
				echo '<tr class="'.(($row['status_id'] == '2')?'bg_gray':'bg_gray').'">
					<td style="width: 60px; padding: 2px 0 0 4px;" class="nobr">
						<img src="./_img/status_'.(($row['status_id'] == '1')?'green':'none_1').'.gif" style="margin: 4px 1px 0 0;" id="1:'.$row['id_id'].'" onclick="changeStatus(\'status\', this.id);">
						<img src="./_img/status_'.(($row['status_id'] == '2')?'red':'none_2').'.gif" style="margin: 4px 0 0 1px;" id="2:'.$row['id_id'].'" onclick="changeStatus(\'status\', this.id);">
						<input type="text" name="level_id:'.$row['id_id'].'" value="'.$row['level_id'].'" style="width: 24px; padding: 0; margin-bottom: 4px; line-height: 9px; height: 11px; size: 10px;" onfocus="this.select();" maxlength="5" class="inp_nrm">
					</td>
					<td class="pdg" style="width: 350px;"><a href="user.php?id='.$row['id_id'].'"><b>'.secureOUT($row['u_alias']).'</b></a></td>
					<td class="pdg">'.secureOUT($row['u_email']).'</td>
					<td class="pdg">'.secureOUT($row['u_fname'].' '.$row['u_sname']).'</td>
					<td class="pdg nobr" align="right"><a href="user.php?id='.$row['id_id'].'">VISA</a> | <a href="user.php?del='.$row['id_id'].'&status='.$status_id.'" onclick="return confirm(\'Proceed ?\');">DELETE</a></td>
					</tr>';
			}
		}
	}
	if (!$status_id && !$change) {

		//visar Nya användare
		$list = $db->getArray("SELECT a.id_id, i.reg_code, a.u_email, a.u_birth, a.u_regdate, i.u_cell FROM s_user a LEFT JOIN s_userinfo i ON i.id_id = a.id_id WHERE a.status_id = 'F' ORDER BY a.u_regdate DESC");
		echo '<tr><th>E-post</th><th>Aktiveringskod</th><th>Mobilnummer</th><th>Födelsedatum</th><th>Datum</th></tr>';
		foreach($list as $row) {
			echo '<tr class="bg_gray"><input type="hidden" class="inp_nrm" name="status:'.$row['id_id'].'" value="'.secureOUT($row['id_id']).'" />
				<td class="pdg" style="width: 150px;"><input style="width: 200px;" type="text" class="inp_nrm" name="email:'.$row['id_id'].'" value="'.secureOUT($row['u_email']).'" /></b></td>
				<td class="pdg"><input style="width: 70px;" type="text" class="inp_nrm" name="code:'.$row['id_id'].'" value="'.secureOUT($row['reg_code']).'" /></td>
				<td class="pdg">'.secureOUT($row['u_cell']).'</td>
				<td class="pdg">'.secureOUT($row['u_birth']).'</td>
				<td class="pdg nobr">'.niceDate($row['u_regdate']).'</td>
				<td class="pdg"><input type="checkbox" name="sendemail:'.$row['id_id'].'" value="1" /></td>
				<td class="pdg nobr" align="right"><a href="user.php?del='.$row['id_id'].'&status='.$status_id.'" onclick="return confirm(\'Proceed ?\');">DELETE</a></td>
			</tr>';
		}
	} else if ($change) {
		//redigera en användare

		echo '<tr><td>';
		echo '<table cellspacing="0"><tr><td style="padding-right: 20px;">';
		echo '<input type="hidden" name="id" value="'.$row['id_id'].'">';
		echo '<input type="hidden" name="oldalias" value="'.secureOUT($row['u_alias']).'">';
		echo '<input type="hidden" name="oldemail" value="'.secureOUT($row['u_email']).'">';
		echo '<input type="hidden" name="domail" value="">';
		echo '<script type="text/javascript">
					function denyAns(val, id, extra) {
						if(!extra) extra = \'\';
						if(confirm(\'Säker ?\'))
						document.location.href = \'user.php?id=\' + id + \'&del_pic=\' + id + \'&reason=\' + val + \'&reasontext=\' + extra;
					}
					</script>';
		echo '<table cellspacing="0"><tr>';
		echo '<td>'.getadminimg($row['id_id'], 1).'</td>';

		echo '<td style="padding-bottom: 5px;"><a href="../user_view.php?id='.$row['id_id'].'" target="_blank" onclick="if(parent.window.opener) parent.window.opener.focus();">Visa profil</a><br/><br/>';
		echo '<b>Alternativ:</b><br><a href="javascript:void(0);" onclick="document.getElementById(\'reason_reason:'.$row['id_id'].'\').style.display = \'\';">NEKA</a><br/>';
		echo '<div id="reason_reason:'.$row['id_id'].'" style="display: none;">';
		echo '<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="R" id="reason_id:'.$row['id_id'].':R"><label for="reason_id:'.$row['id_id'].':R">Reklam</label>';
		echo '<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="AB" id="reason_id:'.$row['id_id'].':AB"><label for="reason_id:'.$row['id_id'].':AB">Stötande</label><br />';
		echo '<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="TSB" id="reason_id:'.$row['id_id'].':TSB"><label for="reason_id:'.$row['id_id'].':TSB">Litet ansikte, beskär</label><br />';
		echo '<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="NF" id="reason_id:'.$row['id_id'].':NF"><label for="reason_id:'.$row['id_id'].':NF">Ej rakt framifrån</label><br />';
		echo '<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="S" id="reason_id:'.$row['id_id'].':S"><label for="reason_id:'.$row['id_id'].':S">Oskärpa</label>';
		echo '<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="M" id="reason_id:'.$row['id_id'].':M"><label for="reason_id:'.$row['id_id'].':M">Flera i bild</label><br />';
		echo '<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="TD" id="reason_id:'.$row['id_id'].':TD"><label for="reason_id:'.$row['id_id'].':TD">Mörk</label>';
		echo '<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="TL" id="reason_id:'.$row['id_id'].':TL"><label for="reason_id:'.$row['id_id'].':TL">Ljus</label><br />';
		echo '<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="F" id="reason_id:'.$row['id_id'].':F"><label for="reason_id:'.$row['id_id'].':F">Fel</label>';
		echo '<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="G" id="reason_id:'.$row['id_id'].':G"><label for="reason_id:'.$row['id_id'].':G">Solglasögon</label><br />';
		echo '<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="if(this.checked) { document.getElementById(\'reasontext_id:'.$row['id_id'].'\').style.display = \'\'; document.getElementById(\'retb_id:'.$row['id_id'].'\').style.display = \'\'; }" onchange="if(!this.checked) { document.getElementById(\'reasontext_id:'.$row['id_id'].'\').style.display = \'none\'; document.getElementById(\'retb_id:'.$row['id_id'].'\').style.display = \'none\'; }" value="X" id="reason_id:'.$row['id_id'].':X"><label for="reason_id:'.$row['id_id'].':X">Valfri</label><br />';
		echo '<input type="text" name="reasontext_id:'.$row['id_id'].'" id="reasontext_id:'.$row['id_id'].'" style="width: 100px; display: none;" value="" class="inp_nrm"><br />';
		echo '<input type="button" class="inp_orgbtn" id="retb_id:'.$row['id_id'].'" style="margin: 0;" style="display: none;" value="skicka valfri" onclick="denyAns(\'X\', \''.$row['id_id'].'\', document.getElementById(\'reasontext_id:'.$row['id_id'].'\').value);" />';
		echo '</div>';
		echo '<br>1: STANDARD<br>2: VIP<br>3: VIP DELUX<br>10: ADMIN</td></tr>';

		echo '<tr>';
			echo '<td><b>Alias:</b><br><input type="text" class="inp_nrm" name="alias" value="'.secureOUT($row['u_alias']).'"></td>';
			echo '<td><b>E-post:</b><br><input type="text" class="inp_nrm" name="email" value="'.secureOUT($row['u_email']).'"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Förnamn:</b><br><input type="text" class="inp_nrm" name="fname" value="'.secureOUT($row['u_fname']).'"></td>';
			echo '<td><b>Efternamn:</b><br><input type="text" class="inp_nrm" name="sname" value="'.secureOUT($row['u_sname']).'"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Gatuadress:</b><br><input type="text" class="inp_nrm" name="street" value="'.secureOUT($row['u_street']).'"></td>';
			echo '<td><b>Postnummer:</b><br><input type="text" class="inp_nrm" name="pstnr" value="'.secureOUT($row['u_pstnr']).'"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Postort:</b><br><input type="text" class="inp_nrm" name="pstort" value="'.secureOUT($row['u_pstort']).'"></td>';
			echo '<td><b>Mobil:</b><br><input type="text" class="inp_nrm" name="cell" value="'.secureOUT($row['u_cell']).'"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Kön:</b><br><input type="text" class="inp_nrm" name="sex" value="'.secureOUT($row['u_sex']).'"></td>';
			echo '<td><b>Ålder:</b><br><input type="text" class="inp_nrm" name="birth" value="'.secureOUT($row['u_birth']).'"></td>';
		echo '</tr>';		echo '<tr>';
			echo '<td><b>Nivå:</b><br><input type="text" class="inp_nrm" name="level" value="'.secureOUT($row['level_id']).'"></td>';
			echo '<td><b>Nivå innan:</b><br><input type="text" class="inp_nrm" name="oldlevel" value="'.secureOUT($row['level_oldlevel']).'"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Nivå pendlar:</b><br><input type="text" class="inp_nrm" name="pending" value="'.secureOUT($row['level_pending']).'"></td>';
			echo '<td><b>Nivå slut:</b><br><input type="text" class="inp_nrm" name="enddate" value="'.secureOUT($row['level_enddate']).'"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td colspan="2"><b>Betatestare:</b><br><input type="text" class="inp_nrm" name="beta" value="'.secureOUT($row['beta']).'"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Profilbildsdatum:</b><br><input type="text" class="inp_nrm" name="picdate" value="'.secureOUT($row['u_picdate']).'"></td>';
			echo '<td><b>Stadskod: <label title="'; foreach($cities as $key => $val) { echo $key.' = '.$val."\n"; } echo '">?</label></b><br><input type="text" class="inp_nrm" name="city" value="'.secureOUT($row['location_id']).'"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td colspan="2"><b>Status:</b> (F = registrerad, 1 = aktiverad, 2 = raderad, 3 = blockerad)<br>';

			echo '<script type="text/javascript">
						var this_status = \''.$row['status_id'].'\';						function getInfo(type, toggle) {
							if(!toggle) toggle = \'\';
							if(toggle == \'\') document.u_f.domail.value = type + \'ed\'; else document.u_f.domail.value = \'\';
							document.getElementById(type + \'_info\').style.display = toggle;
							//document.u_f.submit();
						}
						var r1a = \''.str_replace("\r\n", '\n', gettxt('email-1a')).'\';
						var r2a = \''.str_replace("\r\n", '\n', gettxt('email-2a')).'\';
						var r2b = \''.str_replace("\r\n", '\n', gettxt('email-2b')).'\';
						function insertInfo(id, type) {
							document.getElementById(type).value = eval(\'r\' + id);
						}						</script>';

			echo '<input type="text" class="inp_nrm" name="status" maxlength="1" onchange="
						if(this.value == \'3\' && this_status != \'3\') {
							if(confirm(\'Vill du skicka ut ett e-postmeddelande till:\n'.$row['u_email'].'\nom blockeringen?\')) {
									getInfo(\'block\');
							}
						} else if(this.value == \'1\' && this_status == \'3\') {
							if(confirm(\'Vill du skicka ut ett e-postmeddelande till:\n'.$row['u_email'].'\nom öppningen?\')) {
									getInfo(\'unblock\');
							}
						} else { getInfo(\'block\', \'none\'); getInfo(\'unblock\', \'none\'); }" value="'.secureOUT($row['status_id']).'">';
				echo '</td></tr>';
			echo '<tbody id="block_info" style="display: none;">';
		echo '<tr>';
			echo '<td colspan="2"><b>Anledning till blockering:</b><br>MALLAR: <a href="javascript:void(0);" onclick="insertInfo(\'1a\', \'block_reason\');">1A</a><br><textarea name="block_reason" id="block_reason" class="inp_nrm">'.gettxt('email-1a').'</textarea></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td colspan="2"><b>Disclaimer:</b><br>MALLAR: <a href="javascript:void(0);" onclick="insertInfo(\'2a\', \'block_disc\');">2A</a> - <a href="javascript:void(0);" onclick="insertInfo(\'2b\', \'block_disc\');">2B</a><br><textarea name="block_disc" id="block_disc" class="inp_nrm">'.gettxt('email-2a').'</textarea></td>';
		echo '</tr>';
	echo '</tbody>';
	echo '<tbody id="unblock_info" style="display: none;">';
		echo '<tr>';
			echo '<td colspan="2"><b>Anledning till öppnande:</b><br>MALLAR: Inga.<br><textarea name="unblock_reason" id="unblock_reason"  class="inp_nrm"></textarea></td>';
		echo '</tr>';
	echo '</tbody>';
		echo '<tr>';
			echo '<td><b>Banksaldo:</b><br><input type="text" class="inp_nrm" name="money_count" value="'.@secureOUT($row['money_count']).'"></td>';
			echo '<td><b>SMS-saldo:</b><br><input type="text" class="inp_nrm" name="msg_count" value="'.@secureOUT($row['msg_count']).'"></td>';
		echo '</tr>';

		echo '<tr>';
		if ($_SESSION['data']['u_alias'] != 'webmaster_mentori') echo '<td><b>Lösenord:</b><br><input type="text" class="inp_nrm" name="pass" value="'.secureOUT($row['u_pass']).'"></td>';
		echo '<td align="right"><input type="submit" class="inp_orgbtn" value="Uppdatera"></td>';
		echo '</tr>';
		echo '</table>';
			echo '</td>';
			echo '<td>';
		echo '<table cellspacing="2">';
		echo '<tr class="bg_gray"><td colspan="4" class="pdg bld">40 senaste händelser</td></tr>';

		$v_sql = $db->getArray("SELECT sess_id, sess_ip, sess_date, type_inf FROM s_usersess WHERE id_id = '".$db->escape($row['id_id'])."' ORDER BY main_id DESC LIMIT 40");
		$names = array('i' => 'in', 'o' => 'ut', 'f' => '<b>felaktig</b>');
		foreach ($v_sql as $val) {
			echo '<tr class="bg_gray">';
			echo '<td class="pdg nobr">'.niceDate($val['sess_date']).'</td>';
			echo '<td class="pdg"><a href="search.php?t&view=s&s='.$val['sess_id'].'">'.substr($val['sess_id'], 0, 5).'</a></td>';
			echo '<td class="pdg"><a href="search.php?t&view=s&s='.$val['sess_ip'].'">'.$val['sess_ip'].'</a></td>';
			echo '<td class="pdg">'.$names[$val['type_inf']].'</td>';
			echo '</tr>';
		}
		echo '</table></td></tr>';

	} //end if "redigera profil"

} else {

	if (!empty($pics)) {
?>
<script type="text/javascript">
function denyAns(val, id, extra) {
	if(!extra) extra = '';
	if(confirm('Säker ?'))
		document.location.href = 'user.php?status=2&del_pic=' + id + '&reason=' + val + '&reasontext=' + extra;
}
</script>
			<input type="radio" class="inp_chk" value="A" id="view_A" onclick="document.location.href = 'user.php?status=2';"<?=(!$sex)?' checked':'';?>><label for="view_A" class="txt_bld txt_look">ALLA</label>
			<input type="radio" class="inp_chk" value="M" id="view_M" onclick="document.location.href = 'user.php?status=2&sex=' + this.value;"<?=($sex == 'M')?' checked':'';?>><label for="view_M" class="txt_bld txt_look">KILLAR</label>
			<input type="radio" class="inp_chk" value="F" id="view_F" onclick="document.location.href = 'user.php?status=2&sex=' + this.value;"<?=($sex == 'F')?' checked':'';?>><label for="view_F" class="txt_bld txt_look">TJEJER</label>
<?
			$nl = true;
			$i = 0;
			foreach($pics as $row) {
				if($i % 8 == 0) $nl = true;
				if($i && $nl) echo '</tr>';
				if($nl) echo '<tr class="'.(($row[1] == '2')?'bg_dgray wht':'bg_gray').'">';
				if($nl) $nl = false;
				$i++;
				echo '<td class="pdg cnt"><a href="javascript:void(0);" onclick="document.getElementById(\'reason_reason:'.$row[0].'\').style.display = \'\';">NEKA DIREKT</a><br />
<div id="reason_reason:'.$row[0].'" style="display: none;">
<input type="radio" name="reason_id:'.$row[0].'" onclick="denyAns(this.value, \''.$row[0].'\');" value="R" id="reason_id:'.$row[0].':R"><label for="reason_id:'.$row[0].':R">Reklam</label>
<input type="radio" name="reason_id:'.$row[0].'" onclick="denyAns(this.value, \''.$row[0].'\');" value="AB" id="reason_id:'.$row[0].':AB"><label for="reason_id:'.$row[0].':AB">Stötande</label><br />
<input type="radio" name="reason_id:'.$row[0].'" onclick="denyAns(this.value, \''.$row[0].'\');" value="TSB" id="reason_id:'.$row[0].':TSB"><label for="reason_id:'.$row[0].':TSB">Litet ansikte, beskär</label><br />
<input type="radio" name="reason_id:'.$row[0].'" onclick="denyAns(this.value, \''.$row[0].'\');" value="NF" id="reason_id:'.$row[0].':NF"><label for="reason_id:'.$row[0].':NF">Ej rakt framifrån</label><br />
<input type="radio" name="reason_id:'.$row[0].'" onclick="denyAns(this.value, \''.$row[0].'\');" value="S" id="reason_id:'.$row[0].':S"><label for="reason_id:'.$row[0].':S">Oskärpa</label>
<input type="radio" name="reason_id:'.$row[0].'" onclick="denyAns(this.value, \''.$row[0].'\');" value="M" id="reason_id:'.$row[0].':M"><label for="reason_id:'.$row[0].':M">Flera i bild</label><br />
<input type="radio" name="reason_id:'.$row[0].'" onclick="denyAns(this.value, \''.$row[0].'\');" value="TD" id="reason_id:'.$row[0].':TD"><label for="reason_id:'.$row[0].':TD">Mörk</label>
<input type="radio" name="reason_id:'.$row[0].'" onclick="denyAns(this.value, \''.$row[0].'\');" value="TL" id="reason_id:'.$row[0].':TL"><label for="reason_id:'.$row[0].':TL">Ljus</label><br />
<input type="radio" name="reason_id:'.$row[0].'" onclick="denyAns(this.value, \''.$row[0].'\');" value="F" id="reason_id:'.$row[0].':F"><label for="reason_id:'.$row[0].':F">Fel</label>
<input type="radio" name="reason_id:'.$row[0].'" onclick="denyAns(this.value, \''.$row[0].'\');" value="G" id="reason_id:'.$row[0].':G"><label for="reason_id:'.$row[0].':G">Solglasögon</label><br />
<input type="radio" name="reason_id:'.$row[0].'" onclick="if(this.checked) { document.getElementById(\'reasontext_id:'.$row[0].'\').style.display = \'\'; document.getElementById(\'retb_id:'.$row[0].'\').style.display = \'\'; }" onchange="if(!this.checked) { document.getElementById(\'reasontext_id:'.$row[0].'\').style.display = \'none\'; document.getElementById(\'retb_id:'.$row[0].'\').style.display = \'none\'; }" value="X" id="reason_id:'.$row[0].':X"><label for="reason_id:'.$row[0].':X">Valfri</label><br />
<input type="text" name="reasontext_id:'.$row[0].'" id="reasontext_id:'.$row[0].'" style="width: 100px; display: none;" value="" class="inp_nrm"><br />
<input type="button" class="inp_orgbtn" id="retb_id:'.$row[0].'" style="margin: 0;" style="display: none;" value="skicka valfri" onclick="denyAns(\'X\', \''.$row[0].'\', document.getElementById(\'reasontext_id:'.$row[0].'\').value);" />
</div>
'.getadminimg($row[0], 1).'<br><a href="user.php?id='.$row[0].'"><b>'.secureOUT($row[3]).'</b></a></td>';
			}
			echo '</tr>';
		} else {
			if (!$change) echo '<tr><td class="pdg"><em>LISTAR INGET.</em></td></tr>';
		}
	}
?>
					</table>
					</form>
		</td>
	</tr>
	</table>
<? require('admin_foot.php'); ?>
