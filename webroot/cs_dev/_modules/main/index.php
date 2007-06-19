<?
	if($action == 'image') {
		require('image.php');
		exit;
	}

	if(!empty($_SESSION['data']['id_id'])) {
	if($action == 'start') {
		$user->logAdd('', '', 'MAIN');
		require('start.php');
		exit;
	} elseif($action == 'thought') {
		require('thought.php');
		exit;
	} elseif($action == 'calendar') {
		require('calendar.php');
		exit;
	} elseif($action == 'speakerscorner') {
		require('speakerscorner.php');
		exit;
	} elseif($action == 'faq') {
		require('faq.php');
		exit;
	} elseif($action == 'poll') {
		require('poll.php');
		exit;
	} elseif($action == 'surfcafe') {
		require('surfcafe.php');
		exit;
	} elseif($action == 'editorial') {
		require('editorial.php');
		exit;
	} elseif($action == 'upgrade') {
		require('upgrade.php');
		exit;
	} }
	if(!empty($_SESSION['data']['id_id'])) reloadACT(l('main', 'start'));
	require('index_p.php');
?>