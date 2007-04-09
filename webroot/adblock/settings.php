<?
	require_once('config.php');
	
	if (!$session->id) {
		header('Location: index.php');
		die;
	}

	if (isset($_POST['email'])) {
		$session->save('email', $_POST['email']);
	}

	require('design_head.php');

	wiki('Settings');

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';

	echo 'E-mail:<br/>';
	echo '<input size="30" type="text" name="email" value="'.$session->read('email').'"/><br/>';

	echo '<input type="submit" value="Save"/>';
	echo '</form><br/>';

	if ($session->isAdmin) {
		echo 'Mode: Administrator<br/>';
	}

	require('design_foot.php');
?>