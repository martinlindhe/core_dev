<?
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
			if(empty($error) && !count($error)) $complete = true;
		} elseif(!empty($_POST['sub'])) {
			if(strpos($_POST['sub'], 'INTE')) {
				reloadACT('./');
			} else {
				$gotvalid = true;
			}
		}
		if($complete) {
			#$id_u = md5(substr($_POST['Y'], -2).$_POST['m'].$_POST['d'].'-'.$_POST['i'].'SALTHELVEETE'.microtime());
			$start_code = mt_rand(100000, 999999);
			if(!empty($res) && count($res)) {
				if($res[0] == '2') {
					$sql->queryUpdate("UPDATE {$t}user SET status_id = '2' WHERE id_id = '".$res[1]."' LIMIT 1");
				} else {
					$error['email'] = true;
					$msg[] = 'E-postadressen är upptagen. Redan medlem? Glömt lösenordet? Klicka <a href="'.l('member', 'forgot').'" class="bld">här</a> för att få hjälp!';
				}
			}

			$msg = sprintf(gettxt('email_activate'), $start_code, substr(P2B, 0, -1).l('member', 'activate', secureOUT(str_replace('@', '__at__', $_POST['ins_email'])), $start_code));
		$chk = doMail(secureOUT($_POST['ins_email']), 'Din aktiveringskod: '.$start_code, $msg);
			if ($chk) {
				$id_u = $sql->queryInsert("INSERT INTO {$t}user SET
				u_sex = '$sex',
				u_email = '".secureINS($_POST['ins_email'])."',
				u_birth = '".$birth."',
				u_birth_x = '',
				status_id = 'F',
				u_regdate = NOW()");
	
				$sql->queryUpdate("REPLACE INTO {$t}userinfo SET
				u_tempemail = '',
				u_fname = '',
				u_sname = '',
				u_subscr = '',
				u_cell = '".@secureINS(@$_POST['ins_cell'])."',
				u_street = '',
				fake_try = '".@secureINS($_SESSION['tries'])."',
				reg_sess = '".secureINS($sql->gc())."',
				reg_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."',
				reg_code = '".$start_code."',
				id_id = '".secureINS($id_u)."'");

				$sql->logADD('', $id_u, 'REG1');
				$url = l('member', 'activate', secureOUT(str_replace('@', '__at__', $_POST['ins_email'])));
				include('part1b.php');
			} else {
				echo 'Problem med mailutskick. Var god försök igen';
			}
			exit;
			//splashACT('Ett mail har skickats med en aktiveringskod för att aktivera ditt konto!<br><br><input type="button" onclick="document.location.href = \''..'\';" class="b" value="aktivera konto!">');
		}

		include(DESIGN.'head.php');
?>
<script type="text/javascript">
function tryThis(e) {
	if(!e) var e=window.event;
	alert(e);
}
var gotFocus = false; var gotLet1 = ''; var gotLet2 = ''; var gotLet3 = '';
var numbers = new Array(96,97,98,99,100,101,102,103,104,105);
function changeselected(e) {
	if(!e) var e=window.event;
	gotAct = array_search(e['keyCode'], numbers).toString();
	if(gotFocus == '1' && gotAct >= 0) {
		gotAct = array_search(e['keyCode'], numbers).toString();
		gotLet1 = gotLet1 + gotAct;
		if(gotLet1.length >= 4) {
			index = select_search(gotLet1, document.r.Y);
			if(index < 100)
				document.r.Y.selectedIndex = index;
			gotLet1 = '';
			if(index < 100)
				document.r.m.focus();
		}
		return false;
	} else if(gotFocus == '2' && gotAct >= 0) {
		gotLet2 = gotLet2 + gotAct;
		if(gotLet2.length >= 2) {
			index = select_search(gotLet2, document.r.m);
			if(index < 100)
				document.r.m.selectedIndex = index;
			gotLet2 = '';
			if(index < 100)
				document.r.d.focus();
		}
		return false;
	} else if(gotFocus == '3' && gotAct >= 0) {
		gotLet3 = gotLet3 + gotAct;
		if(gotLet3.length >= 2) {
			index = select_search(gotLet3, document.r.d);
			if(index < 100)
				document.r.d.selectedIndex = index;
			gotLet3 = '';
			if(index < 100)
				document.r.i.focus();
		}
		return false;
	}
}
function array_search(val, arr) {
	var i = arr.length;
	while(i--)
		if(arr[i] && arr[i] === val) break;
	return i;
}
function select_search(val, arr) {
	gotIt = false;
	for(i = 0; i < 100; i++) {
		if(!gotIt && arr[i] && arr[i].value.toString() === val.toString()) { gotIt = true; break; }
	}
	return i;
}
document.onkeydown = changeselected;
</script>
		<div class="bigHeader">registrering</div>
			<div class="bigBody">
	<form name="r" method="post" action="<?=l('member', 'register')?>">
	<input type="hidden" name="do" value="1" />
	<table cellspacing="0" width="510" class="mrg" style="margin-bottom: 15px;">
			<tr>
				<td class="pdg bld" style="padding-bottom 12px;">steg 1 av 3</td>
			</tr>
	<tr>
		<td colspan="3"><?=gettxt('register-part1')?></td>
	</tr>
<?#=($gotvalid)?'steg 1 av 3':'&nbsp;';?>
	<tr>
		<td><span class="bld<?=(isset($error['email']))?' red':'';?>">e-post</span><br />&nbsp;<br /><input type="text" class="txt" name="ins_email" value="<?=(!empty($_POST['ins_email']))?secureOUT($_POST['ins_email']):'';?>" /><script type="text/javascript"><?=(empty($_POST) || !count($_POST))?'document.r.ins_email.focus();':'';?></script></td>
		<td><span class="bld<?=(isset($error['cell']))?' red':'';?>">mobilnummer</span> (0701234567)<br /><input type="text" class="txt" name="ins_cell" value="<?=(!empty($_POST['ins_cell']))?secureOUT($_POST['ins_cell']):'';?>" /></td>
		<td><span class="bld<?=(isset($error['pnr']))?' red':'';?>">personnummer</span><br />&nbsp;<br /><nobr>
<select class="txt" name="Y" title="år" style="width: 60px; margin-bottom: 1px;" onfocus="gotFocus = '1';" onblur="gotFocus = '0'; gotLet1 = '';">
<option value="-">-</option>
<?
	if(!empty($_POST['Y']) && is_numeric($_POST['Y'])) $sel = $_POST['Y']; else $sel = false;
	for($i = (date('Y')-18); $i > (date('Y')-80); $i--) {
		$selected = ($sel && $sel == $i)?' selected':'';
echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	}
?></select>
<select name="m" title="månad" class="txt" style="width: 40px; margin-bottom: 1px;" onfocus="gotFocus = '2';" onblur="gotFocus = '0'; gotLet2 = '';">
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
<select name="d" title="dag" class="txt" style="width: 40px; margin-bottom: 1px;" onfocus="gotFocus = '3';" onblur="gotFocus = '0'; gotLet3 = '';">
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
- <input name="i" title="fyra sista" maxlength="4" onfocus="this.select();" type="text" class="txt" style="width: 40px; margin-bottom: 1px;" value="<?=(!empty($_POST['i']) && is_numeric($_POST['i']))?substr($_POST['i'], 0, 4):'';?>" />
</nobr></td>
	</tr>
	<tr>
		<td colspan="3"><input type="checkbox" class="chk" name="ins_agree" id="agree"<?=(!empty($_POST['ins_agree'])?' checked':'')?>><label for="agree"> Jag accepterar <a href="<?=l('text', 'agree', '1')?>" onclick="makeText('<?=l('text', 'agree', '3')?>', 'big'); return false;" target="_blank"><u>användarvillkoren</u></a></label></td>
	</tr>
	<tr>
		<td colspan="3"><br />
<?=(!empty($msg) && count($msg))?'<span class="bld">OBS!</span><br />'.implode('<br />', $msg):'';?>
		</td>
	</tr>
	</table>
	<input type="submit" class="btn2_min r" value="nästa!" /><br class="clr" />
	</form>
			</div>
<?
	include(DESIGN.'foot.php');
?>