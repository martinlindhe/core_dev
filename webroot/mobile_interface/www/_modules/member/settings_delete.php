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
			$sql->logADD($l['id_id'], $l['u_alias'], 'REG_DEL');
			$res = $sql->queryResult("SELECT l.level_id FROM {$t}userlevel l WHERE l.id_id = '".$l['id_id']."' LIMIT 1");
			if(!empty($res)) $sql->queryUpdate("REPLACE INTO {$t}userlevel_off SET id_id = '".$l['id_id']."', level_id = '".secureINS($res)."'");
			$sql->queryUpdate("DELETE FROM {$t}userlevel WHERE id_id = '".$l['id_id']."' LIMIT 1");
			$sql->queryUpdate("UPDATE {$t}user SET status_id = '2', u_picid = '0', u_picvalid = '0', account_date = '0000-00-00 00:00:00', lastonl_date = '0000-00-00 00:00:00' WHERE id_id = '".secureINS($l['id_id'])."' LIMIT 1");
			reloadACT(l('member', 'logout', '1'));
		} else {
			errorACT('Felaktigt lösenord.', l('member', 'settings', 'delete'));
		}
	}

	$page = 'settings_delete';
	require(DESIGN.'head.php');
?>
<div id="mainContent">
	
	<img src="/_gfx/ttl_settings.png" alt="Inställningar"/><br/><br/>

	<? makeButton(false, 'goLoc(\''.l('member', 'settings').'\')', 'icon_settings.png', 'publika'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'fact').'\')', 'icon_settings.png', 'fakta'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'img').'\')', 'icon_settings.png', 'bild'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'personal').'\')', 'icon_settings.png', 'personliga'); ?>
	<? makeButton(true, 'goLoc(\''.l('member', 'settings', 'delete').'\')', 'icon_settings.png', 'radera konto'); ?>
	<br/><br/><br/>

	<div class="centerMenuBodyWhite">
	<form action="<?=l('member', 'settings', 'delete')?>" name="d" method="post" onsubmit="return confirm('Säker? Allt information kommer att försvinna!');">
	<div style="padding: 5px;">
	<input type="hidden" name="do" value="1" />
		<table summary="" cellspacing="0">
			<tr>
				<td class="pdg" colspan="2"><?=gettxt('register-delete', 0, 1)?></td>
			</tr>
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