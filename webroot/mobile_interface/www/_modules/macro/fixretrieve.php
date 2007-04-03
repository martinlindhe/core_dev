<?
	$users = $sql->query("SELECT id_id FROM {$t}user WHERE status_id = '1'");
	foreach($users as $us) {
		$gb = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}usergb WHERE user_id = '".$us[0]."' AND user_read = '0' AND status_id = '1'");
		$id = $user->setinfo($us[0], 'gb_count', intval($gb));
		
		//check if has got container
		$gotrel = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}obj_rel WHERE content_type = 'user_retrieve' AND owner_id = '".$us[0]."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_retrieve', $us[0]);
		}

		$gb = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}usergb WHERE user_id = '".$us[0]."' AND status_id = '1'");
		$id = $user->setinfo($us[0], 'gb_offset', intval($gb));

		//check if has got container
		$gotrel = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}obj_rel WHERE content_type = 'user_head' AND owner_id = '".$us[0]."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_head', $us[0]);
		}

		$gb = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userchat WHERE user_id = '".$us[0]."' AND user_read = '0' AND status_id = '1'");
		$id = $user->setinfo($us[0], 'chat_count', intval($gb));
		
		//check if has got container
		$gotrel = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}obj_rel WHERE content_type = 'user_retrieve' AND owner_id = '".$us[0]."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_retrieve', $us[0]);
		}

		$gb = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userchat WHERE user_id = '".$us[0]."' AND status_id = '1'");
		$id = $user->setinfo($us[0], 'chat_offset', intval($gb));

		//check if has got container
		$gotrel = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}obj_rel WHERE content_type = 'user_head' AND owner_id = '".$us[0]."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_head', $us[0]);
		}

		$gb = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userphoto WHERE user_id = '".$us[0]."' AND status_id = '1'");
		$id = $user->setinfo($us[0], 'gal_offset', intval($gb));

		//check if has got container
		$gotrel = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}obj_rel WHERE content_type = 'user_head' AND owner_id = '".$us[0]."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_head', $us[0]);
		}

		$gb = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userblog WHERE user_id = '".$us[0]."' AND status_id = '1'");
		$id = $user->setinfo($us[0], 'blog_offset', intval($gb));

		//check if has got container
		$gotrel = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}obj_rel WHERE content_type = 'user_head' AND owner_id = '".$us[0]."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_head', $us[0]);
		}


		$rel_c = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userrelquest a INNER JOIN {$t}user u ON u.id_id = a.sender_id AND u.status_id = '1' WHERE a.user_id = '".secureINS($us[0])."' AND a.status_id = '0'");
		$id = $user->setinfo($us[0], 'rel_count', intval($rel_c));
		
		//check if has got container
		$gotrel = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}obj_rel WHERE content_type = 'user_retrieve' AND owner_id = '".$us[0]."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_retrieve', $us[0]);
		}

		$rel_c = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userrelation WHERE user_id = '".secureINS($us[0])."'");
		$id = $user->setinfo($us[0], 'rel_offset', intval($rel_c));
		//check if has got container
		$gotrel = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}obj_rel WHERE content_type = 'user_head' AND owner_id = '".$us[0]."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_head', $us[0]);
		}

		$rel_c = 0;

		$gb = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}usermail WHERE user_id = '".$us[0]."' AND user_read = '0' AND status_id = '1'");
		$id = $user->setinfo($us[0], 'mail_count', intval($gb));
		
		//check if has got container
		$gotrel = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}obj_rel WHERE content_type = 'user_retrieve' AND owner_id = '".$us[0]."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_retrieve', $us[0]);
		}

		$gb = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}usermail WHERE user_id = '".$us[0]."' AND status_id = '1'");
		$id = $user->setinfo($us[0], 'mail_offset', intval($gb));

		//check if has got container
		$gotrel = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}obj_rel WHERE content_type = 'user_head' AND owner_id = '".$us[0]."' AND object_id = '".$id[1]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_head', $us[0]);
		}

		$id = $user->setinfo($us[0], 'spy_count', intval($rel_c));
		
		//check if has got container
		$gotrel = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}obj_rel WHERE content_type = 'user_retrieve' AND owner_id = '".$us[0]."' LIMIT 1");
		if(!$gotrel) {
			$user->setrel($id[1], 'user_retrieve', $us[0]);
		}

	}
	exit;
?> 