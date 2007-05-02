<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');
?>

	GALLERI - kommentera bild1<br/>
	Uppladdad av test123, igår 14:30<br/>
	<br/>

	<form method="post" action="">
		Kommentar:<br/>
		<textarea></textarea>
	</form>

<?
	require('design_foot.php');
?>