<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');

	echo 'INSTÄLLNINGAR<br/><br/>';

	echo '<div class="mid_content">';
	echo '<a href="user_change_image.php">ÄNDRA BILD</a><br/>';
	echo '<a href="user_change_password.php">ÄNDRA LÖSENORD</a><br/>';
	echo '<a href="user_change_facts.php">ÄNDRA FAKTA</a><br/>';
	echo '<a href="user_change_mms_code.php">ÄNDRA MMS-KOD</a><br/>';
	echo '</div>';

	require('design_foot.php');
?>