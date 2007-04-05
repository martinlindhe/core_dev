<?
	require('config.php');

	if (!empty($_POST['alias']) && !empty($_POST['pass'])) {
		$user_auth->login($_POST['alias'], $_POST['pass']);
	}

	require('design_head.php');
?>

	<form method="post" action="">
		ANVÄNDARE: <input type="text" name="alias"/><br/>
		<br/>
		LÖSENORD: <input type="password" name="pass"/><br/>
		<br/>
		<input type="submit" value="LOGGA IN"/>
	</form>
	
<?
	require('design_foot.php');
?>