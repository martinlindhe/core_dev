<?
	require_once('config.php');
	$user->requireLoggedIn();

	require('design_head.php');
?>

	GALLERI - bild1<br/>
	Uppladdad av test123, igår 14:30<br/>
	<br/>
	(bild1)<br/>
	<br/>
	2 kommentarer:<br/>
	test456, igår 14:33 säger:<br/>
	snygg bild<br/>
	<br/>
	test789, idag 19:40 säger:<br/>
	nice!<br/>
	<br/>
	<a href="gallery_comment.php">SKRIV KOMMENTAR</a>

<?
	require('design_foot.php');
?>
