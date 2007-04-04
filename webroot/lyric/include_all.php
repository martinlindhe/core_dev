<?
	error_reporting(E_ALL);

	include('functions_db.php');
	include('functions_bands.php');
	include('functions_records.php');
	include('functions_lyrics.php');
	include('functions_moderation.php');

	include('functions_session.php');
	include('functions_user.php');

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