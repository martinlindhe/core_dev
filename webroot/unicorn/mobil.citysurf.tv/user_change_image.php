<?
	require_once('config.php');
	$user->requireLoggedIn();

	require('design_head.php');
?>
	ÄNDRA BILD<br/>
	<br/>
	Du kan ladda upp en ny presentationsbild genom att<br/>
	skicka ett MMS till mms@citysurf.tv med rubriken "PRES xxx",
	där xxx är din unika MMS-kod.<br/><br/>
	
	<a href="user_change_mms_code.php">ÄNDRA MMS-KOD</a><br/>

<?
	require('design_foot.php');
?>
