<?
	require_once(CONFIG.'secure.fnc.php');
	require_once(CONFIG.'validate.fnc.php');

	require_once('settings.fnc.php');

	require(DESIGN.'head.php');
	
	$q = 'SELECT verified FROM tblVerifyUsers WHERE user_id='.$l['id_id'];
	$data = $sql->queryLine($q, 0, 1);
	if ($data && $data[0] == 1) die;

	define('REGEXP_VALID_EMAIL', '/^(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}$/');
	function ValidEmail($email)
	{
		if (preg_match(REGEXP_VALID_EMAIL, $email)) return true;
		return false;
	}

	/* Returns true if the passed swedish personal number is correct */
	function ValidPersNr($_persnr, $_gender = 0)//fixme: gender support
	{
		$_persnr = str_replace('-', '', $_persnr);

		//year specified in 4 digits
		if (strlen($_persnr) == 12) $_persnr = substr($_persnr, 2);

		if (strlen($_persnr) != 10) return false;

		$sum = calculateSum($_persnr);

		if (substr($_persnr,-1) == $sum) return true;
		return false;
	}

	function calculateSum($_persNr)
	{
		$d2 = 2;
		$sum = 0;

		for ($i=0; $i<=8; $i++) {
			$d1 = intval(substr($_persNr, $i, 1));
			//echo 'd1 = '.$d1.', d2 = '. $d2. ' ... res1 = '. ($d1 * $d2).'<br/>';
			$res1 = ($d1 * $d2);

			if ($res1 >= 10) {
				$x1 = intval(substr($res1, 0, 1));
				$x2 = intval(substr($res1, 1, 1));
				$res1 = $x1 + $x2;
			}
			$sum += $res1;

			if ($d2 == 2) {
				$d2 = 1;
			} else {
				$d2 = 2; //Switch between 212121-212
			}
		}

		//Substract the ones place digit from 10
		$sum = 10 - intval(substr($sum, -1, 1));
		If ($sum == 10) $sum = 0;

		return $sum;
	}

	/* returns true if $_mobil is a valid swedish cellphone number */
	function ValidMobilNr($_mobil)
	{
		$_mobil = str_replace('-', '', $_mobil);
		$_mobil = str_replace(' ', '', $_mobil);

		$prefix = substr($_mobil, 0, 3);
		$number = substr($_mobil, 3);

		$fake_numbers = array('1234567', '0000000', '1111111', '2222222');
		if (in_array($number, $fake_numbers)) return false;

		switch ($prefix) {
			case '070':
			case '073':
			case '075':
			case '076':
				return true;
		}

		return false;
	}

	$l = $user->getuserfill($l, ', u_email, u_pstort, u_pstlan, location_id');
	$l = $user->getuserfillfrominfo($l, ', u_fname, u_sname, u_street, u_pstnr, u_cell');

	$error = '';
	if (!empty($_POST)) {
		
		$persnr = $_POST['persnr_year'].$_POST['persnr_month'].$_POST['persnr_day'].$_POST['persnr_check'];
		
		if (!ValidEmail($_POST['ins_email'])) $error .= '<li>Felaktig epostaddress';
		if (!ValidPersNr($persnr)) $error .= '<li>Felaktigt personnummer';
		if (!ValidMobilNr($_POST['ins_cell'])) $error .= '<li>Felaktigt mobilnummer';

		$pstnr = str_replace(' ', '', $_POST['ins_pstnr']);
		if (!is_numeric($pstnr)) $error .= '<li>Felaktigt postnummer';
		$newpst1 = $newpst2 = '';
		if (@$l['u_pstnr'] != $pstnr && is_numeric($pstnr)) {
			$pst = $sql->queryLine("SELECT a.st_pst, a.st_ort, a.st_lan, b.main_id FROM {$t}pst a, {$t}pstlan b WHERE a.st_pst = '".secureINS($pstnr)."' AND b.st_lan = a.st_lan LIMIT 1");
			if(!count($pst) || empty($pst)) {
				$pst = $sql->queryLine("SELECT a.st_pst, a.st_ort, a.st_lan, b.main_id FROM {$t}pst a, {$t}pstlan b WHERE a.st_pst LIKE '".substr(secureINS($pstnr), 0, -1)."%' AND b.st_lan = a.st_lan LIMIT 1");
				if(!count($pst) || empty($pst)) $error .= '<li>Felaktigt postnummer';
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

		//Spara alla ändringar förrutom ändrad epost-address.
		//todo: spara postnummer
		if (ValidPersNr($persnr)) {
			$q = 'UPDATE s_user SET '.$newpst1.'u_birth="'.secureINS($_POST['persnr_year']).'-'.secureINS($_POST['persnr_month']).'-'.secureINS($_POST['persnr_day']).'",u_birth_x="" WHERE id_id='.$l['id_id'];
			$sql->queryUpdate($q);
		}

		$q = 'UPDATE s_userinfo SET
		'.$newpst2.'
		u_fname = "'.secureINS($_POST['ins_fname']).'",
		u_sname = "'.secureINS($_POST['ins_sname']).'",
		u_street = "'.secureINS($_POST['ins_street']).'",
		u_cell = "'.secureINS($_POST['ins_cell']).'"
		WHERE id_id = "'.secureINS($l['id_id']).'" LIMIT 1';
		$ins = $sql->queryUpdate($q);

		if (!$ins) {
			$q = 'INSERT INTO s_userinfo SET
			u_fname = "'.secureINS($_POST['ins_fname']).'",
			u_sname = "'.secureINS($_POST['ins_sname']).'",
			u_street = "'.secureINS($_POST['ins_street']).'",
			u_cell = "'.secureINS($_POST['ins_cell']).'",
			id_id = "'.secureINS($l['id_id']).'"';
			$sql->queryUpdate($q);
		}

		//Maila en bekräftelse till epost-addressen. Om länk i bekräftelsen klickas så sparas ändrad epost & tblVerifyUsers.verified=1
	
		if (!$error) {
			$start_code = mt_rand(100000, 9999999);
			$r = array($start_code, $l['id_id']);
	
			$q = 'REPLACE INTO s_userregfast SET activate_code = "'.$start_code.'", u_email="'.secureINS($_POST['ins_email']).'", id_id = '.$l['id_id'];
			$sql->queryUpdate($q);
	
			$msg = 'Hej!'."\n\n".
						'Klicka på länken nedan för att slutföra bekräftelse av dina användaruppgifter!.'."\n\n".
						'http://www.citysurf.tv/mail_confirm.php?activate='.$start_code;
	
			$chk = doMail($_POST['ins_email'], 'Validering av uppgifter', $msg);
			
			if ($chk == true) {	
				echo 'Bekräfta dina uppdateringar genom att läsa e-postmeddelandet som skickats ut till <b>'.secureOUT($_POST['ins_email']).'</b>';
			} else {
				echo 'Problem med mailutskick. Var god försök igen';
			}
			include(DESIGN.'foot.php');
			die;
		}
	}

	$l = $user->getuserfill($l, ', u_email, u_pstort, u_pstlan, location_id');
	$l = $user->getuserfillfrominfo($l, ', u_fname, u_sname, u_street, u_pstnr, u_cell');
	
	$q = 'SELECT u_birth, u_birth_x FROM s_user WHERE id_id='.$l['id_id'];
	$birth = $sql->queryLine($q);

	list($persnr_year, $persnr_month, $persnr_day) = explode('-', $birth[0]);

?>
<div id="mainContent">

	<div class="subHead">bekräfta uppgifter</div><br class="clr"/>
<?
	if ($error) echo '<span style="color: red">'.$error.'</span>';
?>
	<div class="bigHeader">Bekräfta följande uppgifter</div>
	<div class="bigBody">
	<form method="post" action="">

	<table summary="" width="500">
		<tr>
			<td style="padding-right: 6px;"><b>Förnamn:</b><br /><input type="text" class="txt" name="ins_fname" value="<?=@secureOUT($l['u_fname'])?>" /></td>
			<td><b>Efternamn:</b><br /><input type="text" class="txt" name="ins_sname" value="<?=@secureOUT($l['u_sname'])?>" /></td>
		</tr>
		<tr>
			<td class="pdg_t"><b>Gatuadress:</b><br /><input type="text" class="txt" name="ins_street" value="<?=@secureOUT($l['u_street'])?>" /></td>
			<td class="pdg_t"><b>Postnummer:</b><br /><input type="text" class="txt" name="ins_pstnr" value="<?=@secureOUT($l['u_pstnr'])?>" /></td>
		</tr>
		<tr>
			<td class="pdg_t"><b>E-post:</b><br /><input type="text" class="txt" name="ins_email" value="<?=@secureOUT($l['u_email'])?>" /></td>
			<td class="pdg_t"><b>Mobilnummer:</b><br /><input type="text" class="txt" name="ins_cell" value="<?=@secureOUT($l['u_cell'])?>" /></td>
		</tr>
		
		<tr>
			<td class="pdg_t">
				<p style="color:#ff0000">
					Obs. sista delen av ditt<br/>
					personnummer <b>sparas inte</b>!.<br/>
					Det behövs bara anges för att vi<br/>
					ska veta din riktiga ålder.
				</p>

				<b>Personnummer:</b><br />
				
<?
	echo '<select name="persnr_year">';
	for ($i=1930; $i<=date('Y'); $i++) echo '<option value="'.$i.'"'.($i==$persnr_year?' selected="selected"':'').'>'.$i.'</option>';
	echo '</select> ';

	echo '<select name="persnr_month">';
	for ($i=1; $i<=12; $i++) echo '<option value="'.($i<10?'0':'').$i.'"'.($i==$persnr_month?' selected="selected"':'').'>'.$i.'</option>';
	echo '</select> ';

	echo '<select name="persnr_day">';
	for ($i=1; $i<=31; $i++) echo '<option value="'.($i<10?'0':'').$i.'"'.($i==$persnr_day?' selected="selected"':'').'>'.$i.'</option>';
	echo '</select> ';
?>
				- <input type="text" name="persnr_check" class="txtsmall" value="<?=$birth[1]?>" size="3"/>
			</td>
			<td class="pdg_t">
				<br/><input type="submit" class="btn2_sml" value="Skicka"/>
			</td>
		</tr>
	</table>
	
	</form>

	</div>
	<br/>

</div>

<?
	include(DESIGN.'foot.php');
?>