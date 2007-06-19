<?
	$users = $db->getArray('SELECT id_id, u_pstlan FROM s_user WHERE status_id = "1"');
	foreach($users as $us) {
		$id = $db->getOneItem('SELECT main_id FROM s_pstlan WHERE st_lan = "'.$us['u_pstlan'].'"');
		$gotrel = $db->update('UPDATE s_user SET u_pstlan_id = "'.$id.'" WHERE id_id = '.$us['id_id'].' LIMIT 1');
	}
	die;
?> 