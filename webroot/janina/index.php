<?
	require_once('config.php');

	require('design_head.php');

	$session->showInfo();
	
	//$session->save(mt_rand(1,10000), mt_rand(1,10000));
	
	//$session->save('somesetting', '887');
	//$session->save('drejire', 'BZKpspLELELEEL');
	//$kex = $session->read('ke2x');
	//echo 'kex: '.$kex;
	
	//$db->log('test');
	
	if (!$session->id) {
		echo '<a href="login.php">Admin login</a><br>';
	}

	require('design_foot.php');
?>