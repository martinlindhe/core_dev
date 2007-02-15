<?
	error_reporting(E_ALL);
	mb_internal_encoding('UTF-8');
	date_default_timezone_set('Europe/Stockholm');

	$time_start = microtime(true);

	include_once('config.php');

	ContinueSession($db);
	
	//login user
	if (!$_SESSION['loggedIn'] && !empty($_POST['usr']) && isset($_POST['pwd'])) {
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

?>