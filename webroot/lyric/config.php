<?
	error_reporting(E_ALL);

	require_once('functions_db.php');
	require_once('functions_bands.php');
	require_once('functions_records.php');
	require_once('functions_lyrics.php');
	require_once('functions_moderation.php');

	require_once('functions_session.php');
	require_once('functions_user.php');

	$config['debug'] = true;

	$db1['server']   = 'localhost';
	$db1['port']     = 3306;
	$db1['username'] = 'root';
	$db1['password'] = '';
	$db1['database'] = 'dbLyrics';

	$db = dbOpen($db1);	

	session_start();

	if (!isset($_SESSION['loggedIn'])) {
		/* Init session variables for the new session */
		$_SESSION['loggedIn'] = false;
		$_SESSION['userMode'] = 0;
	}
?>