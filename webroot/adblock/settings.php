<?
	require_once('config.php');
	
	$session->requireLoggedIn();

	require('design_head.php');

	wiki('Settings');
	echo '<br/>';

	$session->editSettings();

	if ($session->isAdmin) {
		echo 'Administrator<br/>';
		
		$db->showConfig();
		echo '<br/>';
		$session->showInfo();
	}
	
	require('design_foot.php');
?>