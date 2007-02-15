<?
	include('include_all.php');

	if (empty($_GET['error'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$error = htmlentities($_GET['error']);

	include('design_head.php');

	echo getInfoField($db, 'page_errorhelp');
	
	switch ($error) {
		case 'session timeout':
			echo 'The session has timed out. This is caused by too long inactivity. Please log in again.';
			break;
			
		case 'Login failed':
			echo 'You entered the wrong username or password.';
			break;

		default:
			echo 'Todo: error lookup of message: '.$error;
			break;
	}
	
	include('design_foot.php');
?>