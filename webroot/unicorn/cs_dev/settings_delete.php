<?
	require_once('config.php');
	$user->requireLoggedIn();

	if(!empty($_POST['do']) && $_SESSION['data']['status_id'] == '1') {
		if(!empty($_POST['CCC'])) {
			if(@$_POST['CCC'] != @$_POST['CC']) {
				errorACT('Lösenorden matchar inte.', $_SERVER['PHP_SELF']);
			}
			$exists = $db->getOneItem("SELECT u_pass FROM s_user WHERE id_id = '" .$user->id."' LIMIT 1");
			if(!empty($exists)) {
				if($exists != $_POST['CCC']) {
					errorACT('Felaktigt lösenord.', $_SERVER['PHP_SELF']);
				}
			} else {
				errorACT('Felaktigt lösenord.', $_SERVER['PHP_SELF']);
			}
			//$db->logADD($user->id, $_SESSION['data']['u_alias'], 'REG_DEL');
			$res = $db->getOneItem("SELECT level_id FROM s_userlevel WHERE id_id = '".$user->id."' LIMIT 1");
			if (!empty($res)) $db->replace("REPLACE INTO s_userlevel_off SET id_id = '".$user->id."', level_id = '".$db->escape($res)."'");
			$db->delete("DELETE FROM s_userlevel WHERE id_id = '".$user->id."' LIMIT 1");
			$db->update("UPDATE s_user SET status_id = '2', u_picid = '0', u_picvalid = '0', account_date = '0000-00-00 00:00:00', lastonl_date = '0000-00-00 00:00:00' WHERE id_id = '".$user->id."' LIMIT 1");
			reloadACT('logout.php');
		} else {
			errorACT('Felaktigt lösenord.', $_SERVER['PHP_SELF']);
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
