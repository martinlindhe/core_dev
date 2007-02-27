<?
	require_once('config.php');

	require('design_head.php');

	if (!$session->id) {
		$session->showLoginForm();
	}

	$session->showInfo();
	
	if ($session->id) {
		echo '<a href="?logout">log out</a><br>';
	}
	
	//$session->save('kex', 'med blandade bullar');
	$kex = $session->read('ke2x');
	echo 'kex: '.$kex;


	$db->showProfile($time_start);
	
	require('design_foot.php');
?>