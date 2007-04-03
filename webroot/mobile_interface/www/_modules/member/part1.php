<?
#splashACT('test');
	if($l) reloadACT(l('main', 'start'));
	$topic = 'register';
		$complete = false;
		$error = array();
		$msg = array();
		$gotvalid = false;
		if(!empty($_POST['do'])) {
			$_SESSION['tries'] = (!empty($_SESSION['tries']))?$_SESSION['tries']:0;
			$gotvalid = true;
			if(empty($_POST['ins_agree'])) {
				$error['agree'] = true;
				$msg[] = 'Du måste godkänna användarvillkoren.';
			}
			if(empty($_POST['ins_email']) || !valiField($_POST['ins_email'], 'email')) {
				$error['email'] = true;
				$msg[] = 'Du måste skriva en godkänd e-postadress.';
			}
			if(empty($error['email'])) {
				$res = $sql->queryLine("SELECT status_id, id_id FROM {$t}user WHERE u_email = '".secureINS(trim($_POST['ins_email']))."' AND status_id != '2' LIMIT 1");
				if($res[0] == '1' || $res[0] == '3') {
					$error['email'] = true;
					$msg[] = 'E-postadressen är upptagen. Redan medlem? Glömt lösenordet? Klicka <a href="'.l('member', 'forgot').'" class="bld">här</a> för att få hjälp!';
				} elseif($res[0] == 'F') {
					$error['email'] = true;
					$msg[] = 'E-postadressen väntar på aktivering. Har du fått ett e-post med en aktiveringskod? Klicka <a href="'.l('member', 'activate').'" class="bld">här</a> för att slutföra din registrering!';
				}
			}
			if(!empty($_POST['ins_cell']) && (!is_numeric($_POST['ins_cell']) || !valiField($_POST['ins_cell'], 'cell'))) {
				$error['cell'] = true;
				$msg[] = 'Du måste skriva ett godkänt mobilnummer.';
			}
			$fake_cell = array('0701234567', '0731234567', '0731111111', '0732222222');
			$valid_pre = array('070', '073', '076', '010');
			if(!empty($_POST['ins_cell']) && (!@$error['cell'] && in_array($_POST['ins_cell'], $fake_cell))) {
				$error['cell'] = true;
				$msg[] = 'Du måste skriva ett godkänt mobilnummer. (Kom igen, bättre kan du!)';
			}
			if(!empty($_POST['ins_cell']) && (!@$error['cell'] && !in_array(substr($_POST['ins_cell'], 0, 3), $valid_pre))) {
				$error['cell'] = true;
				$msg[] = 'Du måste skriva ett godkänt mobilnummer.';
			}
			if(empty($_POST['Y']) || empty($_POST['m']) || empty($_POST['d']) || !is_numeric($_POST['Y'].$_POST['m'].$_POST['d'].$_POST['i'])) {
				$error['pnr'] = true;
				$msg[] = 'Du måste skriva ett godkänt personnummer.';
			}
			$sex = '';
			if(empty($error['pnr'])) {
				if(!valiDate($_POST['Y'], $_POST['m'], $_POST['d'])) {
					$error['pnr'] = true;
					$msg[] = 'Du måste skriva ett godkänt personnummer.';
				} elseif($user->doage($_POST['Y'].'-'.$_POST['m'].'-'.$_POST['d']) <= 17) {
					$_SESSION['tries']++;
					$error['pnr'] = true;
					$msg[] = 'Du måste vara 18 år för att kunna registera dig.';
				} elseif(!valiPnr(substr($_POST['Y'], -2).$_POST['m'].$_POST['d'].'-'.$_POST['i'])) {
					$_SESSION['tries']++;
					$error['pnr'] = true;
					$msg[] = 'Du måste skriva ett godkänt personnummer.';
				} else {
					$birth = $_POST['Y'].'-'.$_POST['m'].'-'.$_POST['d'];
					$sex = valiSex(substr($_POST['Y'], -2).$_POST['m'].$_POST['d'].'-'.$_POST['i']);
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
			if($sex && $sex_c != $sex && empty($_POST['notified'])) {
				$error['sexNotify'] = true;
			}
			if(!empty($_POST['ins_pstnr'])) $_POST['ins_pstnr'] = str_replace(' ', '', $_POST['ins_pstnr']);
			if(!valiField($_POST['ins_pstnr'], 'postnr')) {
				$error['pstnr'] = true;
				$msg[] = 'Felaktigt postnummer.';
			}
			if(empty($error) && !count($error)) {
				$pst = $sql->queryLine("SELECT st_pst, st_lan, st_ort FROM {$t}pst WHERE st_pst = '".secureINS($_POST['ins_pstnr'])."' LIMIT 1");
				if(empty($pst) || !count($pst)) {
					$pst = $sql->queryLine("SELECT st_pst, st_lan, st_ort FROM {$t}pst WHERE st_pst LIKE '".substr(secureINS($_POST['ins_pstnr']), 0, -1)."%' LIMIT 1");
					if(empty($pst) || !count($pst)) {
						$error['pstnr'] = true;
						$msg[] = 'Felaktigt postnummer.';
					}
				}
				@$pst_ort = $pst[2];
				@$pst_lan = $pst[1];
				@$pst = $pst[0];
			}
			if(!valiField($_POST['ins_alias'], 'user')) {
				$error['alias'] = true;
				$msg[] = 'Felaktigt alias.';
			} else $alias = $_POST['ins_alias'];
			if(empty($error['alias'])) {
				$exists = $sql->queryLine("SELECT status_id FROM {$t}user WHERE u_alias = '".secureINS($alias)."' LIMIT 1");
				if(!empty($exists) && count($exists)) {
					if($exists[0] == '1' || $exists[0] == '3' || $exists[0] == 'F') {
						$error['alias'] = true;
						$msg[] = 'Aliaset är upptaget.';
					}
				}
			}
			if(strlen($_POST['ins_pass1']) > 15 || strlen($_POST['ins_pass1']) < 5) {
				$error['pass1'] = true;
				$msg[] = 'Felaktigt lösenord. Minst 5, max 15 tecken.';
			}
			if(empty($_POST['ins_pass2']) || $_POST['ins_pass1'] != $_POST['ins_pass2']) {
				$error['pass1'] = true;
				$error['pass2'] = true;
				$msg[] = 'Lösenorden matchar inte.';
			}

#			if(empty($error) && !count($error)) $complete = true;
			if(empty($error) && !count($error)) $complete = true;
		}
		/*} elseif(!empty($_POST['sub'])) {
			if(strpos($_POST['sub'], 'INTE')) {
				reloadACT('./');
			} else {
				$gotvalid = true;
			}
		}*/
		if($complete) {
#			$id_u = md5(substr($_POST['Y'], -2).$_POST['m'].$_POST['d'].'-'.$_POST['i'].'SALTHELVEETE'.microtime());
#			$start_code = mt_rand(100000, 999999);
			if(!empty($res) && count($res)) {
				if($res[0] == '2') {
					$sql->queryUpdate("UPDATE {$t}user SET status_id = '2' WHERE id_id = '".$res[1]."' LIMIT 1");
				} else {
					$error['email'] = true;
					$msg[] = 'E-postadressen är upptagen. Redan medlem? Glömt lösenordet? Klicka <a href="'.l('member', 'forgot').'" class="bld">här</a> för att få hjälp!';
				}
			}
			if(empty($error) && !count($error)) {
				$pstlan = $sql->queryResult("SELECT main_id FROM {$t}pstlan WHERE st_lan = '".secureINS($pst_lan)."' LIMIT 1");

			$id = $sql->queryInsert("INSERT INTO {$t}user SET
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
			u_sex = '$sex',
			u_email = '".secureINS($_POST['ins_email'])."',
			u_birth = '".$birth."',
			u_birth_x = ''");

			$sql->queryUpdate("REPLACE INTO {$t}userinfo SET
			u_tempemail = '',
			u_subscr = '',
			u_fname = '".secureINS($_POST['ins_fname'])."',
			u_sname = '".secureINS($_POST['ins_sname'])."',
			u_pstnr = '".secureINS($pst)."',
			u_street = '".secureINS($_POST['ins_street'])."',
			reg_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."',
			reg_sess = '".secureINS($sql->gc())."',
			reg_code = ''
			u_cell = '".@secureINS(@$_POST['ins_cell'])."',
			fake_try = '".@secureINS($_SESSION['tries'])."',
			id_id = '".secureINS($id)."'");

			$sql->queryInsert("INSERT INTO {$t}userbirth SET
			id_id = '".$id."',
			level_id = '".$birth."'");

			$sql->queryInsert("REPLACE INTO {$t}userlevel SET
			id_id = '".$id."',
			level_id = ' ACTIVE SEX$sex LEVEL1 ORT".str_replace('-', '', str_replace(' ', '', $pst_ort))." LÄN".str_replace('-', '', str_replace(' ', '', $pst_lan))."'");
			$sql->logADD($alias, $id, 'REG_DONE');
			$user_auth->login_data(array($id, $alias, '1', '00', PD, '1', $sex_c, $birth, $pstlan, $pst_ort.', '.$pst_lan, now(), now(), now())); 
			splashACT('Välkommen som medlem!<br />Vänta...', l('main', 'start'), 1000);
				#$msg = sprintf(gettxt('email_activate'), $start_code, substr(P2B, 0, -1).l('member', 'activate', secureOUT(str_replace('@', '__at__', $_POST['ins_email'])), $start_code));
				#doMail(secureOUT($_POST['ins_email']), 'Din aktiveringskod: '.$start_code, $msg);
				#doMail('member@'.URL, secureOUT($_POST['ins_email']), $msg);
				#$sql->logADD('', $id, 'REG1');
				#$url = l('member', 'activate', secureOUT(str_replace('@', '__at__', $_POST['ins_email'])));
				#include('part1b.php');
				#exit;
			}
			//splashACT('Ett mail har skickats med en aktiveringskod för att aktivera ditt konto!<br /><br /><input type="button" onclick="document.location.href = \''..'\';" class="b" value="aktivera konto!">');
		}
		include(DESIGN.'head_start.php');
	if(isset($error['sexNotify'])) {
?>
<script type="text/javascript">
function notifyAboutSex() {
	got_sex = '<?=@$sex?>';
	r.elements['notified'].value = '1';
	if(r.elements['ins_sex'].value != got_sex) {
		if(!confirm('Du har valt fel kön utifrån ditt personnummer. Detta kommer att anmälas och granskas i efterhand.\n\nVill du fortsätta?')) {
			r.elements['ins_sex'].focus();
			return false;
		} else {
			r.submit();
		}
	}
	return true;
}
</script>
<?
	}
?>
			<div class="wholeHeader2"><h4>registrering</h4></div>
			<div class="wholeBoxed2">
<script type="text/javascript" src="<?=OBJ?>register.js"></script>
	<p><?=gettxt('register-part1')?></p>
	<form name="r" method="post" action="<?=l('member', 'register')?>">
	<input type="hidden" name="do" value="1" />
	<input type="hidden" name="notified" value="<?=(!empty($_POST['notified'])?'1':'0')?>" />
	<table cellspacing="0" class="mrg" style="margin-bottom: 15px;">
	<tr>
		<td><span class="bld<?=(isset($error['email']))?'_red':'';?>">e-post</span><br /><input type="text" class="txt" name="ins_email" value="<?=(!empty($_POST['ins_email']))?secureOUT($_POST['ins_email']):'';?>" /><script type="text/javascript"><?=(empty($_POST) || !count($_POST))?'document.r.ins_email.focus();':'';?></script></td>
		<td class="pdg_l"><span class="bld<?=(isset($error['cell']))?'_red':'';?>">mobilnummer</span> (ex 0701234567)<br /><input type="text" class="txt" name="ins_cell" value="<?=(!empty($_POST['ins_cell']))?secureOUT($_POST['ins_cell']):'';?>" /></td>
		</tr>
		<tr>
			<td colspan="2" class="pdg_t"><span class="bld<?=(isset($error['pnr']))?'_red':'';?>">personnummer</span> (ex 1980 02 23-1234)<br /><nobr>
<select class="txt" name="Y" title="år" style="width: 60px; margin-bottom: 1px;" onchange="r.elements['notified'].value = '0';" onfocus="gotFocus = '1';" onblur="gotFocus = '0'; gotLet1 = '';">
<option value="-">-</option>
<?
	if(!empty($_POST['Y']) && is_numeric($_POST['Y'])) $sel = $_POST['Y']; else $sel = false;
	for($i = (date('Y')-11); $i > (date('Y')-100); $i--) {
		$selected = ($sel && $sel == $i)?' selected':'';
echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	}
?></select>
<select name="m" title="månad" class="txt" style="width: 40px; margin-bottom: 1px;" onchange="r.elements['notified'].value = '0';" onfocus="gotFocus = '2';" onblur="gotFocus = '0'; gotLet2 = '';">
<option value="-">-</option>
<?
	if(!empty($_POST['m']) && is_numeric($_POST['m'])) $sel = $_POST['m']; else $sel = false;
	for($i = 1; $i <= 12; $i++) {
		if(strlen($i) == '1') $i = '0'.$i;
		$selected = ($sel && $sel == $i)?' selected':'';
echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	}
?>
</select>
<select name="d" title="dag" class="txt" style="width: 40px; margin-bottom: 1px;" onchange="r.elements['notified'].value = '0';" onfocus="gotFocus = '3';" onblur="gotFocus = '0'; gotLet3 = '';">
<option value="-">-</option>
<?
	if(!empty($_POST['d']) && is_numeric($_POST['d'])) $sel = $_POST['d']; else $sel = false;
	for($i = 1; $i <= 31; $i++) {
		if(strlen($i) == '1') $i = '0'.$i;
		$selected = ($sel && $sel == $i)?' selected':'';
echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	}
?>
</select>
- <input name="i" title="fyra sista" maxlength="4" onfocus="this.select();" onchange="r.elements['notified'].value = '0';" type="text" class="txt" style="width: 40px; margin-bottom: 1px;" value="<?=(!empty($_POST['i']) && is_numeric($_POST['i']))?substr($_POST['i'], 0, 4):'';?>" />
</nobr></td>
	</tr>
	<tr>
		<td colspan="2">
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
				<select name="ins_sex" class="txt" onchange="this.form.notified.value = '0';">
					<option value="0">Välj</option>
					<option value="M"<?=($sex_c == 'M')?' selected':'';?>>Kille</option>
					<option value="F"<?=($sex_c == 'F')?' selected':'';?>>Tjej</option>
				</select></td>
				</tr>
				<tr>
					<td colspan="2" style="padding-bottom: 6px;"><span class="bld<?=(isset($error['alias']))?'_red':'';?>">valfritt alias</span><br /><input type="text" class="txt" name="ins_alias" value="<?=(!empty($_POST['ins_alias']))?secureOUT($_POST['ins_alias']):'';?>" /></td>
				</tr>
				<tr>
					<td style="padding: 0 6px 6px 0;"><span class="bld<?=(isset($error['pass1']))?'_red':'';?>">lösenord</span><br /><input type="password" class="txt" name="ins_pass1" value="" /></td>
					<td style="padding-bottom: 6px;"><span class="bld<?=(isset($error['pass2']))?'_red':'';?>">repetera lösenord</span><br /><input type="password" class="txt" name="ins_pass2" value="" /></td>
				</tr>
				</table>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="checkbox" class="chk" name="ins_agree" id="agree"<?=(!empty($_POST['ins_agree'])?' checked':'')?>><label for="agree"<?=isset($error['agree'])?' class="bld_red"':''?>> Jag accepterar <a href="<?=l('text', 'agree', '3')?>" onclick="makeText('<?=l('text', 'agree', '3')?>', 'big'); return false;" target="_blank"><u<?=isset($error['agree'])?' class="bld_red"':''?>>användarvillkoren</u></a></label></td>
	</tr>
	<tr>
		<td colspan="2"><br />
<?=(!empty($msg) && count($msg))?'<span class="bld">OBS!</span><br />'.implode('<br />', $msg):'';?>
		</td>
	</tr>
	</table>
	<input type="submit" class="btn2_med r" value="nästa!" /><br class="clr" />
	</form>
<script type="text/javascript">
<?	if(isset($error['sexNotify']) && @$sex) echo 'window.onload = notifyAboutSex;'; ?>
</script>
			</div>
<?
	include(DESIGN.'foot_start.php');
?>