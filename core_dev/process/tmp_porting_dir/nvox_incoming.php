<?
	require_once('config.php');
	
	if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ip)) {
		$session->log('nvox_incoming.php accessed by unlisted IP', LOGLEVEL_ERROR);

		$l = 'The IP '.$_SERVER['REMOTE_ADDR'].' tried to access '.$_SERVER['SCRIPT_NAME'];
		mail('martin@unicorn.tv', '[NVOX] Unexpected access by unlisted IP', $l);

		die('ip not allowed');
	}

	if (empty($_GET['id']) || empty($_GET['d']) || empty($_GET['l'])) die('no input');

	$_id = $_GET['id'];
	$_days = $_GET['d'];
	$_level = $_GET['l'];

	if (!is_numeric($_id)) {
		//lookup username->id
		$q = 'SELECT id_id FROM s_user WHERE u_alias="'.$db->escape($_id).'" LIMIT 1';
		$_id = $user_db->getOneItem($q);
		if (!$_id) die('wrong uid lookup');
	}

	nvoxHandleIncoming($_id, $_days, $_level);
?>
