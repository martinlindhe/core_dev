<?
	/*
		Funktioner för att hantera användar-relationer

		Koden från början skriven av Frans Rosén
		Uppdaterad av Martin Lindhe, martin@unicorn.tv, 2007-04-05
	*/

	/* Skickar en förfrågan till $_id om att skapa en relation */
	function sendRelationRequest($_id, $_relation_type)
	{
		global $sql, $user;
		
		if (!is_numeric($_id) || !is_numeric($_relation_type)) return false;

		$r = getset($_POST['ins_rel'], 'r');
		if (!$r) return false; //'Relationen finns inte.';

		$c = $sql->queryResult("SELECT main_id FROM s_userrelation WHERE user_id = '".secureINS($l['id_id'])."' AND friend_id = '".$_id."' LIMIT 1");
		if(!empty($c)) {
			### ÄNDRA!
			$sql->queryUpdate("UPDATE s_userrelquest SET status_id = '2' WHERE user_id = ".$l['id_id']." AND sender_id = ".$_id);
			$sql->queryUpdate("UPDATE s_userrelquest SET status_id = '2' WHERE user_id = ".$_id." AND sender_id = ".$l['id_id']);
			$sql->queryUpdate("DELETE FROM s_userrelation WHERE user_id = ".$l['id_id']." AND friend_id = ".$_id);
			$sql->queryUpdate("DELETE FROM s_userrelation WHERE user_id = ".$_id." AND friend_id = ".$l['id_id']);
			$res = $sql->queryInsert("INSERT s_userrelquest SET
				sent_cmt = '".secureINS($r)."',
				status_id = '0',
				sent_date = NOW(),
				user_id = ".$_id.",
				sender_id = ".$l['id_id']);
			$user->setRelCount($_id);
			$user->setRelCount($l['id_id']);
			return 'Nu har du skickat en förfrågan.';
		} else {
			$c = $sql->queryResult("SELECT COUNT(*) as count FROM s_userrelquest WHERE user_id = ".$l['id_id']." AND sender_id = ".$_id." AND status_id = '0'");
			if($c > 0) return false; //popupACT('Du har redan blivit tillfrågad.');

			$c = $sql->queryResult("SELECT COUNT(*) as count FROM s_userrelquest WHERE user_id = ".$_id." AND sender_id = ".$l['id_id']." AND status_id = '0'");
			if($c > 0) {
				$q = "UPDATE s_userrelquest SET
				sent_cmt = '".secureINS($r)."',
				status_id = '0',
				sent_date = NOW()
				WHERE user_id = ".$_id." AND sender_id = ".$l['id_id']." AND status_id = '0'";
				
				$sql->queryUpdate($q);
				$user->setRelCount($_id);
				$user->setRelCount($l['id_id']);
				return true; //'Nu har du skickat en förfrågan.';
			} else {
				$q = "INSERT INTO s_userrelquest SET
				user_id = ".$_id.",
				sender_id = ".$l['id_id'].",
				sent_cmt = '".secureINS($r)."',
				status_id = '0',
				deleted_id = 0,
				sent_date = NOW()";
				
				$sql->queryInsert($q);
				$user->setRelCount($_id);
				$user->setRelCount($l['id_id']);
				return true; //'Nu har du skickat en förfrågan.';
			}
		}
	}
	
	/* Returns 1 if user1 has user2 on his friend list */
	function areTheyFriends($_id1, $_id2)
	{
		global $sql;
		if (!is_numeric($_id1) || !is_numeric($_id2)) return false;

		return $sql->queryResult("SELECT COUNT(*) as count FROM s_userrelation WHERE user_id = ".$_id1." AND friend_id = ".$_id2." LIMIT 1");
	}
	
	/* Accepterar relation-request $_id */
	function acceptRelationRequest($_id)
	{
		global $sql, $user;
		
		if (!is_numeric($_id)) return false;

		$q = "SELECT sent_cmt, sender_id FROM s_userrelquest WHERE user_id = ".$l['id_id']." AND main_id = ".$_id." AND status_id = '0' LIMIT 1";
		$c = $sql->query($q, 0, 1);
		if(empty($c) || !count($c)) return 'Det finns ingen förfrågan.';
		
		$isFriends = areTheyFriends($l['id_id'], $c[0]['sender_id']);
		if ($isFriends) {
			return 'Ni har redan en relation.';
		}
				
		$q = "INSERT INTO s_userrelation SET
		user_id = ".$l['id_id'].",
		friend_id = ".$c[0]['sender_id'].",
		rel_id = '".secureINS($c[0]['sent_cmt'])."',
		activated_date = NOW()";
		$sql->queryInsert($q);

		$q = "INSERT INTO s_userrelation SET
		user_id = ".$c[0]['sender_id'].",
		friend_id = ".$l['id_id'].",
		rel_id = '".secureINS($c[0]['sent_cmt'])."',
		activated_date = NOW()";
		$sql->queryInsert($q);

		$check = $sql->queryUpdate("UPDATE s_userrelquest SET status_id = '1' WHERE user_id = ".$l['id_id']." AND sender_id = ".$c[0]['sender_id']." AND status_id = '0' LIMIT 1");
		#if($check) sysMSG($u->id, 'Relation', 'Your relation with '.$s->alias.' is accepted!');
		#$user->spy($s['id_id'], $l['id_id'], 'MSG', array('Din relation med <b>'.$l['u_alias'].'</b> har accepterats.'));
		#$user->setRelCount($s['id_id']);
		#$user->setRelCount($l['id_id']);
		#$user->get_cache();
		$user->notifyDecrease('rel', $l['id_id']);
		$user->counterIncrease('rel', $l['id_id']);
		$user->counterIncrease('rel', $c[0]['sender_id']);
		return true;
	}

	/* Removes a relation, or a pending relation request from user $_user_id */
	function removeRelation($_user_id)
	{
		global $sql, $user;

		if (!is_numeric($_user_id)) return false;

		$sql->queryUpdate("UPDATE s_userrelquest SET status_id = 'D' WHERE user_id = ".$l['id_id']." AND sender_id = ".$_user_id);
		$sql->queryUpdate("UPDATE s_userrelquest SET status_id = 'D' WHERE user_id = ".$_user_id." AND sender_id = ".$l['id_id']);
		$sql->queryUpdate("DELETE FROM s_userrelation WHERE user_id = ".$l['id_id']." AND friend_id = ".$_user_id);
		$sql->queryUpdate("DELETE FROM s_userrelation WHERE user_id = ".$_user_id." AND friend_id = ".$l['id_id']);

		$isFriends = areTheyFriends($l['id_id'], $_user_id);
		if ($isFriends) {
			//Ta bort relation
			#sysMSG($s['id_id'], 'Relation', 'Your relation with '.$s->alias.' has ended!');
			#$user->spy($s['id_id'], $l['id_id'], 'MSG', array('Din relation med <b>'.$l['u_alias'].'</b> har avslutats.'));
			#$user->setRelCount($s['id_id']);
			#$user->setRelCount($l['id_id']);
			#$user->get_cache();
			$user->counterDecrease('rel', $_user_id);
			$user->counterDecrease('rel', $l['id_id']);

			return true;
		} else {
			//Ta bort väntande relationsförfrågan
			$q = "SELECT main_id FROM s_userrelquest WHERE user_id = ".$l['id_id']." AND sender_id = ".$_user_id." LIMIT 1";
			$c = $sql->queryResult($q);
			if (!empty($c) && count($c)) {
				$sql->queryUpdate("UPDATE s_userrelquest SET status_id = '2' WHERE user_id = ".$l['id_id']." AND sender_id = ".$_user_id.' AND main_id = '.$c);
				#if(mysql_affected_rows()) sysMSG($s['id_id'], 'Relation', 'Your relation with '.$s->alias.' is denied!');
				$user->notifyDecrease('rel', $l['id_id']);
			} else {
				$c = $sql->queryResult("SELECT main_id FROM s_userrelquest WHERE user_id = ".$_user_id." AND sender_id = ".$l['id_id']." AND status_id = '0' LIMIT 1");
				if (!empty($c) && count($c)) {
					$sql->queryUpdate("UPDATE s_userrelquest SET status_id = '2' WHERE user_id = ".$_user_id." AND sender_id = ".$l['id_id'].' AND main_id = '.$c);
					$user->notifyDecrease('rel', $_user_id);
				}
			}
			#$user->setRelCount($s['id_id']);
			#$user->setRelCount($l['id_id']);
			#$user->get_cache();

			return true;
		}
	}

	function blockRelation($_id)
	{
		global $sql;

		if (!is_numeric($_id)) return false;
		
		$sql->queryInsert("INSERT INTO s_userblock SET rel_id = 'u', user_id = ".$l['id_id'].", friend_id = ".$_id.", activated_date = NOW()");
		$sql->queryInsert("INSERT INTO s_userblock SET rel_id = 'f', user_id = ".$_id.", friend_id = ".$l['id_id'].", activated_date = NOW()");
		return true;
	}

	//id = user-id att sluta blockera
	function unblockRelation($_id)
	{
		global $sql;
		if (!is_numeric($_id)) return false;

		$sql->queryUpdate("DELETE FROM s_userblock WHERE user_id = ".$l['id_id']." AND friend_id = ".$_id." AND rel_id = 'u' LIMIT 1");
		$sql->queryUpdate("DELETE FROM s_userblock WHERE friend_id = ".$l['id_id']." AND user_id = ".$_id." AND rel_id = 'f' LIMIT 1");

		return true;
	}

	/* for pager */
	function getBlockedRelationsCnt()
	{
		global $sql;

		$q = "SELECT COUNT(*) FROM s_userblock b INNER JOIN s_user u ON b.friend_id = u.id_id AND u.status_id = '1' WHERE b.user_id = ".$l['id_id']." AND rel_id = 'u'";
		return $sql->queryResult($q, 0, 1);
	}

	function getBlockedRelations($_limit = '')
	{
		global $db, $user;

		$q = "SELECT b.main_id, b.friend_id, b.activated_date, u.id_id, u.u_alias, u.u_picid, u.u_picd, u.status_id, u.lastonl_date, u.u_sex, u.u_birth, u.level_id FROM s_userblock b INNER JOIN s_user u ON b.friend_id = u.id_id AND u.status_id = '1' WHERE b.user_id = ".$user->id." AND rel_id = 'u'".$_limit;
		return $db->getArray($q);
	}

	function getRelations($_id, $_ord = '', $_start = 0, $_end = 0)
	{
		global $db;
		if (!is_numeric($_id) || !is_numeric($_start) || !is_numeric($_end)) return false;

		$q = 'SELECT rel.main_id, rel.user_id, rel.rel_id, rel.gallx, u.id_id, u.u_alias, u.account_date, u.u_picid, u.u_picd, u.status_id, u.lastonl_date, u.lastlog_date, u.u_sex, u.u_birth, u.level_id FROM s_userrelation rel INNER JOIN s_user u ON u.id_id = rel.friend_id AND u.status_id = "1" WHERE rel.user_id = '.$_id;
		if ($_ord) $q .= ' ORDER BY '.$_ord;
		if ($_start || $_end) $q .= ' LIMIT '.$_start.','.$_end;

		return $db->getArray($q);
	}

	function setGallXStatus($user_id, $other_id, $status)
	{
		global $sql;

		if (!is_numeric($user_id) || !is_numeric($other_id) || !is_numeric($status)) return false;

		$q = 'UPDATE s_userrelation SET gallx='.$status.' WHERE user_id='.$user_id.' AND friend_id='.$other_id;
		$sql->queryUpdate($q);
	}

	//kollar om aktuell user får se $_id's galleri
	function getGallXStatus($_id)
	{
		global $db, $user;
		if (!is_numeric($_id)) return false;
		
		$q = 'SELECT gallx FROM s_userrelation WHERE user_id='.$_id.' AND friend_id='.$user->id;
		return $db->getOneItem($q);
	}

	function getRelationsCount($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT COUNT(*) FROM s_userrelation rel INNER JOIN s_user u ON u.id_id = rel.friend_id AND u.status_id = "1" WHERE rel.user_id = '.$_id;
		return $db->getOneItem($q);
	}

	//returnerar lista med förfrågningar som andra skickat till dig
	function getRelationRequestsToMe()
	{
		global $db, $user;

		$q = 'SELECT q.main_id, q.sent_cmt, q.sent_date, u.id_id, u.u_alias, u.account_date, u.u_picid, u.u_picd, u.status_id, u.lastonl_date, u.u_sex, u.u_birth, u.level_id FROM s_userrelquest q INNER JOIN s_user u ON u.id_id = q.sender_id AND u.status_id = "1" WHERE q.user_id = '.$user->id.' AND q.status_id = "0" ORDER BY q.main_id DESC';
		return $db->getArray($q);
	}

	function getRelationRequestsFromMe()
	{
		global $db, $user;

		$q = 'SELECT q.main_id, q.sent_cmt, q.sent_date, u.id_id, u.u_alias, u.account_date, u.u_picid, u.u_picd, u.status_id, u.lastonl_date, u.u_sex, u.u_birth, u.level_id FROM s_userrelquest q INNER JOIN s_user u ON u.id_id = q.user_id AND u.status_id = "1" WHERE q.sender_id = '.$user->id.' AND q.status_id = "0" ORDER BY q.main_id DESC';
		return $db->getArray($q);
	}

	//Returns a number indicating how many of your friends are currently online
	function relationsOnlineCount()
	{
		global $sql;

		$timeout = date("Y-m-d H:i:s", strtotime('-30 MINUTES'));
		
	
		$q = "SELECT COUNT(rel.friend_id) FROM s_userrelation rel ".
				"INNER JOIN s_user u ON u.id_id = rel.friend_id AND u.status_id = '1' ".
				"WHERE rel.user_id = 1 AND u.account_date > '".$timeout."'";

		return $sql->queryResult($q);
	}
	
	//Returns the X last users online
	//todo: move to user class file
	function getLastUsersOnline($_cnt = 5)
	{
		global $sql;
		if (!is_numeric($_cnt)) return false;

		$q =	'SELECT *,(SELECT sess_date FROM s_usersess WHERE id_id=t1.id_id ORDER BY sess_date DESC LIMIT 1) AS sess_date '.
					'FROM s_user AS t1 ORDER BY sess_date DESC LIMIT 0,'.$_cnt;

		return $sql->query($q, 0, 1);
	}

?>