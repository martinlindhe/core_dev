<?
	require_once('config.php');

	require('design_head.php');

	if (!$session->id) {
		$session->showLoginForm();
	}

	//$session->showInfo();
	
	if ($session->id) {
		echo '<a href="?logout">log out</a><br>';
	}
	
	//$session->save(mt_rand(1,10000), mt_rand(1,10000));
	
	//$session->save('somesetting', '887');
	//$session->save('drejire', 'BZKpspLELELEEL');
	//$kex = $session->read('ke2x');
	//echo 'kex: '.$kex;
	
	$session->editSettings();

	require('design_foot.php');
?>