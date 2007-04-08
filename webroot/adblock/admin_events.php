<?
	require_once('config.php');

	if (!$session->isAdmin) {
		header('Location: '.$config['start_page']);
		die;
	}

	require('design_head.php');

	echo getInfoField('page_admin_eventlog');

	$db->showEvents();

	require('design_foot.php');
?>