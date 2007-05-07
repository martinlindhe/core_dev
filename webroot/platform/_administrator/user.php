<?
session_start();
#ob_start();
#    ob_implicit_flush(0);
#    ob_start('ob_gzhandler');
	ini_set("max_execution_time", 0);
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	require("../_config/validate.fnc.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	if(!$isCrew) errorNEW('Ingen behörighet.');
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
'NF' => ' på grund av: <b>Ej rakt framifrån.</b>');
	$sql = &new sql();
	$user = &new user($sql);
	$change = false;
	$types = array('jpeg', 'swf', 'event');
	$status_id = 0;
	if(isset($_GET['status']) && is_numeric($_GET['status'])) {
		$status_id = $_GET['status'];
	} elseif(!empty($_GET['status']) && $_GET['status'] == 'N') $status_id = 'N';
	elseif($change) $status_id = $row['status_id'];


	if(!empty($_POST['doupd'])) {
		if(!empty($_POST['id']) && is_md5($_POST['id'])) {
			if($_POST['alias'] != $_POST['oldalias']) {
				$res = $sql->queryLine("SELECT status_id, u_alias FROM {$t}user WHERE u_alias = '".secureINS($_POST['alias'])."' LIMIT 1");
				if(!empty($res) && count($res)) if($res[0] == '1' || $res[0] == '3' || $res[0] == 'F') errorACT('Aliaset finns redan. ( '.$res[1].' )', 'user.php?id='.$_POST['id']);
			}
			if($_POST['email'] != $_POST['oldemail']) {
				$res = $sql->queryResult("SELECT u_alias FROM {$t}user WHERE u_email = '".secureINS($_POST['email'])."' AND status_id = '1' LIMIT 1");
				if($res) errorACT('E-postadressen finns redan. ( '.$res.' )', 'user.php?id='.$_POST['id']);
			}
			$row = $sql->queryLine("SELECT u.id_id, u.level_enddate, u.level_pending, u.level_id, l.level_id AS search, status_id FROM {$t}user u LEFT JOIN {$t}userlevel l ON l.id_id = u.id_id WHERE u.id_id = '".$_POST['id']."'", 1);
			if(!empty($row['search'])) {
				if(strpos($row['search'], 'LEVEL'.$row['level_id'])) {
					$row['search'] = str_replace('LEVEL'.$row['level_id'], 'LEVEL'.$_POST['level'], $row['search']);
				}
				if(strpos($row['search'], 'SEXM')) {
					$row['search'] = str_replace('SEXM', 'SEX'.($_POST['sex'] == 'M'?'M':'F'), $row['search']);
				}
				if(strpos($row['search'], 'SEXF')) {
					$row['search'] = str_replace('SEXF', 'SEX'.($_POST['sex'] == 'M'?'M':'F'), $row['search']);
				}
			}
			$sql->queryUpdate("UPDATE {$t}userlevel SET level_id = '{$row['search']}' WHERE id_id = '".$row['id_id']."' LIMIT 1");
			if(!empty($row['status_id']) && $row['status_id'] != $_POST['status']) {
				if($row['status_id'] == '1' && ($_POST['status'] == '2' || $_POST['status'] == '3')) {
					$res = $sql->queryResult("SELECT l.level_id FROM {$t}userlevel l WHERE l.id_id = '".$_POST['id']."' LIMIT 1");
					if(!empty($res)) $sql->queryUpdate("REPLACE INTO {$t}userlevel_off SET id_id = '".$_POST['id']."', level_id = '".secureINS($res)."'");
					$sql->queryUpdate("DELETE FROM {$t}userlevel WHERE id_id = '".$_POST['id']."' LIMIT 1");
				} elseif(($row['status_id'] == '2' || $row['status_id'] == '3') && $_POST['status'] == '1') {
					$res = $sql->queryResult("SELECT l.level_id FROM {$t}userlevel_off l WHERE l.id_id = '".$_POST['id']."' LIMIT 1");
					if(!empty($res)) $sql->queryUpdate("REPLACE INTO {$t}userlevel SET id_id = '".$_POST['id']."', level_id = '".secureINS($res)."'");
					$sql->queryUpdate("DELETE FROM {$t}userlevel_off WHERE id_id = '".$_POST['id']."' LIMIT 1");					
				}
			}
			$sql->queryUpdate("UPDATE {$t}user SET
				u_alias = '".secureINS($_POST['alias'])."',
				u_email = '".secureINS($_POST['email'])."',
				location_id = '".secureINS($_POST['city'])."',
				level_id = '".secureINS($_POST['level'])."',
				level_enddate = '".secureINS($_POST['enddate'])."',
				level_pending = '".secureINS($_POST['pending'])."',
				level_oldlevel = '".secureINS($_POST['oldlevel'])."',
				status_id = '".secureINS($_POST['status'])."',
				u_picdate = '".secureINS($_POST['picdate'])."',
				u_pass = '".secureINS($_POST['pass'])."',
				u_pstort = '".secureINS($_POST['pstort'])."',
				u_sex = '".secureINS($_POST['sex'])."',
				u_birth = '".secureINS($_POST['birth'])."'
			WHERE id_id = '".secureINS($_POST['id'])."' LIMIT 1");
			$sql->queryUpdate("UPDATE {$t}userinfo SET
				u_fname = '".secureINS($_POST['fname'])."',
				u_sname = '".secureINS($_POST['sname'])."',
				u_street = '".secureINS($_POST['street'])."',
				u_pstnr = '".secureINS($_POST['pstnr'])."',
				beta = '".secureINS($_POST['beta'])."',
				msg_count = '".secureINS($_POST['msg_count'])."',
				money_count = '".secureINS($_POST['money_count'])."',
				u_cell = '".secureINS($_POST['cell'])."'
			WHERE id_id = '".secureINS($_POST['id'])."' LIMIT 1");
			if(!empty($_POST['domail'])) {
				require("../_set/set_mail.php");
				if($_POST['domail'] == 'blocked') {
					require("../_tpl/email_block.php");
					$msg = sprintf($msg, $_POST['alias'], $_POST['block_reason'], $_POST['block_disc']);
					$titl = 'Din profil är blockerad!';
					$type = 'BLOCKERAD';
					$sql->queryUpdate("UPDATE {$t}user SET
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
			header("Location: user.php?id={$_POST['id']}");
			exit;
		} elseif(!empty($_POST['a'])) {
			$res = $sql->queryResult("SELECT id_id FROM {$t}user WHERE u_alias = '".secureINS($_POST['a'])."' LIMIT 1");
			if($res) header("Location: user.php?id=".$res);
		} else {
			foreach($_POST as $key => $val) {
				if(strpos($key, 'status_id') !== false) {
					$kid = explode(":", $key);
					$kid = $kid[1];
					if(isset($_POST['status_id:' . $kid])) {
						$sql->queryUpdate("UPDATE {$t}user SET status_id = '".secureINS($_POST['status_id:' . $kid])."', level_id = '".secureINS($_POST['level_id:' . $kid])."', view_id = '1' WHERE id_id = '".secureINS($kid)."' LIMIT 1");
					}
				}
			}
			foreach($_POST as $key => $val) {
				if(strpos($key, 'status:') !== false) {
					$kid = explode(":", $key);
					$kid = $kid[1];
					if(isset($_POST['code:' . $kid]) && !empty($_POST['code:' . $kid])) {
						$sql->queryUpdate("UPDATE {$t}userinfo SET reg_code = '".secureINS($_POST['code:' . $kid])."' WHERE id_id = '".secureINS($kid)."' LIMIT 1");
					}
					if(isset($_POST['email:' . $kid]) && !empty($_POST['email:' . $kid])) {
						$got = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}user WHERE u_email = '".secureINS($_POST['email:' . $kid])."' WHERE status_id = '1' LIMIT 1");
						if(!$got) $sql->queryUpdate("UPDATE {$t}user SET u_email = '".secureINS($_POST['email:' . $kid])."' WHERE id_id = '".secureINS($kid)."' LIMIT 1");
					}
					if(isset($_POST['sendemail:' . $kid]) && !empty($_POST['sendemail:' . $kid])) {
						$inf = $sql->queryLine("SELECT i.reg_code, u.u_email FROM {$t}user u LEFT JOIN {$t}userinfo i ON i.id_id = u.id_id WHERE u.id_id = '".$kid."' LIMIT 1");
						if(!empty($inf) && count($inf)) {
							$msg = sprintf(gettxt('email_activate'), $inf[0], substr(P2B, 0, -1).l('member', 'activate', secureOUT(str_replace('@', '__at__', $inf[1])), $inf[0]));
							doMail(secureOUT($inf[1]), 'Din aktiveringskod: '.$inf[0], $msg);
							doMail('member@'.URL, secureOUT($inf[1]), $msg);
						}
					}
				}
			}
			header("Location: user.php?status={$_POST['status']}&sort=".@$_GET['sort'].'&sorttype='.@$_GET['sorttype']);
			exit;
		}
	}

	$change = false;
	if(!empty($_GET['del']) && is_md5($_GET['del'])) {
		$row = $sql->queryResult("SELECT status_id FROM {$t}user WHERE id_id = '".secureINS($_GET['del'])."' LIMIT 1");
		if($row == 'F' || $row == '2') {
			$sql->queryUpdate("DELETE FROM {$t}user WHERE id_id = '".secureINS($_GET['del'])."' LIMIT 1");	
		} else $sql->query("UPDATE {$t}user SET status_id = '2' WHERE id_id = '".secureINS($_GET['del'])."' LIMIT 1");
		header("Location: user.php?status=$status_id");
		exit;
	}

	if(!empty($_GET['id']) && is_md5($_GET['id'])) {
		$row = $sql->queryLine("SELECT u.*, i.* FROM {$t}user u LEFT JOIN {$t}userinfo i ON i.id_id = u.id_id WHERE u.id_id = '".secureINS($_GET['id'])."' LIMIT 1", 1);
		if(!count($row)) {
			$change = false;
		} else {
			$change = true;
		}
	}

	if(!empty($_GET['del_pic'])) {
		$res = $sql->queryLine("SELECT id_id, u_picd, u_picid FROM {$t}user WHERE id_id = '".secureINS($_GET['del_pic'])."' LIMIT 1");
		if(!empty($res) && count($res)) {
			@rename('../user_img/'.$res[1].'/'.$res[0].$res[2].'.jpg', '../user_img_off/'.$res[0].'_'.md5(microtime()).'.jpg');
			@unlink('../user_img/'.$res[1].'/'.$res[0].$res[2].'_2.jpg');
		}
		$string = $sql->queryResult("SELECT level_id FROM {$t}userlevel WHERE id_id = '".$res[0]."' LIMIT 1");
		$string = str_replace('VALID', '', $string);
		$sql->queryUpdate("UPDATE {$t}userlevel SET level_id = '$string' WHERE id_id = '".$res[0]."' LIMIT 1");
		if(!empty($_GET['reason'])) {
			if(!empty($_GET['reasontext']) && $_GET['reason'] == 'X')
				$user->spy($res[0], 'ID', 'MSG', array('Din nya profilbild har nekats på grund av: <b>'.$_GET['reasontext'].'</b> Prova med en ny.'));
			else
				$user->spy($res[0], 'ID', 'MSG', array('Din nya profilbild har nekats'.$reasons[$_GET['reason']].' Prova med en ny.'));
		} else
			$user->spy($res[0], 'ID', 'MSG', array('Din profilbild har nekats. Prova igen'));
		$sql->queryUpdate("UPDATE {$t}user SET u_picvalid = '0', u_picdate = '' WHERE id_id = '".secureINS($res[0])."' LIMIT 1");
		if($change)
			header("Location: user.php?id=".$row['id_id']);
		else
			header("Location: user.php?status=2");
		exit;
	}
	$page = 'USER';
	$menu = $menu_USER;
	$pics = array();
	$list = array();
	if(!$change) {
		if(!$status_id) {
			$list = $sql->query("SELECT a.id_id, i.reg_code, a.u_email, a.u_birth, a.u_regdate, i.u_cell FROM {$t}user a LEFT JOIN {$t}userinfo i ON i.id_id = a.id_id WHERE a.status_id = 'F' ORDER BY a.u_regdate DESC");
		} elseif($status_id == 'N') {
			$list = $sql->query("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname FROM {$t}user u LEFT JOIN {$t}userinfo i ON i.id_id = u.id_id WHERE status_id = '1' AND view_id = '0' ORDER BY u_regdate DESC");
		} elseif($status_id == '2') {
			$sex = false;
			if(!empty($_GET['sex'])) {
				$sex = $_GET['sex'];
				$pics = $sql->query("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname, u_picvalid, u_picid, u_picd FROM {$t}user u LEFT JOIN {$t}userinfo i ON i.id_id = u.id_id WHERE status_id = '1' AND u_picvalid = '1' AND u_sex = '$sex' ORDER BY u_regdate DESC");
			} else
				$pics = $sql->query("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname, u_picvalid, u_picid, u_picd FROM {$t}user u LEFT JOIN {$t}userinfo i ON i.id_id = u.id_id WHERE status_id = '1' AND u_picvalid = '1' ORDER BY u_regdate DESC");
		} elseif($status_id == '3') {
			$sort = '';
			if(!empty($_GET['sort'])) $sort = $_GET['sort'];
			if(!empty($_POST['sort'])) $sort = $_POST['sort'];
			$type = 'date';
			if(!empty($_GET['sorttype'])) $type = $_GET['sorttype'];
			if(!empty($_POST['sorttype'])) $type = $_POST['sorttype'];
			$list = $sql->query("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname, u_picvalid, u_picid, location_id, lastlog_date, lastonl_date, account_date FROM {$t}user u LEFT JOIN {$t}userinfo i ON i.id_id = u.id_id WHERE status_id = '1' AND account_date > '".$user->timeout('30 MINUTES')."' ORDER BY ".(!$sort?'u_regdate DESC':($type == 'date'?'location_id '.$sort.', u_alias ASC':'lastlog_date '.$sort)));
		} elseif($status_id == '5') {
			$list = $sql->query("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname, u_picvalid, u_picid, lastlog_date, u_regdate FROM {$t}user u LEFT JOIN {$t}userinfo i ON i.id_id = u.id_id WHERE status_id = '2' ORDER BY lastlog_date DESC");
		} elseif($status_id == '10') {
			$list = $sql->query("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname, level_enddate, level_pending, location_id FROM {$t}user u LEFT JOIN {$t}userinfo i ON i.id_id = u.id_id WHERE status_id = '1' AND (level_pending = '1' OR level_pending = '0' AND level_id > 1) ORDER BY level_enddate ASC");
		} elseif($status_id == '6') {
			$list = $sql->query("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname, u_picvalid, u_picid FROM {$t}user u LEFT JOIN {$t}userinfo i ON i.id_id = u.id_id WHERE status_id = '3' ORDER BY lastlog_date DESC");
		} elseif($status_id == '4') {
			$list = false;
			$pics = false;
		} else {
			$list = $sql->query("SELECT u.id_id, status_id, level_id, u_alias, u_email, u_fname, u_sname FROM {$t}user u LEFT JOIN {$t}userinfo i ON i.id_id = u.id_id WHERE view_id = '1' AND status_id = '1' ORDER BY u_regdate DESC");
		}
	} else $list = '';
	require("./_tpl/admin_head.php");
?>
	<script type="text/javascript" src="fnc_adm.js"></script>
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
			<form action="user.php" method="post">
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
			<br>Visar information från användare: <input type="text" name="a" value="" class="inp_nrm"> <input type="submit" class="inp_orgbtn" value="UPPDATERA" style="width: 70px; margin: 11px 20px 0 10px;"> | <select name="csv_level" id="csv_level" class="inp_nrm">
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
<a href="../STAEDA.EXE"><img src="./styleform.gif" alt="STÄDA" style="margin: 0 4px -10px 0;" />STÄDA</a><input type="button" class="inp_orgbtn" style="width: 140px; margin-left: 10px;" value="HÄMTA LISTA" onclick="if(document.getElementById('csv_pass').value.length > 0) CSV();" style="width: 70px; margin: 11px 0 0 20px;">
</nobr>
			</form>
<form action="user.php?sort=<?=@$sort?>&sorttype=<?=@$type?>" method="post" name="u_f">
			<input type="hidden" name="doupd" value="1">
			<input type="hidden" name="status" value="<?=$status_id?>">
<input type="submit" class="inp_orgbtn" value="UPPDATERA" style="width: 70px; margin: 11px 0 0 20px;">
			<table cellspacing="2" style="margin: 5px 0 10px 0;">
<?
function listUserDisabled($list) {
global $status_id;
echo '<table cellspacing="2" style="margin: 5px 0 10px 0;">
<tr><th>Status</th><th>Alias</th><th>E-post</th><th>Namn</th></tr>';
	foreach($list as $row) {
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
	if($status_id == '4') {

foreach($cities as $key => $val) {
################
		$total = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}user WHERE status_id = '1' AND location_id = '".$key."'");
		if(!empty($_GET['show'])) {
			if(is_numeric($_GET['show'])) {
				$list = $sql->query("SELECT * FROM {$t}user WHERE status_id = '1' AND YEAR(u_birth) = '".$_GET['show']."' AND location_id = '".$key."'", 0, 1);
			} elseif($_GET['show'] == 'F') {
				$list = $sql->query("SELECT * FROM {$t}user WHERE status_id = '1' AND u_sex = 'F' AND location_id = '".$key."'", 0, 1);
			} elseif($_GET['show'] == 'M') {
				$list = $sql->query("SELECT * FROM {$t}user WHERE status_id = '1' AND u_sex = 'M' AND location_id = '".$key."'", 0, 1);
			}
		}
?>
			<tr><td colspan="8"><br><br><br><br><b><?=$val?></b> - Antal i ålder:<br><? 	for($t = 2000; $t >= 1930; $t--) { $t1 = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}user WHERE status_id = '1' AND YEAR(u_birth) = '$t' AND location_id = '".$key."'"); if($t1) echo $t.': <a href="user.php?status=4&show='.$t.'"><b class="txt_chead">'.$t1.'</b></a><br> ';
	if(!empty($_GET['show']) && $_GET['show'] == $t) {
listUserDisabled($list);
	}
} ?></tr>
			<tr><td colspan="8"><b><?=$val?></b> - Antal tjejer: <a href="user.php?status=4&show=F"><b class="txt_chead"><?=$female = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}user WHERE status_id = '1' AND u_sex = 'F' AND location_id = '".$key."'")?></b></a>.
<?
	if(!empty($_GET['show']) && $_GET['show'] == 'F') {
listUserDisabled($list);
	}
?>
			</td></tr>
			<tr><td colspan="8"><b><?=$val?></b> - Antal killar: <a href="user.php?status=4&show=M"><b class="txt_chead"><?=$sql->queryResult("SELECT COUNT(*) as count FROM {$t}user WHERE status_id = '1' AND location_id = '".$key."'") - $female?></b></a>.
<?
	if(!empty($_GET['show']) && $_GET['show'] == 'M') {
listUserDisabled($list);
	}
?>
<br>&nbsp;</td></tr>
			<tr><td colspan="8"><b><?=$val?></b> - Antal med profilbild: <b class="txt_chead"><?=$without = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}user WHERE status_id = '1' AND u_picvalid = '1' AND location_id = '".$key."'")?></b></a>.
			<tr><td colspan="8"><b><?=$val?></b> - Antal utan profilbild: <b class="txt_chead"><?=$total-$without?></b></a>.
			<tr><td colspan="8"><b><?=$val?></b> - Totalt: <b class="txt_chead"><?=$total?></b></a>.
<?

################
}

		$total = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}user WHERE status_id = '1'");
		if(!empty($_GET['show'])) {
			if(is_numeric($_GET['show'])) {
				$list = $sql->query("SELECT * FROM {$t}user WHERE status_id = '1' AND YEAR(u_birth) = '".$_GET['show']."'", 0, 1);
			} elseif($_GET['show'] == 'F') {
				$list = $sql->query("SELECT * FROM {$t}user WHERE status_id = '1' AND u_sex = 'F'", 0, 1);
			} elseif($_GET['show'] == 'M') {
				$list = $sql->query("SELECT * FROM {$t}user WHERE status_id = '1' AND u_sex = 'M'", 0, 1);
			}
		}
?>
			<tr><td colspan="8"><br><br><br><br><b>TOTALT</b> - Antal i ålder:<br><? 	for($t = 2000; $t >= 1930; $t--) { $t1 = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}user WHERE status_id = '1' AND YEAR(u_birth) = '$t'"); if($t1) echo $t.': <a href="user.php?status=4&show='.$t.'"><b class="txt_chead">'.$t1.'</b></a><br> ';
	if(!empty($_GET['show']) && $_GET['show'] == $t) {
listUserDisabled($list);
	}
} ?></tr>
			<tr><td colspan="8"><b>TOTALT</b> - Antal tjejer: <a href="user.php?status=4&show=F"><b class="txt_chead"><?=$female = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}user WHERE status_id = '1' AND u_sex = 'F'")?></b></a>.
<?
	if(!empty($_GET['show']) && $_GET['show'] == 'F') {
listUserDisabled($list);
	}
?>
			</td></tr>
			<tr><td colspan="8"><b>TOTALT</b> - Antal killar: <a href="user.php?status=4&show=M"><b class="txt_chead"><?=$sql->queryResult("SELECT COUNT(*) as count FROM {$t}user WHERE status_id = '1'") - $female?></b></a>.
<?
	if(!empty($_GET['show']) && $_GET['show'] == 'M') {
listUserDisabled($list);
	}
?>
<br>&nbsp;</td></tr>
			<tr><td colspan="8"><b>TOTALT</b> - Antal med profilbild: <b class="txt_chead"><?=$without = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}user WHERE status_id = '1' AND u_picvalid = '1'")?></b></a>.
			<tr><td colspan="8"><b>TOTALT</b> - Antal utan profilbild: <b class="txt_chead"><?=$total-$without?></b></a>.
			<tr><td colspan="8"><b>TOTALT</b> - Totalt: <b class="txt_chead"><?=$total?></b></a>.
<?







	} else {
?>
			<tr><td colspan="8"><br>Antal listade: <b class="txt_chead"><?=($list)?@count($list):@count(@$pics);?></b>.<br>&nbsp;</td></tr>
<?
	}

	$nl = true;
	$ol = 0;
	$old = '';
	if(!empty($list) && count($list) && $status_id != '4') {
	if($status_id) {
		if($status_id == '10') {
		echo '<tr><th>Status</th><th>Stad</th><th>Alias</th><th>Nivå</th><th>Längd</th></tr>';
		foreach($list as $row) {
			@$days = @date_diff($row[7].' 23:59:00', date("Y-m-d H:i"));
#name="status_id:'.$row[0].'"
			echo '<input type="hidden" id="status_id:'.$row[0].'" value="'.$row[1].'">';
			echo '<tr class="'.(($row[1] == '2')?'bg_gray':'bg_gray').'">
				<td style="width: 60px; padding: 2px 0 0 4px;" class="nobr"><img src="./_img/status_'.(($row[1] == '1')?'green':'none_1').'.gif" style="margin: 4px 1px 0 0;" id="1:'.$row[0].'" onclick="changeStatus(\'status\', this.id);"><img src="./_img/status_'.(($row[1] == '2')?'red':'none_2').'.gif" style="margin: 4px 0 0 1px;" id="2:'.$row[0].'" onclick="changeStatus(\'status\', this.id);"> <input type="text" readonly value="'.$row[2].'" style="width: 24px; padding: 0; margin-bottom: 4px; line-height: 9px; height: 11px; size: 10px;" onfocus="this.select();" maxlength="5" class="inp_nrm"></td>
				<td class="pdg">'.@$cities[$row[9]].'</td>
				<td class="pdg" style="width: 350px;"><a href="user.php?id='.$row[0].'"><b>'.secureOUT($row[3]).'</b></a></td>
				<td class="pdg">'.$levels[$row[2]].'</td>
				<td class="pdg">'.($row[8] == '0'?'<b>PENDLAR INTE!!!</b>':'<b class="up">'. $days['days'] .'</b> dag'.(($days['days'] == '1')?'':'ar').', <b class="up">'. $days['hours'] .'</b> timm'.(($days['hours'] == '1')?'e':'ar')).'</td>
				<td class="pdg nobr" align="right"><a href="user.php?id='.$row[0].'">VISA</a> | <a href="user.php?del='.$row[0].'&status='.$status_id.'" onclick="return confirm(\'Proceed ?\');">DELETE</a></td>
			</tr>';
		}
		} elseif($status_id == '5') {
		echo '<tr><th>Status</th><th>Alias</th><th>Reggade</th><th>Avreggad</th><th>E-post</th><th>Namn</th></tr>';
		foreach($list as $row) {
			echo '<input type="hidden" name="status_id:'.$row[0].'" id="status_id:'.$row[0].'" value="'.$row[1].'">';
			echo '<tr class="'.(($row[1] == '2')?'bg_gray':'bg_gray').'">
				<td style="width: 60px; padding: 2px 0 0 4px;" class="nobr"><img src="./_img/status_'.(($row[1] == '1')?'green':'none_1').'.gif" style="margin: 4px 1px 0 0;" id="1:'.$row[0].'" onclick="changeStatus(\'status\', this.id);"><img src="./_img/status_'.(($row[1] == '2')?'red':'none_2').'.gif" style="margin: 4px 0 0 1px;" id="2:'.$row[0].'" onclick="changeStatus(\'status\', this.id);"> <input type="text" name="level_id:'.$row[0].'" value="'.$row[2].'" style="width: 24px; padding: 0; margin-bottom: 4px; line-height: 9px; height: 11px; size: 10px;" onfocus="this.select();" maxlength="5" class="inp_nrm"></td>
				<td class="pdg" style="width: 350px;"><a href="user.php?id='.$row[0].'"><b>'.secureOUT($row[3]).'</b></a></td>
				<td class="pdg nobr">'.niceDate($row[10]).'</td>
				<td class="pdg nobr">'.niceDate($row[9]).'</td>
				<td class="pdg">'.secureOUT($row[4]).'</td>
				<td class="pdg">'.secureOUT($row[5].' '.$row[6]).'</td>
				<td class="pdg nobr" align="right"><a href="user.php?id='.$row[0].'">VISA</a> | <a href="user.php?del='.$row[0].'&status='.$status_id.'" onclick="return confirm(\'Proceed ?\');">DELETE</a></td>
			</tr>';
		}
		} elseif($status_id == '3') {
		echo '<tr><th><a href="user.php?status=3&sort='.($sort == 'ASC'?'DESC':'ASC').'">Stad</a></th><th>Alias</th><th><a href="user.php?status=3&sorttype=login&sort='.($sort == 'ASC'?'DESC':'ASC').'">Inloggningslängd</a></th></tr>';
		foreach($list as $row) {
			@$days = @date_diff($row[12], $row[11]);
			#echo '<input type="hidden" name="status_id:'.$row[0].'" id="status_id:'.$row[0].'" value="'.$row[1].'">';
			echo '<tr class="'.(($row[1] == '2')?'bg_gray':'bg_gray').'">
				<td class="pdg">'.@$cities[$row[9]].'</td>
				<td class="pdg" style="width: 350px;"><a href="user.php?id='.$row[0].'"><b>'.secureOUT($row[3]).'</b></a></td>
				<td class="pdg">'.($row[12] != $row[11]?'<b class="up">'. $days['days'] .'</b> dag'.(($days['days'] == '1')?'':'ar').', <b class="up">'. $days['hours'] .'</b> timm'.(($days['hours'] == '1')?'e':'ar').', <b class="up">'. $days['minutes'] .'</b> minut'.(($days['minutes'] == '1')?'':'er'):'<em>loggade in för mindre än 6 min sen.</em>').'</td>
			</tr>';
		}
		} else {
		echo '<tr><th>Status</th><th>Alias</th><th>E-post</th><th>Namn</th></tr>';
		foreach($list as $row) {
			echo '<input type="hidden" name="status_id:'.$row[0].'" id="status_id:'.$row[0].'" value="'.$row[1].'">';
			echo '<tr class="'.(($row[1] == '2')?'bg_gray':'bg_gray').'">
				<td style="width: 60px; padding: 2px 0 0 4px;" class="nobr"><img src="./_img/status_'.(($row[1] == '1')?'green':'none_1').'.gif" style="margin: 4px 1px 0 0;" id="1:'.$row[0].'" onclick="changeStatus(\'status\', this.id);"><img src="./_img/status_'.(($row[1] == '2')?'red':'none_2').'.gif" style="margin: 4px 0 0 1px;" id="2:'.$row[0].'" onclick="changeStatus(\'status\', this.id);"> <input type="text" name="level_id:'.$row[0].'" value="'.$row[2].'" style="width: 24px; padding: 0; margin-bottom: 4px; line-height: 9px; height: 11px; size: 10px;" onfocus="this.select();" maxlength="5" class="inp_nrm"></td>
				<td class="pdg" style="width: 350px;"><a href="user.php?id='.$row[0].'"><b>'.secureOUT($row[3]).'</b></a></td>
				<td class="pdg">'.secureOUT($row[4]).'</td>
				<td class="pdg">'.secureOUT($row[5].' '.$row[6]).'</td>
				<td class="pdg nobr" align="right"><a href="user.php?id='.$row[0].'">VISA</a> | <a href="user.php?del='.$row[0].'&status='.$status_id.'" onclick="return confirm(\'Proceed ?\');">DELETE</a></td>
			</tr>';
		}
		}
	} else {
		echo '<tr><th>E-post</th><th>Aktiveringskod</th><th>Mobilnummer</th><th>Födelsedatum</th><th>Datum</th></tr>';
		foreach($list as $row) {
		echo '<tr class="bg_gray"><input type="hidden" class="inp_nrm" name="status:'.$row[0].'" value="'.secureOUT($row[0]).'" />
			<td class="pdg" style="width: 150px;"><input style="width: 200px;" type="text" class="inp_nrm" name="email:'.$row[0].'" value="'.secureOUT($row[2]).'" /></b></td>
			<td class="pdg"><input style="width: 70px;" type="text" class="inp_nrm" name="code:'.$row[0].'" value="'.secureOUT($row[1]).'" /></td>
			<td class="pdg">'.secureOUT($row[5]).'</td>
			<td class="pdg">'.secureOUT($row[3]).'</td>
			<td class="pdg nobr">'.niceDate($row[4]).'</td>
			<td class="pdg"><input type="checkbox" name="sendemail:'.$row[0].'" value="1" /></td>
			<td class="pdg nobr" align="right"><a href="user.php?del='.$row[0].'&status='.$status_id.'" onclick="return confirm(\'Proceed ?\');">DELETE</a></td>
		</tr>';
		}
	} } else {
?>
<?
		if(!empty($pics)) {
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
'.getadminimg($row[0].$row[8].$row[9]).'<br><a href="user.php?id='.$row[0].'"><b>'.secureOUT($row[3]).'</b></a></td>';
			}
			echo '</tr>';
		} else {
			if(!$change) echo '<tr><td class="pdg"><em>LISTAR INGET.</em></td></tr>';
			else {

echo '	
		<tr>
			<td>
		<table cellspacing="0">
		<tr>
			<td style="padding-right: 20px;">		
<input type="hidden" name="id" value="'.$row['id_id'].'">
<input type="hidden" name="oldalias" value="'.secureOUT($row['u_alias']).'">
<input type="hidden" name="oldemail" value="'.secureOUT($row['u_email']).'">
<input type="hidden" name="domail" value="">
<script type="text/javascript">
function denyAns(val, id, extra) {
	if(!extra) extra = \'\';
	if(confirm(\'Säker ?\'))
		document.location.href = \'user.php?id=\' + id + \'&del_pic=\' + id + \'&reason=\' + val + \'&reasontext=\' + extra;
}
</script>
		<table cellspacing="0">
		<tr>
			<td>'.getadminimg($row['id_id'].$row['u_picid'].$row['u_picd'], 1).'</td>
			<td style="padding-bottom: 5px;"><a href="../user.php?id='.$row['id_id'].'" target="commain" onclick="if(parent.window.opener) parent.window.opener.focus();">Visa profil</a><br /><br /><b>Alternativ:</b><br><a href="javascript:void(0);" onclick="document.getElementById(\'reason_reason:'.$row['id_id'].'\').style.display = \'\';">NEKA</a><br>
<div id="reason_reason:'.$row['id_id'].'" style="display: none;">
<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="R" id="reason_id:'.$row['id_id'].':R"><label for="reason_id:'.$row['id_id'].':R">Reklam</label>
<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="AB" id="reason_id:'.$row['id_id'].':AB"><label for="reason_id:'.$row['id_id'].':AB">Stötande</label><br />
<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="TSB" id="reason_id:'.$row['id_id'].':TSB"><label for="reason_id:'.$row['id_id'].':TSB">Litet ansikte, beskär</label><br />
<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="NF" id="reason_id:'.$row['id_id'].':NF"><label for="reason_id:'.$row['id_id'].':NF">Ej rakt framifrån</label><br />
<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="S" id="reason_id:'.$row['id_id'].':S"><label for="reason_id:'.$row['id_id'].':S">Oskärpa</label>
<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="M" id="reason_id:'.$row['id_id'].':M"><label for="reason_id:'.$row['id_id'].':M">Flera i bild</label><br />
<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="TD" id="reason_id:'.$row['id_id'].':TD"><label for="reason_id:'.$row['id_id'].':TD">Mörk</label>
<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="TL" id="reason_id:'.$row['id_id'].':TL"><label for="reason_id:'.$row['id_id'].':TL">Ljus</label><br />
<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="F" id="reason_id:'.$row['id_id'].':F"><label for="reason_id:'.$row['id_id'].':F">Fel</label>
<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="G" id="reason_id:'.$row['id_id'].':G"><label for="reason_id:'.$row['id_id'].':G">Solglasögon</label><br />
<input type="radio" name="reason_id:'.$row['id_id'].'" onclick="if(this.checked) { document.getElementById(\'reasontext_id:'.$row['id_id'].'\').style.display = \'\'; document.getElementById(\'retb_id:'.$row['id_id'].'\').style.display = \'\'; }" onchange="if(!this.checked) { document.getElementById(\'reasontext_id:'.$row['id_id'].'\').style.display = \'none\'; document.getElementById(\'retb_id:'.$row['id_id'].'\').style.display = \'none\'; }" value="X" id="reason_id:'.$row['id_id'].':X"><label for="reason_id:'.$row['id_id'].':X">Valfri</label><br />
<input type="text" name="reasontext_id:'.$row['id_id'].'" id="reasontext_id:'.$row['id_id'].'" style="width: 100px; display: none;" value="" class="inp_nrm"><br />
<input type="button" class="inp_orgbtn" id="retb_id:'.$row['id_id'].'" style="margin: 0;" style="display: none;" value="skicka valfri" onclick="denyAns(\'X\', \''.$row['id_id'].'\', document.getElementById(\'reasontext_id:'.$row['id_id'].'\').value);" />
</div>
<br>1: STANDARD<br>3: BRONS<br>5: SILVER<br>6: GULD<br>7: STAFF<br>10: ADMIN</td>
		</tr>
		<tr>
			<td><b>Alias:</b><br><input type="text" class="inp_nrm" name="alias" value="'.secureOUT($row['u_alias']).'"></td>
			<td><b>E-post:</b><br><input type="text" class="inp_nrm" name="email" value="'.secureOUT($row['u_email']).'"></td>
		</tr>
		<tr>
			<td><b>Förnamn:</b><br><input type="text" class="inp_nrm" name="fname" value="'.secureOUT($row['u_fname']).'"></td>
			<td><b>Efternamn:</b><br><input type="text" class="inp_nrm" name="sname" value="'.secureOUT($row['u_sname']).'"></td>
		</tr>
		<tr>
			<td><b>Gatuadress:</b><br><input type="text" class="inp_nrm" name="street" value="'.secureOUT($row['u_street']).'"></td>
			<td><b>Postnummer:</b><br><input type="text" class="inp_nrm" name="pstnr" value="'.secureOUT($row['u_pstnr']).'"></td>
		</tr>
		<tr>
			<td><b>Postort:</b><br><input type="text" class="inp_nrm" name="pstort" value="'.secureOUT($row['u_pstort']).'"></td>
			<td><b>Mobil:</b><br><input type="text" class="inp_nrm" name="cell" value="'.secureOUT($row['u_cell']).'"></td>
		</tr>
		<tr>
			<td><b>Kön:</b><br><input type="text" class="inp_nrm" name="sex" value="'.secureOUT($row['u_sex']).'"></td>
			<td><b>Ålder:</b><br><input type="text" class="inp_nrm" name="birth" value="'.secureOUT($row['u_birth']).'"></td>
		</tr>
		<tr>
			<td><b>Nivå:</b><br><input type="text" class="inp_nrm" name="level" value="'.secureOUT($row['level_id']).'"></td>
			<td><b>Nivå innan:</b><br><input type="text" class="inp_nrm" name="oldlevel" value="'.secureOUT($row['level_oldlevel']).'"></td>
		</tr>
		<tr>
			<td><b>Nivå pendlar:</b><br><input type="text" class="inp_nrm" name="pending" value="'.secureOUT($row['level_pending']).'"></td>
			<td><b>Nivå slut:</b><br><input type="text" class="inp_nrm" name="enddate" value="'.secureOUT($row['level_enddate']).'"></td>
		</tr>
		<tr>
			<td colspan="2"><b>Betatestare:</b><br><input type="text" class="inp_nrm" name="beta" value="'.secureOUT($row['beta']).'"></td>
		</tr>
		<tr>
			<td><b>Profilbildsdatum:</b><br><input type="text" class="inp_nrm" name="picdate" value="'.secureOUT($row['u_picdate']).'"></td>
			<td><b>Stadskod: <label title="'; foreach($cities as $key => $val) { echo $key.' = '.$val."\n"; } echo '">?</label></b><br><input type="text" class="inp_nrm" name="city" value="'.secureOUT($row['location_id']).'"></td>
		</tr>
		<tr>
			<td colspan="2"><b>Status:</b> (F = registrerad, 1 = aktiverad, 2 = raderad, 3 = blockerad)<br>
<script type="text/javascript">var this_status = \''.$row['status_id'].'\';
function getInfo(type, toggle) {
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
}
</script>
<input type="text" class="inp_nrm" name="status" maxlength="1" onchange="
if(this.value == \'3\' && this_status != \'3\') {
	if(confirm(\'Vill du skicka ut ett e-postmeddelande till:\n'.$row['u_email'].'\nom blockeringen?\')) {
			getInfo(\'block\');
	}
} else if(this.value == \'1\' && this_status == \'3\') {
	if(confirm(\'Vill du skicka ut ett e-postmeddelande till:\n'.$row['u_email'].'\nom öppningen?\')) {
			getInfo(\'unblock\');
	}
} else { getInfo(\'block\', \'none\'); getInfo(\'unblock\', \'none\'); }" value="'.secureOUT($row['status_id']).'"></td>
		</tr>
	<tbody id="block_info" style="display: none;">
		<tr>
			<td colspan="2"><b>Anledning till blockering:</b><br>MALLAR: <a href="javascript:void(0);" onclick="insertInfo(\'1a\', \'block_reason\');">1A</a><br><textarea name="block_reason" id="block_reason" class="inp_nrm">'.gettxt('email-1a').'</textarea></td>
		</tr>
		<tr>
			<td colspan="2"><b>Disclaimer:</b><br>MALLAR: <a href="javascript:void(0);" onclick="insertInfo(\'2a\', \'block_disc\');">2A</a> - <a href="javascript:void(0);" onclick="insertInfo(\'2b\', \'block_disc\');">2B</a><br><textarea name="block_disc" id="block_disc" class="inp_nrm">'.gettxt('email-2a').'</textarea></td>
		</tr>
	</tbody>
	<tbody id="unblock_info" style="display: none;">
		<tr>
			<td colspan="2"><b>Anledning till öppnande:</b><br>MALLAR: Inga.<br><textarea name="unblock_reason" id="unblock_reason"  class="inp_nrm"></textarea></td>
		</tr>
	</tbody>
		<tr>
			<td><b>Banksaldo:</b><br><input type="text" class="inp_nrm" name="money_count" value="'.secureOUT($row['money_count']).'"></td>
			<td><b>SMS-saldo:</b><br><input type="text" class="inp_nrm" name="msg_count" value="'.secureOUT($row['msg_count']).'"></td>
		</tr>
		<tr>
			<td><b>Lösenord:</b><br><input type="text" class="inp_nrm" name="pass" value="'.secureOUT($row['u_pass']).'"></td>
			<td align="right"><input type="submit" class="inp_orgbtn" value="Uppdatera"></td>
		</tr>
		</table>
			</td>
			<td>
		<table cellspacing="2">
		<tr class="bg_gray"><td colspan="4" class="pdg bld">40 senaste händelser</td></tr>
';
	$v_sql = $sql->query("SELECT sess_id, sess_ip, sess_date, type_inf FROM {$t}usersess WHERE id_id = '".secureINS($row['id_id'])."' ORDER BY main_id DESC LIMIT 40");
$names = array('i' => 'in', 'o' => 'ut', 'f' => '<b>felaktig</b>');
	foreach($v_sql as $val) {
echo '<tr class="bg_gray">
		<td class="pdg nobr">'.niceDate($val[2]).'</td>
		<td class="pdg"><a href="search.php?t&view=s&s='.$val[0].'">'.substr($val[0], 0, 5).'</a></td>
		<td class="pdg"><a href="search.php?t&view=s&s='.$val[1].'">'.$val[1].'</a></td>
		<td class="pdg">'.$names[$val[3]].'</td>
</tr>';
	}
echo '
		</table>
		</td>
	</tr>';

			}
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