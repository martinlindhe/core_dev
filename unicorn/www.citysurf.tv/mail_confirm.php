<?
	require_once('config.php');

	$update = $activate = false;

	//"activate" används för att bekräfta ändrad mailaddress vid "bekräfta uppgifter"
	if (!empty($_GET['activate']) && is_numeric($_GET['activate'])) {
		$code = $_GET['activate'];
		$activate = true;
	}

	//"update" används vid bekräftelse av ändrad mail vid "ändra inställningar"
	if (!empty($_GET['update']) && is_numeric($_GET['update'])) {
		$code = $_GET['update'];
		$update = true;
	}

	if (empty($code)) die;

	require(DESIGN.'head.php');

	$q = 'SELECT id_id,u_email FROM s_userregfast WHERE activate_code="'.$code.'"';
	$row = $db->getOneRow($q);
	if (!$row) {
			$msg = 'Felaktig aktiveringskod!';
	} else if ($activate) {

		$q = 'UPDATE tblVerifyUsers SET verified=1 WHERE user_id='.$row['id_id'];
		$db->update($q);
	
		$q = 'UPDATE s_user SET u_email="'.$row['u_email'].'",level_id="'.VIP_LEVEL2.'" WHERE id_id='.$row['id_id'];
		$db->update($q);

		//addVIP($row['id_id'], VIP_LEVEL2, 7);
		//$msg = 'Dina uppgifter har bekräftats och du har nu fått 7 dagars VIP-Deluxe.';
		$msg = 'Dina uppgifter har nu bekräftats.';

	} else if ($update) {

		$q = 'UPDATE s_user SET u_email="'.$row['u_email'].'" WHERE id_id='.$row['id_id'];
		$db->update($q);

		$msg = 'Din addressändring har nu bekräftats.';
	}

	$q = 'DELETE FROM s_userregfast WHERE activate_code="'.$code.'"';
	$db->update($q);

?>
<div id="mainContent">

	<div class="bigHeader">Bekräftelse av uppgifter</div>
	<div class="bigBody"><?=$msg?>
	</div>

</div>
<?
	require(DESIGN.'foot.php');
?>
