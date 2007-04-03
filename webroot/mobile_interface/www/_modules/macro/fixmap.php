<?
	$users = $sql->query("SELECT id_id, u_pstlan FROM {$t}user WHERE status_id = '1'");
	foreach($users as $us) {
		$id = $sql->queryResult("SELECT main_id FROM {$t}pstlan WHERE st_lan = '".$us[1]."'");
		$gotrel = $sql->queryResult("UPDATE {$t}user SET u_pstlan_id = '".$id."' WHERE id_id = '".$us[0]."' LIMIT 1");
	}
	exit;
?> 