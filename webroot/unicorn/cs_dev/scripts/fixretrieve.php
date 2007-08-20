<?
	require_once('../config.php');

	$users = $db->getArray('SELECT id_id FROM s_user WHERE status_id = "1" AND lastonl_date >= "2007-03-20"');

	foreach ($users as $us)
	{
		$gb = $db->getOneItem('SELECT COUNT(*) FROM s_usergb WHERE user_id = '.$us['id_id'].' AND user_read = "0" AND status_id = "1"');
		$id = $user->setinfo($us['id_id'], 'gb_count', $gb);
		
		//check if has got container
		$gotrel = $db->getOneItem("SELECT COUNT(*) FROM s_obj_rel WHERE content_type = 'user_retrieve' AND owner_id = '".$us['id_id']."' AND object_id = '".$id[1]."' LIMIT 1");
		if (!$gotrel) {
			$user->setrel($id[1], 'user_retrieve', $us['id_id']);
		}

		$gb = $db->getOneItem("SELECT COUNT(*) FROM s_usergb WHERE user_id = '".$us['id_id']."' AND status_id = '1'");
		$id = $user->setinfo($us['id_id'], 'gb_offset', $gb);

		//check if has got container
		$gotrel = $db->getOneItem("SELECT COUNT(*) FROM s_obj_rel WHERE content_type = 'user_head' AND owner_id = '".$us['id_id']."' AND object_id = '".$id[1]."' LIMIT 1");
		if (!$gotrel) {
			$user->setrel($id[1], 'user_head', $us['id_id']);
		}

		$gb = $db->getOneItem("SELECT COUNT(*) FROM s_userchat WHERE user_id = '".$us['id_id']."' AND user_read = '0' AND status_id = '1'");
		$id = $user->setinfo($us['id_id'], 'chat_count', $gb);

		//check if has got container
		$gotrel = $db->getOneItem("SELECT COUNT(*) FROM s_obj_rel WHERE content_type = 'user_retrieve' AND owner_id = '".$us['id_id']."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_retrieve', $us['id_id']);
		}

		$gb = $db->getOneItem("SELECT COUNT(*) FROM s_userchat WHERE user_id = '".$us['id_id']."' AND status_id = '1'");
		$id = $user->setinfo($us['id_id'], 'chat_offset', $gb);

		//check if has got container
		$gotrel = $db->getOneItem("SELECT COUNT(*) FROM s_obj_rel WHERE content_type = 'user_head' AND owner_id = '".$us['id_id']."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_head', $us['id_id']);
		}

		$gb = $db->getOneItem("SELECT COUNT(*) FROM s_userphoto WHERE user_id = '".$us['id_id']."' AND status_id = '1'");
		$id = $user->setinfo($us['id_id'], 'gal_offset', $gb);

		//check if has got container
		$gotrel = $db->getOneItem("SELECT COUNT(*) FROM s_obj_rel WHERE content_type = 'user_head' AND owner_id = '".$us['id_id']."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_head', $us['id_id']);
		}

		$gb = $db->getOneItem("SELECT COUNT(*) FROM s_userblog WHERE user_id = '".$us['id_id']."' AND status_id = '1'");
		$id = $user->setinfo($us['id_id'], 'blog_offset', $gb);

		//check if has got container
		$gotrel = $db->getOneItem("SELECT COUNT(*) FROM s_obj_rel WHERE content_type = 'user_head' AND owner_id = '".$us['id_id']."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_head', $us['id_id']);
		}


		$rel_c = $db->getOneItem("SELECT COUNT(*) FROM s_userrelquest a INNER JOIN s_user u ON u.id_id = a.sender_id AND u.status_id = '1' WHERE a.user_id = '".$us['id_id']."' AND a.status_id = '0'");
		$id = $user->setinfo($us['id_id'], 'rel_count', $rel_c);
		
		//check if has got container
		$gotrel = $db->getOneItem("SELECT COUNT(*) FROM s_obj_rel WHERE content_type = 'user_retrieve' AND owner_id = '".$us['id_id']."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_retrieve', $us['id_id']);
		}

		$rel_c = $db->getOneItem("SELECT COUNT(*) FROM s_userrelation INNER JOIN s_user u on u.id_id = friend_id and u.status_id = '1' WHERE user_id = '".$us['id_id']."'");
		$id = $user->setinfo($us['id_id'], 'rel_offset', $rel_c);
		//check if has got container
		$gotrel = $db->getOneItem("SELECT COUNT(*) FROM s_obj_rel WHERE content_type = 'user_head' AND owner_id = '".$us['id_id']."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_head', $us['id_id']);
		}

		$rel_c = 0;

		$gb = $db->getOneItem("SELECT COUNT(*) FROM s_usermail WHERE user_id = '".$us['id_id']."' AND user_read = '0' AND status_id = '1'");
		$id = $user->setinfo($us['id_id'], 'mail_count', $gb);
		
		//check if has got container
		$gotrel = $db->getOneItem("SELECT COUNT(*) FROM s_obj_rel WHERE content_type = 'user_retrieve' AND owner_id = '".$us['id_id']."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_retrieve', $us['id_id']);
		}

		$gb = $db->getOneItem("SELECT COUNT(*) FROM s_usermail WHERE user_id = '".$us['id_id']."' AND status_id = '1'");
		$id = $user->setinfo($us['id_id'], 'mail_offset', $gb);

		//check if has got container
		$gotrel = $db->getOneItem("SELECT COUNT(*) FROM s_obj_rel WHERE content_type = 'user_head' AND owner_id = '".$us['id_id']."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_head', $us['id_id']);
		}

		$id = $user->setinfo($us['id_id'], 'spy_count', $rel_c);
		
		//check if has got container
		$gotrel = $db->getOneItem("SELECT COUNT(*) FROM s_obj_rel WHERE content_type = 'user_retrieve' AND owner_id = '".$us['id_id']."' LIMIT 1");
		if (!$gotrel) {
			$user->setrel($id[1], 'user_retrieve', $us['id_id']);
		}
	}
	die;
?> 
