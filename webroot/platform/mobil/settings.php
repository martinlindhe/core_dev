<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');
?>
	INSTÄLLNINGAR<br/>
	<br/>
	<a href="user_change_image.php">ÄNDRA BILD</a><br/>
	<a href="user_change_password.php">ÄNDRA LÖSENORD</a><br/>
	<a href="user_change_facts.php">ÄNDRA FAKTA</a><br/>

	<a href="user_change_mms_code.php">ÄNDRA MMS-KOD</a><br/>
<?
	//todo: gör mej till admin
	if ($isAdmin) {
		echo 'xx';
	}

	require('design_foot.php');
?>