<?
	require_once('config.php');

	if (!$session->isAdmin) {
		header('Location: '.$config['start_page']);
		die;
	}

	require('design_head.php');

	wiki('Admin_events');

	$db->showEvents();

	require('design_foot.php');
?>