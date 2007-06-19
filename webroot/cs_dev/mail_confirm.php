<?
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

	include("_config/online.include.php");

	require(DESIGN.'head.php');

	$q = 'SELECT id_id,u_email FROM s_userregfast WHERE activate_code="'.$code.'"';
	$row = $sql->queryLine($q);
	if (!$row) {
			$msg = 'Felaktig aktiveringskod!';
	} else if ($activate) {
	
		$q = 'UPDATE tblVerifyUsers SET verified=1 WHERE user_id='.$row[0];
		$sql->queryUpdate($q);
	
		$q = 'UPDATE s_user SET u_email="'.$row[1].'",level_id="'.VIP_LEVEL2.'" WHERE id_id='.$row[0];
		$sql->queryUpdate($q);
			
		addVIP($row[0], VIP_LEVEL2, 7);

		$msg = 'Dina uppgifter har bekräftats och du har nu fått 7 dagars VIP-Deluxe.';

	} else if ($update) {

		$q = 'UPDATE s_user SET u_email="'.$row[1].'" WHERE id_id='.$row[0];
		$sql->queryUpdate($q);

		$msg = 'Din addressändring har nu bekräftats.';
	}

	$q = 'DELETE FROM s_userregfast WHERE activate_code="'.$code.'"';
	$sql->queryUpdate($q);

?>
<div id="mainContent">

	<div class="bigHeader">Bekräftelse av uppgifter</div>
	<div class="bigBody"><?=$msg?>
	</div>

</div>
<?
	require(DESIGN.'foot.php');
?>
