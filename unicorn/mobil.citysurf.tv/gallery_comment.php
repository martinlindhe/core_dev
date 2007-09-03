<?
	require_once('config.php');
	$user->requireLoggedIn();

	require('design_head.php');
?>

	GALLERI - kommentera bild1<br/>
	Uppladdad av test123, igår 14:30<br/>
	<br/>

	<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
		Kommentar:<br/>
		<textarea></textarea>
	</form>

<?
	require('design_foot.php');
?>
