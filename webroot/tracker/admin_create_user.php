<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$user_created = false;
	
	if (!empty($_POST['username']) && !empty($_POST['password']) && isset($_POST['mode']) && is_numeric($_POST['mode'])) {
		//Skapa ny user

		$userId = makeNewUser($db, $_POST['username'], $_POST['password'], $_POST['password'], $_POST['mode']);
		if (is_numeric($userId)) {
			$user_created = 'User account '.trim(strip_tags($_POST['username'])).' has been created!';
		} else {
			$user_created = 'Error: '.$userId;
		}
	}

	include('design_head.php');
	
	echo '<h2>Administrative tasks - Create user</h2>';

	echo 'Create a new user<br><br>';
	echo '<form name="newuser" method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo 'User name: <input type="text" name="username"><br>';
	echo 'Password: <input type="text" name="password"><br>';
	echo 'User mode: <br>';
	echo makeRadioButton('newuser', 'mode', '0', '0', 'Normal user', true).'<br>';
	echo makeRadioButton('newuser', 'mode', '1', '1', 'Admin', false).'<br>';
	echo makeRadioButton('newuser', 'mode', '2', '2', 'Super Admin', false).'<br><br>';
	echo '<input type="submit" class="button" value="Create user"><br>';
	echo '</form>';

	include('design_foot.php');
	
	if ($user_created) JS_Alert($user_created);
?>