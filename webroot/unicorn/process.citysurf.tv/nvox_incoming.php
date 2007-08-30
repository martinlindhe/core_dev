<?
	if (empty($_GET['id']) || empty($_GET['d']) || empty($_GET['l'])) die('no input');
	require_once('config.php');
	
	if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ip)) {
		$session->log('nvox_incoming.php accessed by unlisted IP', LOGLEVEL_ERROR);
		die('ip not allowed');
	}

	$_id = $_GET['id'];
	$_days = $_GET['d'];
	$_level = $_GET['l'];

	if (!is_numeric($_id)) {
		//lookup username->id
		$user_db = new DB_MySQLi($config['user_db']);
		$q = 'SELECT id_id FROM s_user WHERE u_alias="'.$db->escape($_id).'" LIMIT 1';
		$_id = $user_db->getOneItem($q);
		if (!$_id) die('wrong uid lookup');
	}

	nvoxHandleIncoming($_id, $_days, $_level);
?>
