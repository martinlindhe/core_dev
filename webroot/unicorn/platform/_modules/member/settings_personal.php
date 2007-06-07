<?
	require_once('settings.fnc.php');

	$l = $user->getuserfill($l, ', u_email, u_pstort, u_pstlan, location_id');
	$l = $user->getuserfillfrominfo($l, ', u_fname, u_sname, u_street, u_pstnr, u_cell');
	$settings = $user->getcontent($l['id_id'], 'user_settings');
	$hidlog = $user->getinfo($l['id_id'], 'hidlog');

	$mmskey_error = '';

	if (!empty($_POST['do'])) {
		include(CONFIG.'validate.fnc.php');
		$newpst = '';
		$newemail = '';
		foreach($_POST as $key => $val) {
			$_POST[$key] = trim($val);
		}
		if(empty($_POST['ins_fname']))
			errorACT('Felaktigt förnamn.', l('member', 'settings', 'personal'));
		if(empty($_POST['ins_sname']))
			errorACT('Felaktigt efternamn.', l('member', 'settings', 'personal'));
		if(empty($_POST['ins_street']))
			errorACT('Felaktig gatuadress.', l('member', 'settings', 'personal'));
		if(empty($_POST['ins_pstnr']))
			errorACT('Felaktigt postnummer.', l('member', 'settings', 'personal'));
		if(empty($_POST['ins_cell']))
			errorACT('Felaktigt mobilnummer.', l('member', 'settings', 'personal'));
		$pstnr = str_replace(' ', '', $_POST['ins_pstnr']);
		if(!is_numeric($pstnr))
			errorACT('Felaktigt postnummer.', l('member', 'settings', 'personal'));
		if($l['u_pstnr'] != $pstnr) {
			$newpst = true;
		}
		$newpst1 = '';
		$newpst2 = '';
		if($newpst) {
			$pst = $sql->queryLine("SELECT a.st_pst, a.st_ort, a.st_lan, b.main_id FROM {$t}pst a, {$t}pstlan b WHERE a.st_pst = '".secureINS($pstnr)."' AND b.st_lan = a.st_lan LIMIT 1");
			if(!count($pst) || empty($pst)) {
				$pst = $sql->queryLine("SELECT a.st_pst, a.st_ort, a.st_lan, b.main_id FROM {$t}pst a, {$t}pstlan b WHERE a.st_pst LIKE '".substr(secureINS($pstnr), 0, -1)."%' AND b.st_lan = a.st_lan LIMIT 1");
				if(!count($pst) || empty($pst)) {
					errorACT('Felaktigt postnummer.', l('member', 'settings', 'personal'));
				}
			}
			$pstort = $pst[1];
			$pstlan = $pst[2];
			$pstlan_id = $pst[3];
			$pstnr = $pst[0];
			$_SESSION['data']['u_pst'] = $pstort.', '.$pstlan;
			$_SESSION['data']['u_pstlan_id'] = $pstlan_id;
			$newpst1 = "u_pstort = '" . secureINS($pstort) . "', u_pstlan = '" . secureINS($pstlan) . "', u_pstlan_id = '" . secureINS($pstlan_id) . "',";
			$newpst2 = "u_pstnr = '" . secureINS($pstnr) . "',";
		}
		if(empty($_POST['ins_email']) || !valiField($_POST['ins_email'], 'email')) {
			errorACT('Felaktig e-postadress.', l('member', 'settings', 'personal'));
		}
		if($l['u_email'] != $_POST['ins_email']) {
			$newemail = true;
		}
		$exists = $sql->queryLine("SELECT status_id, id_id FROM {$t}user WHERE u_email = '" . secureINS($_POST['ins_email']) . "' LIMIT 1");
		if(!empty($exists) && count($exists)) {
			if($exists[0] == '1' && $exists[1] != $l['id_id']) {
				errorACT('E-postadressen är upptagen.', l('member', 'settings', 'personal'));
			}
		}
		$fake_cell = array('0701234567', '0731234567', '0731111111', '0732222222');
		$valid_pre = array('070', '073', '075', '076', '010');
		if(in_array($_POST['ins_cell'], $fake_cell)) {
			errorACT('Felaktigt mobilnummer. (Kom igen, bättre kan du!)', l('member', 'settings', 'personal'));
		}
		if(!in_array(substr($_POST['ins_cell'], 0, 3), $valid_pre)) {
			errorACT('Felaktigt mobilnummer.', l('member', 'settings', 'personal'));
		}
		if($newemail) {
			$start_code = mt_rand(100000, 999999);
			$r = array($start_code, $l['id_id']);
			//fixme: länken är felaktig
			$msg = sprintf(gettxt('email_update'), $r[0], P2B.'auth.php?update&go=1&i='.$r[1].'&key=');
			$newemail = "u_tempemail = '" . secureINS($_POST['ins_email']) . "',";
			doMail($_POST['ins_email'], 'Uppdatera din e-postadress', $msg);
			$msg = 'Bekräfta dina uppdateringar genom att läsa e-postmeddelandet som skickats ut till <b>'.secureOUT($_POST['ins_email']).'</b>';
			$sql->queryUpdate("REPLACE INTO {$t}userregfast SET activate_code = '$start_code', id_id = '".$l['id_id']."'");
		}
		$newcity = '';
		$reload = false;

		if (!empty($_POST['ins_opass']) && !empty($_POST['ins_npass']) && !empty($_POST['ins_npass2'])) {
			$error = setNewPassword($_POST['ins_opass'], $_POST['ins_npass'], $_POST['ins_npass2']);
			if ($error !== true) {
				 errorACT($error, l('member', 'settings', 'personal'));
			}
		}

		if(strlen($newemail.$newpst1.$newcity)) $ins = $sql->queryUpdate("UPDATE {$t}user SET
		".substr($newemail.$newpst1.$newcity, 0, -1)."
		WHERE id_id = '".secureINS($l['id_id'])."' LIMIT 1");
		$ins++;
		$ins = $sql->queryUpdate("UPDATE {$t}userinfo SET
		$newpst2
		u_fname = '".secureINS($_POST['ins_fname'])."',
		u_sname = '".secureINS($_POST['ins_sname'])."',
		u_street = '".secureINS($_POST['ins_street'])."',
		u_cell = '".secureINS($_POST['ins_cell'])."'
		WHERE id_id = '".secureINS($l['id_id'])."' LIMIT 1");
		if(!$ins) {
			$sql->queryUpdate("INSERT INTO {$t}userinfo SET
			$newpst2
			u_fname = '".secureINS($_POST['ins_fname'])."',
			u_sname = '".secureINS($_POST['ins_sname'])."',
			u_street = '".secureINS($_POST['ins_street'])."',
			u_cell = '".secureINS($_POST['ins_cell'])."',
			id_id = '".secureINS($l['id_id'])."'");
		}
		$ins++;
		if($newpst) {
			$string = $sql->queryResult("SELECT level_id FROM {$t}userlevel WHERE id_id = '".$l['id_id']."' LIMIT 1");
			$p_lan = str_replace(' ', '', $l['u_pstlan']);
			$p_ort = str_replace(' ', '', $l['u_pstort']);
			$string = str_replace(' LÄN'.$p_lan, '', $string);
			$string = str_replace(' ORT'.$p_ort, '', $string);
			$string = $string.' LÄN'.str_replace('-', '', str_replace(' ', '', $pstlan));
			$string = $string.' ORT'.str_replace('-', '', str_replace(' ', '', $pstort));
			$sql->queryUpdate("UPDATE {$t}userlevel SET level_id = '$string' WHERE id_id = '".$l['id_id']."' LIMIT 1");
		}
		if(!$ins) {
			errorTACT('Någonting gick fel.', l('member', 'settings', 'personal'), 1500);
		}
		if(@$settings['private_chat'] != @$_POST['opt_chat'] || (@$settings['private_chat'] && !$isOk)) {
			$hidden = (!empty($_POST['opt_chat']) && $isOk)?'1':'0';
			$id = $user->setinfo($l['id_id'], 'private_chat', $hidden);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}
		if(@$settings['hidden_view'] != @$_POST['opt_view'] || (@$settings['hidden_view'] && !$isOk)) {
			$hidden = (!empty($_POST['opt_view']) && $isOk)?'1':'0';
			$id = $user->setinfo($l['id_id'], 'hidden_view', $hidden);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}
		if(@$settings['hidden_bview'] != @$_POST['opt_bview'] || (@$settings['hidden_bview'] && !$isOk)) {
			$hidden = (!empty($_POST['opt_bview']) && $isOk)?'1':'0';
			$id = $user->setinfo($l['id_id'], 'hidden_bview', $hidden);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}
		if(@$settings['hidden_pview'] != @$_POST['opt_pview'] || (@$settings['hidden_pview'] && !$isOk)) {
			$hidden = (!empty($_POST['opt_pview']) && $isOk)?'1':'0';
			$id = $user->setinfo($l['id_id'], 'hidden_pview', $hidden);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}
		/*
		if($isAdmin && @$settings['mmsenabled'] != @$_POST['opt_mmsenabled']) {
			$id = $user->setinfo($l['id_id'], 'mmsenabled', @$_POST['opt_mmsenabled']);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}
		if($isAdmin && @$settings['mmstype'] != @$_POST['opt_mmstype']) {
			$hidden = (!empty($_POST['opt_mmstype']) && $_POST['opt_mmstype'] == 'B')?'B':'P';
			$id = $user->setinfo($l['id_id'], 'mmstype', $hidden);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}
		if($isAdmin && @$settings['mmspriv'] != @$_POST['ins_mmspriv']) {
			$hidden = (!empty($_POST['ins_mmspriv']))?'1':'0';
			$id = $user->setinfo($l['id_id'], 'mmspriv', $hidden);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}
		*/
		/*
		if($isAdmin && @$settings['mmskey'] != @$_POST['ins_mmskey']) {
			$id = $user->setinfo($l['id_id'], 'mmskey', @$_POST['ins_mmskey']);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}
		*/
		$mmskey_error = updateMMSKey();
		
		if(@$settings['hidden_slogin'] != @$_POST['opt_shidden']) {
			$hidden = (!empty($_POST['opt_shidden']) && $isOk)?'1':'0';
			$id = $user->setinfo($l['id_id'], 'hidden_slogin', $hidden);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}
		if(@$settings['zoom_auto'] != @$_POST['opt_zoom']) {
			$hidden = (!empty($_POST['opt_zoom']) && $isOk)?'1':'0';
			$id = $user->setinfo($l['id_id'], 'zoom_auto', $hidden);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}
		if(@$settings['hidden_login'] != @$_POST['opt_hidden']) {
			$hidden = (!empty($_POST['opt_hidden']) && $isOk)?'1':'0';
			$id = $user->setinfo($l['id_id'], 'hidden_login', $hidden);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
			$_SESSION['c_h'] = $hidden;
			if($hidden) $_SESSION['c_d'] = 0;
		}
		if($hidlog != @$_POST['opt_hidlog'] || ($hidlog && !$isOk)) {
			$hidden = (!empty($_POST['opt_hidlog']) && $isOk)?'1':'0';
			$id = $user->setinfo($l['id_id'], 'hidlog', $hidden);
			if($id[0]) $user->setrel($id[1], 'user_profile', $l['id_id']);
		}
		if(isset($_POST['opt_hidchat']))
			$hidchat = 0;
		else $hidchat = 1;
		if(@$settings['hidden_chat'] != @$hidchat || (@$settings['hidden_chat'] && !$isOk)) {
			$hidden = (!empty($hidchat) && $isOk)?'1':'0';
			$id = $user->setinfo($l['id_id'], 'hidden_chat', $hidden);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}
		if(@$settings['send_spec'] != @$_POST['opt_spec'] || (@$settings['send_spec'] && !$isOk)) {
			$hidden = (!empty($_POST['opt_spec']))?'1':'0';
			$id = $user->setinfo($l['id_id'], 'send_spec', $hidden);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}
		if(isset($_POST['opt_cell']) && !isset($settings['send_cell']) || @$settings['send_cell'] != @$_POST['opt_cell'] || !isset($settings['send_cell']) && !isset($_POST['opt_cell'])) {
			$hidden = (!empty($_POST['opt_cell']))?'0':'1';
			$id = $user->setinfo($l['id_id'], 'send_cell', $hidden);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}
		if(isset($_POST['opt_email']) && !isset($settings['send_email']) || @$settings['send_email'] != @$_POST['opt_email'] || !isset($settings['send_email']) && !isset($_POST['opt_email'])) {
			$hidden = (!empty($_POST['opt_email']))?'0':'1';
			$id = $user->setinfo($l['id_id'], 'send_email', $hidden);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}
		if(@$settings['random'] != @$_POST['opt_random']) {
			$r = (!empty($_POST['opt_random']))?'0':$sexs[$l['u_sex']];
			if(!$r) {
				if($_POST['opt_random'] == 'M') $r = 'M';
				elseif($_POST['opt_random'] == 'F') $r = 'F';
				else $r = 'B';
			}
			$id = $user->setinfo($l['id_id'], 'random', $r);
			if($id[0]) $user->setrel($id[1], 'user_settings', $l['id_id']);
		}

		if(!empty($msg)) errorACT($msg); else errorTACT('Uppdaterat!', l('member', 'settings', 'personal'), 1000);
	}
	$page = 'settings';
	include(DESIGN.'head.php');
?>
<div id="mainContent">
	
	<div class="subHead">inställningar</div><br class="clr"/>

	<? makeButton(false, 'goLoc(\''.l('member', 'settings').'\')', 'icon_settings.png', 'publika'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'fact').'\')', 'icon_settings.png', 'fakta'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'theme').'\')', 'icon_settings.png', 'tema'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'img').'\')', 'icon_settings.png', 'bild'); ?>
	<? makeButton(true, 'goLoc(\''.l('member', 'settings', 'personal').'\')', 'icon_settings.png', 'personliga'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'subscription').'\')', 'icon_settings.png', 'bevakningar'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'delete').'\')', 'icon_settings.png', 'radera konto'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'vipstatus').'\')', 'icon_settings.png', 'VIP'); ?>
	<br class="clr"/>


	<div class="centerMenuBodyWhite">
	<form action="<?=l('member', 'settings', 'personal')?>" method="post">
	<input type="hidden" name="do" value="1" />
	<div style="padding: 5px;">

	<table summary="" cellspacing="0" width="510">
		<tr>
			<td style="padding-right: 6px;"><b>Förnamn:</b><br /><input type="text" class="txt" name="ins_fname" value="<?=secureOUT($l['u_fname'])?>" /></td>
			<td><b>Efternamn:</b><br /><input type="text" class="txt" name="ins_sname" value="<?=secureOUT($l['u_sname'])?>" /></td>
		</tr>
		<tr>
			<td class="pdg_t"><b>Gatuadress:</b><br /><input type="text" class="txt" name="ins_street" value="<?=secureOUT($l['u_street'])?>" /></td>
			<td class="pdg_t"><b>Postnummer:</b><br /><input type="text" class="txt" name="ins_pstnr" value="<?=secureOUT($l['u_pstnr'])?>" /></td>
		</tr>
		<tr>
			<td class="pdg_t"><b>E-post:</b><br /><input type="text" class="txt" name="ins_email" value="<?=secureOUT($l['u_email'])?>" /></td>
			<td class="pdg_t"><b>Mobilnummer:</b><br /><input type="text" class="txt" name="ins_cell" value="<?=secureOUT($l['u_cell'])?>" /></td>
		</tr>
		<tr>
			<td class="pdg_t" colspan="2"><br /><b>Om du vill byta lösenord, skriv in ditt nuvarande:</b><br /><input type="password" class="txt" name="ins_opass" value="" /></td>
		</tr>
		<tr>
			<td class="pdg_t" style="padding-bottom: 24px;"><b>Nytt lösenord:</b><br /><input type="password" class="txt" name="ins_npass" value="" /></td>
			<td class="pdg_t" style="padding-bottom: 24px;"><b>Upprepa nytt lösenord:</b><br /><input type="password" class="txt" name="ins_npass2" value="" /></td>
		</tr>
		<tr>
			<td colspan="2" class="pdg_t"><b>Kön att slumpa fram:</b><br /><select name="opt_random" class="txt">
				<option value="F"<?=((empty($settings['random'][1]) && $sexs[$l['u_sex']] == 'F') || (!empty($settings['random'][1]) && $settings['random'][1] == 'F'))?' selected':'';?>>Tjejer</option>
				<option value="M"<?=((empty($settings['random'][1]) && $sexs[$l['u_sex']] == 'M') || (!empty($settings['random'][1]) && $settings['random'][1] == 'M'))?' selected':'';?>>Killar</option>
				<option value="B"<?=(!empty($settings['random'][1]) && $settings['random'][1] == 'B')?' selected':'';?>>Båda könen</option>
			</select></td>
		</tr>
		<?
		/*
		<tr>
			<td class="pdg_t" colspan="2" style="padding-top: 12px;"><input type="checkbox" class="chk" name="opt_chat" value="1" id="opt_chat1"<?=(!$isOk)?' disabled':'';?><?=(!empty($settings['private_chat'][1]))?' checked':'';?> /><label for="opt_chat1"> Använd privatchat endast med mina vänner (<img src="<?=OBJ?>6.gif" alt="" title="Guld" />)</label></td>
		</tr>
		<tr>
			<td class="pdg_t" colspan="2"><input type="checkbox" class="chk" name="opt_view" value="1" id="opt_view1"<?=(!$isOk)?' disabled':'';?><?=(!empty($settings['hidden_view'][1]))?' checked':'';?> /><label for="opt_view1"> Dölj mig i besökslogg (<img src="<?=OBJ?>6.gif" alt="" title="Guld" />)</label></td>
		</tr>
		<tr>
			<td class="pdg_t" colspan="2"><input type="checkbox" class="chk" name="opt_bview" value="1" id="opt_bview1"<?=(!$isOk)?' disabled':'';?><?=(!empty($settings['hidden_bview'][1]))?' checked':'';?> /><label for="opt_bview1"> Dölj mig i bloggar (<img src="<?=OBJ?>6.gif" alt="" title="Guld" />)</label></td>
		</tr>
		<tr>
			<td class="pdg_t" colspan="2"><input type="checkbox" class="chk" name="opt_pview" value="1" id="opt_pview1"<?=(!$isOk)?' disabled':'';?><?=(!empty($settings['hidden_pview'][1]))?' checked':'';?> /><label for="opt_pview1"> Dölj mig i fotoalbum (<img src="<?=OBJ?>6.gif" alt="" title="Guld" />)</label></td>
		</tr>
		<tr>
			<td class="pdg_t" colspan="2"><input type="checkbox" class="chk" name="opt_shidden" value="1" id="opt_shidden1"<?=(!$isOk)?' disabled':'';?><?=(!empty($settings['hidden_slogin'][1]))?' checked':'';?> /><label for="opt_shidden1"> Dölj mig i "senaste inloggade" (<img src="<?=OBJ?>6.gif" alt="" title="Guld" />)</label></td>
		</tr>
		<tr>
			<td class="pdg_t" colspan="2"><input type="checkbox" class="chk" name="opt_hidlog" value="1" id="opt_hidlog"<?=(!$isOk)?' disabled':'';?><?=(!empty($hidlog))?' checked':'';?> /><label for="opt_hidlog"> Dold inloggningshistorik (<img src="<?=OBJ?>6.gif" alt="" title="Guld" />)</label></td>
		</tr>
		<tr>
			<td class="pdg_t" colspan="2"><input type="checkbox" class="chk" name="opt_hidchat" value="0" id="opt_hidchat"<?=(!$isOk)?' disabled':'';?><?=(!empty($settings['hidden_chat'][1]))?'':(($isOk)?' checked':'');?> /><label for="opt_hidchat"> Visa historik i privatchat (<img src="<?=OBJ?>6.gif" alt="" title="Guld" />)</label></td>
		</tr>
		<tr>
			<td class="pdg_t" colspan="2"><input type="checkbox" class="chk" name="opt_zoom" value="1" id="opt_zoom"<?=(!$isOk)?' disabled':'';?><?=(!empty($settings['zoom_auto'][1]))?' checked':'';?> /><label for="opt_zoom"> Gå till zoomverktyget automatiskt i vimmel (<img src="<?=OBJ?>6.gif" alt="" title="Guld" />)</label></td>
		</tr>
		*/

			if ($user->vip_check(VIP_LEVEL2))
				echo '<tr><td class="pdg_t" colspan="2"><input type="checkbox" class="chk" name="opt_hidden" value="1" id="opt_hidden1"'.(!$isOk?' disabled':'').(!empty($settings['hidden_login'][1])?' checked':'').'/><label for="opt_hidden1"> Hemlig inloggning (VIP-Delux)</label></td></tr>';
			if ($user->vip_check(VIP_LEVEL1))
				echo '<tr><td class="pdg_t" colspan="2"><input type="checkbox" class="chk" name="opt_spec" value="1" id="opt_spec1"'.(!$isOk?' disabled':'').(!empty($settings['send_spec'][1]) && $isOk?' checked':'').'/><label for="opt_spec1"> Ja, jag vill ha VIP-inbjudningar (VIP)</label></td></tr>';
		?>

		<tr>
			<td class="pdg_t" colspan="2"><input type="checkbox" class="chk" name="opt_cell" value="1" id="opt_cell1"<?=(empty($settings['send_cell'][1]))?' checked':'';?> /><label for="opt_cell1"> Ja, jag vill ha erbjudanden via SMS</label></td>
		</tr>
		<tr>
			<td class="pdg_t" colspan="2"><input type="checkbox" class="chk" name="opt_email" value="1" id="opt_email1"<?=(empty($settings['send_email'][1]))?' checked':'';?> /><label for="opt_email1"> Ja, jag vill ha erbjudanden via e-post</label></td>
		</tr>
<!--
		<tr>
			<td class="pdg_t" style="padding-top: 24px;" colspan="2"><input type="checkbox" class="chk" name="opt_mmsenabled" value="1"<?=(!$isAdmin)?' disabled':'';?> id="opt_mmsenabled1"<?=(!empty($settings['mmsenabled'][1]) && $isAdmin)?' checked':'';?> /><label for="opt_mmsenabled1"> Ja, jag vill skicka MMS från mobil till <select name="opt_mmstype" class="txt" style="width: 100px;"<?=(!$isAdmin)?' disabled':'';?>><option value="B"<?=((empty($settings['mmstype'][1]) || $settings['mmstype'][1] == 'B') && $isAdmin)?' selected':'';?>>min blogg</option><option value="P"<?=((!empty($settings['mmstype'][1]) && $settings['mmstype'][1] == 'P') && $isAdmin)?' selected':'';?>>mitt fotoalbum</option></select> (<img src="<?=OBJ?>10.gif" alt="" title="Admin" />)</label></td>
		</tr>
		<tr>
			<td class="pdg_t" colspan="2"><input type="checkbox" class="chk" name="ins_mmspriv" value="1"<?=(!$isAdmin)?' disabled':'';?> id="opt_mmspriv1"<?=(!empty($settings['mmspriv'][1]) && $isAdmin)?' checked':'';?> /><label for="opt_mmspriv1"> Ja, gör alla mina MMS privata (<img src="<?=OBJ?>10.gif" alt="" title="Admin" />)</label></td>
		</tr>
-->
<?
	if($isAdmin) {
?>
		<tr>
			<td class="pdg_t" colspan="2"><b>MMS-nyckel:</b><br /><input type="text" class="txt" name="ins_mmskey" value="<?=@secureOUT(@$settings['mmskey'][1])?>" /><?=$mmskey_error?></td>
		</tr>
<?
	}
?>
	</table>
	</div>
	<input type="submit" class="btn2_min r" value="spara!" /><br class="clr"/>
	</form>
	</div>
</div>
<?
	include(DESIGN.'foot.php');
	die;
?>