<?
	/* This script just takes POST variables usr & pwd, processes them and then returns the user at $config['start_page']
		If a login failed, the variable $_SESSION['lastError'] is set */

	include('include_all.php');

	if ($_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	$url = '';
	if (!empty($_GET['u'])) $url = basename(trim( str_replace("\n", '', $_GET['u']) )); //should be immune to sending headerinfo thru $url
	if (!$url) $url = $config['start_page'];

	if (!empty($_POST['usr']) && isset($_POST['pwd'])) {
		$user = $_POST['usr'];
		$pass = $_POST['pwd'];	//pwd is empty if sha1-hash is used

		WriteCookie('usr', $user, 90);	//remembers username in 90 days from last visit

		$userId = 0;
		$error = loginUser($db, $user, $pass);
		if (is_numeric($error)) $userId = $error;
		if (!$userId) {
			$_SESSION['lastError'] = $error;
			logEntry($db, 'Login failed: '.$_SESSION['lastError']);
		}
	}

	header('Location: '.$url);
?>