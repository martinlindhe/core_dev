<?
	if(!empty($_POST['do']) && $l['status_id'] == '1') {
		if(!empty($_POST['CCC'])) {
			if(@$_POST['CCC'] != @$_POST['CC']) {
				errorACT('Lösenorden matchar inte.', l('member', 'settings', 'delete'));
			}
		$exists = $sql->queryResult("SELECT u_pass FROM {$t}user WHERE id_id = '" . secureINS($l['id_id']) . "' LIMIT 1");
		if(!empty($exists)) {
			if($exists != $_POST['CCC']) {
				errorACT('Felaktigt lösenord.', l('member', 'settings', 'delete'));
			}
		} else {
			errorACT('Felaktigt lösenord.', l('member', 'settings', 'delete'));
		}
			#$picid = intval($l['u_picid']) - 1;
			#if(strlen($picid) == '1') $picid = '0' . $picid;
		$sql->logADD($l['id_id'], $l['u_alias'], 'REG_DEL');
		$res = $sql->queryResult("SELECT l.level_id FROM {$t}userlevel l WHERE l.id_id = '".$l['id_id']."' LIMIT 1");
		if(!empty($res)) $sql->queryUpdate("REPLACE INTO {$t}userlevel_off SET id_id = '".$l['id_id']."', level_id = '".secureINS($res)."'");
		$sql->queryUpdate("DELETE FROM {$t}userlevel WHERE id_id = '".$l['id_id']."' LIMIT 1");
		$sql->queryUpdate("UPDATE {$t}user SET status_id = '2', u_picid = '0', u_picvalid = '0', account_date = '0000-00-00 00:00:00', lastonl_date = '0000-00-00 00:00:00' WHERE id_id = '".secureINS($l['id_id'])."' LIMIT 1");
		#echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head><meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"></head><body><script type="text/javascript">parent.document.location.href = \'/member/logout/1\';</script>Vänta...</body></html>';
		#exit;
		reloadACT(l('member', 'logout', '1'));
		} else errorACT('Felaktigt lösenord.', l('member', 'settings', 'delete'));
	}

	$page = 'settings_delete';
	require(DESIGN.'head.php');
?>
	<div id="mainContent">
			<div class="mainHeader2"><h4>inställningar - <?=makeMenu($page, $menu)?></h4></div>
			<div class="mainBoxed2"><div style="padding: 5px;">
<script type="text/javascript">
</script>
	<form action="<?=l('member', 'settings', 'delete')?>" name="d" method="post" onsubmit="return confirm('Säker? Allt information kommer att försvinna!');">
<form name="pres" action="" method="post" onsubmit="if(TC_active) TC_VarToHidden();">
	<input type="hidden" name="do" value="1" />
<table cellspacing="0">
<tr>
	<td class="pdg" colspan="2"><?=gettxt('register-delete', 0, 1)?></td>
<tr>
	<td class="pdg"><b>Skriv in ditt nuvarande lösenord:</b><br /><input type="password" class="txt" name="CC" value="" /></td>
	<td class="pdg"><b>Skriv det en gång till:</b><br /><input type="password" class="txt" name="CCC" value="" /></td>
</tr>
</table>
</div>
	<input type="submit" value="radera mig!" class="btn2_med r" />
	</form>
		</div>
	</div>
<?
	include(DESIGN.'foot.php');
?>