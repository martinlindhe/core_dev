<?
	require_once('config.php');
	
	if (!$_SESSION['userId']) {
		header('Location: index.php');
		die;
	}

	if (isset($_POST['email'])) {
		saveUserSetting($db, $_SESSION['userId'], 'email', $_POST['email']);
	}

	require('design_head.php');

	echo getInfoField('page_settings');

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';

	echo 'E-mail:<br/>';
	echo '<input size="30" type="text" name="email" value="'.getUserSetting($db, $_SESSION['userId'], 'email').'"/><br/>';

	echo '<input type="submit" value="Save"/>';
	echo '</form><br/>';

	if ($_SESSION['isSuperAdmin']) {
		echo 'Mode: Super admin<br/>';
	} else if ($_SESSION['isAdmin']) {
		echo 'Mode: Administrator<br/>';
	}

	require('design_foot.php');
?>