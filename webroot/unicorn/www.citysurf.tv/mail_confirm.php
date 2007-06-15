<?
	//"activate" används för att bekräfta ändrad mailaddress vid "bekräfta uppgifter"
	$activate_code = '';
	if (!empty($_GET['activate']) && is_numeric($_GET['activate'])) $activate_code = $_GET['activate'];

	//"regcheck" används vid nyregisrering (och vid ändring av email under inställningar-todo!)
	$regcheck = '';
	if (!empty($_GET['regcheck']) && is_numeric($_GET['regcheck'])) $regcheck = $_GET['regcheck'];

	if (!$activate_code && !$regcheck) die;

	include("_config/online.include.php");

	require(DESIGN.'head.php');

	if ($activate_code) {
		$q = 'SELECT id_id,u_email FROM s_userregfast WHERE activate_code="'.$activate_code.'"';
		$row = $sql->queryLine($q);
	
		if ($row) {
			$q = 'UPDATE tblVerifyUsers SET verified=1 WHERE user_id='.$row[0];
			$sql->queryUpdate($q);
	
			$q = 'UPDATE s_user SET u_email="'.$row[1].'",level_id="'.VIP_LEVEL2.'" WHERE id_id='.$row[0];
			$sql->queryUpdate($q);
			
			$q = 'DELETE FROM s_userregfast WHERE activate_code="'.$activate_code.'"';
			$sql->queryUpdate($q);
	
			addVIP($row[0], VIP_LEVEL2, 7);
	
			$msg = 'Dina uppgifter har bekräftats och du har nu fått en veckas VIP-Deluxe. Logga in på nytt för att den ska träda i kraft!';
		} else {
			$msg = 'Felaktig aktiveringskod!';
		}
	}

?>
<div id="mainContent">

	<div class="bigHeader">Bekräftelse av uppgifter</div>
	<div class="bigBody"><?=$msg?>
	</div>

</div>

<?
	require(DESIGN.'foot.php');
?>
