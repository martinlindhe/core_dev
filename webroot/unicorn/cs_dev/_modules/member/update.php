<?
	require("./_set/set_val.php");
	require("./_set/set_mail.php");
	$complete = false;
	$error = array();
	$msg = array();
	if(!empty($_GET['i'])) {
		if(empty($_GET['i']) || !is_md5($_GET['i'])) {
			splashACT('Felaktig användare.');
		}
		$res = $sql->queryLine("SELECT status_id, id_id, u_tempemail, blocked_id FROM {$tab['user']} WHERE id_id = '".secureINS($_GET['i'])."' LIMIT 1");
		if(!empty($res[0]) && $res[0] == '1' && $res[3] == '0') {
			$res[4] = $sql->queryResult("SELECT activate_code FROM {$tab['regfast']} WHERE id_id = '".$res[1]."' LIMIT 1");
			if(empty($_GET['key']) || !is_numeric($_GET['key']) || empty($res[4]) || $res[4] != $_GET['key']) {
				splashACT('Felaktig uppdateringskod.');
			} elseif(!empty($res[2]) && valiField($res[2], 'email')) {
				$complete = true;
				$info = array($res[1], $res[2]);
			} else splashACT('Uppdateringen är redan slutförd.');
		} elseif($res[3] == '1') {
			splashACT('Du är blockerad.');
		} else {
			splashACT('Felaktig användare.');
		}
	}
	if($complete) {
		$sql->queryUpdate("UPDATE {$tab['user']} SET u_email = '".$info[1]."', u_tempemail = '' WHERE id_id = '".$info[0]."' LIMIT 1");
		$sql->queryUpdate("DELETE FROM {$tab['regfast']} WHERE id_id = '".$info[0]."' LIMIT 1");
		splashACT('Din e-postadress är uppdaterad.');
	} else splashACT('Felaktig information.');
?>