<?
	$topic = 'register';
	$complete = false;
	$error = array();
	$msg = array();
	if(!empty($_POST['doit'])) {
		if(!valiField($_POST['ins_alias'], 'user')) {
			$error['alias'] = true;
			$msg[] = 'Felaktigt användarnamn.';
		} else $alias = $_POST['ins_alias'];
		if(empty($error['alias'])) {
			$exists = $sql->queryLine("SELECT status_id FROM {$t}user WHERE u_alias = '".secureINS($alias)."' LIMIT 1");
			if(!empty($exists) && count($exists)) {
				if($exists[0] == '1' || $exists[0] == '3' || $exists[0] == 'F') {
					$error['alias'] = true;
					$msg[] = 'Användarnamnet är upptaget.';
				}
			}
		}
		if(empty($_POST['ins_fname'])) {
			$error['fname'] = true;
			$msg[] = 'Felaktigt förnamn.';
		}
		if(empty($_POST['ins_sname'])) {
			$error['sname'] = true;
			$msg[] = 'Felaktigt efternamn.';
		}

		if(!valiField($_POST['ins_street'], 'street')) {
			$error['street'] = true;
			$msg[] = 'Felaktig gatuadress.';
		}

		if($_POST['ins_sex'] != 'M' && $_POST['ins_sex'] != 'F') {
			$error['sex'] = true;
			$msg[] = 'Felaktigt kön.';
		} else $sex_c = $_POST['ins_sex'];

		if(!empty($_POST['ins_pstnr'])) $_POST['ins_pstnr'] = str_replace(' ', '', $_POST['ins_pstnr']);
		if(!valiField($_POST['ins_pstnr'], 'postnr')) {
			$error['pstnr'] = true;
			$msg[] = 'Felaktigt postnummer.';
		}

		if(empty($error) && !count($error)) {
			$pst = $sql->queryLine("SELECT st_pst, st_lan, st_ort FROM {$t}pst WHERE st_pst = '".secureINS($_POST['ins_pstnr'])."' LIMIT 1");
			if(!count($pst)) {
				$pst = $sql->queryLine("SELECT st_pst, st_lan, st_ort FROM {$t}pst WHERE st_pst LIKE '".substr(secureINS($_POST['ins_pstnr']), 0, -1)."%' LIMIT 1");
				if(!count($pst)) {
					$error['pstnr'] = true;
					$msg[] = 'Felaktigt postnummer.';
				}
			}
			@$pst_ort = $pst[2];
			@$pst_lan = $pst[1];
			@$pst = $pst[0];
		}

		if(strlen($_POST['ins_pass1']) > 15 || strlen($_POST['ins_pass1']) < 5) {
			$error['pass1'] = true;
			$msg[] = 'Felaktigt lösenord. Minst 5, max 15 tecken.';
		}
	/*
		if(empty($_POST['ins_city']) || !array_key_exists($_POST['ins_city'], $cities) || $cities[$_POST['ins_city']] == '?') {
			$error['city'] = true;
			$msg[] = 'Felaktigt stad.';
		}
	*/
		if(empty($_POST['ins_pass2']) || $_POST['ins_pass1'] != $_POST['ins_pass2']) {
			$error['pass1'] = true;
			$error['pass2'] = true;
			$msg[] = 'Lösenorden matchar inte.';
		}

		if(empty($error) && !count($error)) $complete = true;
	} else $sex_c = $sex;
	if($complete) {
		$pstlan = $sql->queryResult("SELECT main_id FROM {$t}pstlan WHERE st_lan = '".secureINS($pst_lan)."' LIMIT 1");
		$sql->queryUpdate("UPDATE {$t}user SET
		u_sex = '$sex_c',
		level_id = '1',
		u_alias = '".secureINS($alias)."',
		u_pstort = '".secureINS($pst_ort)."',
		u_pstlan = '".secureINS($pst_lan)."',
		u_pstlan_id = '".$pstlan."',
		account_date = '".now()."',
		lastonl_date = '".now()."',
		lastlog_date = '".now()."',
		u_regdate = '".now()."',
		u_pass = '".secureINS($_POST['ins_pass1'])."',
		status_id = '1',
		u_regdate = NOW() WHERE id_id = '".secureINS($id_u)."' LIMIT 1");

		$sql->queryUpdate("UPDATE {$t}userinfo SET
		u_fname = '".secureINS($_POST['ins_fname'])."',
		u_sname = '".secureINS($_POST['ins_sname'])."',
		u_pstnr = '".secureINS($pst)."',
		u_street = '".secureINS($_POST['ins_street'])."',
		reg_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."',
		reg_sess = '".secureINS($sql->gc())."',
		reg_code = ''
		WHERE id_id = '".secureINS($id_u)."' LIMIT 1");

		$birth = $sql->queryResult("SELECT u_birth FROM {$t}user WHERE id_id = '".$id_u."' LIMIT 1");
			
		$sql->queryInsert("INSERT INTO {$t}userbirth SET
		id_id = '".$id_u."',
		level_id = '$birth'");
		$sql->queryInsert("INSERT INTO {$t}userlevel SET
		id_id = '".$id_u."',
		level_id = ' ACTIVE SEX$sex LEVEL1 ORT".str_replace('-', '', str_replace(' ', '', $pst_ort))." LÄN".str_replace('-', '', str_replace(' ', '', $pst_lan))."'");
		$sql->logADD($alias, $id_u, 'REG_DONE');
		$user_auth->login_data(array($id_u, $alias, '1', '00', PD, '1', $sex_c, $birth, $pstlan, $pst_ort.', '.$pst_lan, now(), now(), now())); 
/*
$_SESSION['data'] = array('u_pst' => $result[9], 'u_pstlan_id' => $result[8], 'lastlog_date' => $result[10], 'lastonl_date' => $result[11], 'u_regdate' => $result[12], 'status_id' => $result[5], 'id_id' => $result[0], 'u_alias' => $result[1], 'u_sex' => $result[6], 'u_picid' => $result[3], 'u_picd' => $result[4], 'u_birth' => $result[7], 'level_id' => $result[2]);

			$_SESSION['c_i'] = $id_u;
			$_SESSION['cc'] = $_POST['ins_city'];
			cookieSET("TTT", $_SESSION['cc']);
			cookieSET("a65", $alias);
			$c = intval($user->getinfo($id_u, 'login_cnt'));
			$id = $user->setinfo($id_u, 'login_cnt', "'".($c+1)."'");
			if($id[0]) $user->setrel($id[1], 'user_head', $id_u);
			$res = $sql->queryResult("SELECT NOW()");
			$_SESSION['c_d'] = $res;
			$sql->queryInsert("INSERT INTO {$tab['user']}sess SET id_id = '".secureINS($_SESSION['c_i'])."', sess_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."', sess_id = '".secureINS($sql->gc())."', sess_date = NOW(), type_inf = 'i'");
			$sql->queryUpdate("UPDATE {$tab['user']} SET lastlog_date = NOW(), lastonl_date = NOW(), account_date = NOW() WHERE id_id = '".secureINS($_SESSION['c_i'])."'");
			$vis = $sql->queryUpdate("UPDATE {$tab['online']} SET account_date = NOW() WHERE id_id = '".secureINS($_SESSION['c_i'])."' LIMIT 1");
			if(!$vis) {
				$vis = $sql->queryInsert("INSERT INTO {$tab['online']} SET account_date = NOW(), id_id = '".secureINS($_SESSION['c_i'])."'");
			}
*/

		splashACT('Välkommen som medlem!<br />Vänta...', l('main', 'start'), 1000);
	}
	require(DESIGN."head_start.php");
?>
<script type="text/javascript">
function validate(tForm) {
	got_sex = '<?=$sex?>';
	if(tForm.elements['ins_sex'].value != got_sex) {
		if(!confirm('Du har valt fel kön utifrån ditt personnummer. Detta kommer att anmälas och granskas i efterhand.\n\nVill du fortsätta?')) {
			tForm.elements['ins_sex'].focus();
			return false;
		}
	}
	return true;
}
</script>
		<div class="wholeHeader2"><h4>Slutför konto</h4></div>
		<div class="wholeBoxed2">
<form name="r" method="post" action="<?=l('member', 'activate', secureOUT($fid), secureOUT($key))?>" onsubmit="return validate(this);">
			<input type="hidden" name="doit" value="1">
			<table cellspacing="0" width="500" style="height: 150px; margin-bottom: 10px;">
			<tr>
				<td class="pdg bld" style="width: 155px; padding-bottom 12px;">steg 3 av 3</td>
			</tr>
			<tr>
				<td class="pdg" style="height: 100%;">
				<?=safeOUT(gettxt('register-part3'))?>
				<table cellspacing="0" style="margin-top: 6px;">
				<tr>
					<td style="padding: 0 6px 6px 0;"><span class="bld<?=(isset($error['fname']))?'_red':'';?>">förnamn</span><br /><input type="text" class="txt" name="ins_fname" value="<?=(!empty($_POST['ins_fname']))?secureOUT($_POST['ins_fname']):'';?>" /></td>
					<td style="padding-bottom: 6px;"><span class="bld<?=(isset($error['sname']))?'_red':'';?>">efternamn</span><br /><input type="text" class="txt" name="ins_sname" value="<?=(!empty($_POST['ins_sname']))?secureOUT($_POST['ins_sname']):'';?>" /></td>
				</tr>
				<tr>
					<td style="padding: 0 6px 6px 0;"><span class="bld<?=(isset($error['street']))?'_red':'';?>">gatuadress</span><br /><input type="text" class="txt" name="ins_street" value="<?=(!empty($_POST['ins_street']))?secureOUT($_POST['ins_street']):'';?>" /></td>
					<td style="padding-bottom: 6px;"><span class="bld<?=(isset($error['pstnr']))?'_red':'';?>">postnummer</span> (ex 85250)<br /><input type="text" class="txt" name="ins_pstnr" value="<?=(!empty($_POST['ins_pstnr']))?secureOUT($_POST['ins_pstnr']):'';?>" /></td>
				</tr>
				<tr>
					<td style="padding-bottom: 6px;"><span class="bld<?=(isset($error['sex']))?'_red':'';?>">kön</span><br />
				<select name="ins_sex" class="txt">
					<option value="M"<?=($sex_c == 'M')?' selected':'';?>>Kille</option>
					<option value="F"<?=($sex_c == 'F')?' selected':'';?>>Tjej</option>
				</select></td>
					<?/*<td style="padding-bottom: 6px;"><span class="bld<?=(isset($error['city']))?' red':'';?>">stad som förval</span><br /><select name="ins_city" class="txt">
<?
	foreach($cities as $key => $val) {
		if($val != '?')
			echo '<option value="'.$key.'"'.(@$_POST['ins_city'] == $key?' selected':(CITY == $key?' selected':'')).'>'.ucwords(strtolower($val)).'</option>';
	}
?>
					</select></td>*/?>
				</tr>
				<tr>
					<td colspan="2" style="padding-bottom: 6px;"><span class="bld<?=(isset($error['alias']))?'_red':'';?>">önskat alias</span><br /><input type="text" class="txt" name="ins_alias" value="<?=(!empty($_POST['ins_alias']))?secureOUT($_POST['ins_alias']):'';?>" /></td>
				</tr>
				<tr>
					<td style="padding: 0 6px 6px 0;"><span class="bld<?=(isset($error['pass1']))?'_red':'';?>">lösenord</span><br /><input type="password" class="txt" name="ins_pass1" value="" /></td>
					<td style="padding-bottom: 6px;"><span class="bld<?=(isset($error['pass2']))?'_red':'';?>">repetera lösenord</span><br /><input type="password" class="txt" name="ins_pass2" value="" /></td>
				</tr>
				</table>
<?=(!empty($msg) && count($msg))?'<span class="bld">OBS!</span><br />'.implode('<br />', $msg):'';?>
				</td>
			</tr>
			</table>
	<input type="submit" class="btn2_med r" value="slutför" /><br class="clr" />
			</form>
		</div>
<?
	include(DESIGN.'foot_start.php');
?>