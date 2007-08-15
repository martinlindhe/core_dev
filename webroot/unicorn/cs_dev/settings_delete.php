<?
	require_once('config.php');

	if(!empty($_POST['do']) && $l['status_id'] == '1') {
		if(!empty($_POST['CCC'])) {
			if(@$_POST['CCC'] != @$_POST['CC']) {
				errorACT('Lösenorden matchar inte.', l('member', 'settings', 'delete'));
			}
			$exists = $sql->queryResult("SELECT u_pass FROM s_user WHERE id_id = '" . secureINS($l['id_id']) . "' LIMIT 1");
			if(!empty($exists)) {
				if($exists != $_POST['CCC']) {
					errorACT('Felaktigt lösenord.', l('member', 'settings', 'delete'));
				}
			} else {
				errorACT('Felaktigt lösenord.', l('member', 'settings', 'delete'));
			}
			$sql->logADD($l['id_id'], $l['u_alias'], 'REG_DEL');
			$res = $sql->queryResult("SELECT l.level_id FROM s_userlevel l WHERE l.id_id = '".$l['id_id']."' LIMIT 1");
			if(!empty($res)) $sql->queryUpdate("REPLACE INTO s_userlevel_off SET id_id = '".$l['id_id']."', level_id = '".secureINS($res)."'");
			$sql->queryUpdate("DELETE FROM s_userlevel WHERE id_id = '".$l['id_id']."' LIMIT 1");
			$sql->queryUpdate("UPDATE s_user SET status_id = '2', u_picid = '0', u_picvalid = '0', account_date = '0000-00-00 00:00:00', lastonl_date = '0000-00-00 00:00:00' WHERE id_id = '".secureINS($l['id_id'])."' LIMIT 1");
			reloadACT(l('member', 'logout', '1'));
		} else {
			errorACT('Felaktigt lösenord.', l('member', 'settings', 'delete'));
		}
	}

	$page = 'settings_delete';
	require(DESIGN.'head.php');
?>
<div id="mainContent">
	
	<div class="subHead">inställningar</div><br class="clr"/>

	<? makeButton(false, "document.location='settings_presentation.php'", 'icon_settings.png', 'publika'); ?>
	<? makeButton(false, "document.location='settings_fact.php'", 'icon_settings.png', 'fakta'); ?>
	<? makeButton(false, "document.location='settings_theme.php'", 'icon_settings.png', 'tema'); ?>
	<? makeButton(false, "document.location='settings_img.php'", 'icon_settings.png', 'bild'); ?>
	<? makeButton(false, "document.location='settings_personal.php'", 'icon_settings.png', 'personliga'); ?>
	<? makeButton(false, "document.location='settings_subscription.php'", 'icon_settings.png', 'span'); ?>
	<? makeButton(true, "document.location='settings_delete.php'", 'icon_settings.png', 'radera konto'); ?>
	<? makeButton(false, "document.location='settings_vipstatus.php'", 'icon_settings.png', 'VIP'); ?>
	<br class="clr"/>


	<div class="centerMenuBodyWhite">
	<form action="<?=$_SERVER['PHP_SELF']?>" name="d" method="post" onsubmit="return confirm('Säker? Allt information kommer att försvinna!');">
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
	<input type="submit" value="radera mig!" class="btn2_min r" /><br class="clr"/>
	</form>
	</div>
</div>
<?
	include(DESIGN.'foot.php');
?>