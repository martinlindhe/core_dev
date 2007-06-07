<?
	require_once('config.php');

	if (!empty($_POST['alias']) && !empty($_POST['pass'])) {
		$user_auth->login($_POST['alias'], $_POST['pass'], true);
	}

	require('design_head.php');

	if (isset($_GET['err'])) echo '<span class="critical">FELAKTIG LOGIN!</span><br/><br/>';
?>

	<form method="post" action="login.php">
		ANVÄNDARNAMN:<br/>
		<input type="text" name="alias"/><br/>
		<br/>
		LÖSENORD:<br/>
		<input type="password" name="pass"/><br/>
		<br/>
		<input type="submit" value="LOGGA IN"/>
	</form>
	
<?
	require('design_foot.php');
?>