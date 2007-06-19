<?
	$users = $db->getArray('SELECT id_id, status_id FROM s_user');
	foreach($users as $us) {
		if($us['status_id'] != '1') {
			$res = $db->getOneItem('SELECT level_id FROM s_userlevel WHERE id_id = '.$us['id_id'].' LIMIT 1');
			if(!empty($res)) $db->replace('REPLACE INTO s_userlevel_off SET id_id = '.$us['id_id'].', level_id = "'.secureINS($res).'"');
			$db->delete('DELETE FROM s_userlevel WHERE id_id = '.$us['id_id'].' LIMIT 1');
		}
	}
	die;
?> 