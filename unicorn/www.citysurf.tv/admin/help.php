<?
	require_once('find_config.php');

	$msg = $db->getOneRow('SELECT text_cmt FROM $text_tab WHERE main_id = "help_'.$db->escape($_GET['id']).'" LIMIT 1');
	if ($msg) {
		$ttl = 'HJÄLP';
	} else {
		$msg = 'Hjälptext finns inte.';
	}

	require('notice_apopup.php');
	die;
?>
