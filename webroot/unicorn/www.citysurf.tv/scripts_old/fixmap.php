<?
	$users = $sql->query("SELECT id_id, u_pstlan FROM s_user WHERE status_id = '1'");
	foreach($users as $us) {
		$id = $sql->queryResult("SELECT main_id FROM s_pstlan WHERE st_lan = '".$us[1]."'");
		if ($id) {
			echo 'Updating user '.$us[0].' to lan '.$id.'<br/>';
			$gotrel = $sql->queryResult("UPDATE s_user SET u_pstlan_id = '".$id."' WHERE id_id = '".$us[0]."' LIMIT 1");
		} else {
			echo 'User '.$us[0].' didnt specify lan<br/>';
			$gotrel = $sql->queryResult("UPDATE s_user SET u_pstlan_id = '0' WHERE id_id = '".$us[0]."' LIMIT 1");
		}
	}
	exit;
?>