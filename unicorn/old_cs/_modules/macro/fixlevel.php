<?
	$users = $sql->query("SELECT id_id, status_id FROM {$t}user");
	foreach($users as $us) {
		if($us[1] != '1') {
			$res = $sql->queryResult("SELECT l.level_id FROM {$t}userlevel l WHERE l.id_id = '".$us[0]."' LIMIT 1");
			if(!empty($res)) $sql->queryUpdate("REPLACE INTO {$t}userlevel_off SET id_id = '".$us[0]."', level_id = '".secureINS($res)."'");
			$sql->queryUpdate("DELETE FROM {$t}userlevel WHERE id_id = '".$us[0]."' LIMIT 1");
		}

	}
	exit;
?> 