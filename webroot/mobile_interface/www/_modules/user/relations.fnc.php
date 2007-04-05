<?
	/*
		Funktioner för att hantera användar-relationer

		Koden från början skriven av Frans Rosén
		Uppdaterad av Martin Lindhe, martin@unicorn.tv, 2007-04-05
	*/

	/* Skickar en förfrågan till $_s om att skapa en relation */
	function sendRelationRequest($_relation_type)
	{
		//$s = personen vi kikar på för stunden
		global $sql, $user, $t, $l, $s, $isAdmin;
		
		if (!is_numeric($_relation_type)) return false;

		if($isAdmin) {
			//todo: vad är dealen med denna kod??
			$r = (is_numeric($_POST['ins_rel']))?getset($_POST['ins_rel'], 'r'):$_POST['ins_rel'];
		} else {
			$r = getset($_POST['ins_rel'], 'r');
			if(!$r) return 'Relationen finns inte.';
		}
		$c = $sql->queryResult("SELECT main_id FROM {$t}userrelation WHERE user_id = '".secureINS($l['id_id'])."' AND friend_id = '".secureINS($s['id_id'])."' LIMIT 1");
		if(!empty($c)) {
			### ÄNDRA!
			$sql->queryUpdate("UPDATE {$t}userrelquest SET status_id = '2' WHERE user_id = '".secureINS($l['id_id'])."' AND sender_id = '".secureINS($s['id_id'])."'");
			$sql->queryUpdate("UPDATE {$t}userrelquest SET status_id = '2' WHERE user_id = '".secureINS($s['id_id'])."' AND sender_id = '".secureINS($l['id_id'])."'");
			$sql->queryUpdate("DELETE FROM {$t}userrelation WHERE user_id = '".secureINS($l['id_id'])."' AND friend_id = '".secureINS($s['id_id'])."'");
			$sql->queryUpdate("DELETE FROM {$t}userrelation WHERE user_id = '".secureINS($s['id_id'])."' AND friend_id = '".secureINS($l['id_id'])."'");
			$res = $sql->queryInsert("INSERT {$t}userrelquest SET
				sent_cmt = '".secureINS($r)."',
				status_id = '0',
				sent_date = NOW(),
				user_id = '".secureINS($s['id_id'])."',
				sender_id = '".secureINS($l['id_id'])."'");
			$user->setRelCount($s['id_id']);
			$user->setRelCount($l['id_id']);
			return 'Nu har du skickat en förfrågan.';
		} else {
			$c = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userrelquest WHERE user_id = '".secureINS($l['id_id'])."' AND sender_id = '".secureINS($s['id_id'])."' AND status_id = '0'");
			if($c > 0) popupACT('Du har redan blivit tillfrågad.');

			$c = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userrelquest WHERE user_id = '".secureINS($s['id_id'])."' AND sender_id = '".secureINS($l['id_id'])."' AND status_id = '0'");
			if($c > 0) {
				@mysql_query("UPDATE {$t}userrelquest SET
				sent_cmt = '".secureINS($r)."',
				status_id = '0',
				sent_date = NOW()
				WHERE user_id = '".secureINS($s['id_id'])."' AND sender_id = '".secureINS($l['id_id'])."' AND status_id = '0'");
				$user->setRelCount($s['id_id']);
				$user->setRelCount($l['id_id']);
				return 'Nu har du skickat en förfrågan.';
			} else {
				$sql->queryInsert("INSERT INTO {$t}userrelquest SET
				user_id = '".secureINS($s['id_id'])."',
				sender_id = '".secureINS($l['id_id'])."',
				sent_cmt = '".secureINS($r)."',
				status_id = '0',
				sent_date = NOW()");
				$user->setRelCount($s['id_id']);
				$user->setRelCount($l['id_id']);
				return 'Nu har du skickat en förfrågan.';
			}
		}
	}
	
	/* Returns 1 if user1 has user2 on his friend list */
	function areTheyFriends($_id1, $_id2)
	{
		global $sql;
		if (!is_numeric($_id1) || !is_numeric($_id2)) return false;

		return $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userrelation WHERE user_id = '".$_id1."' AND friend_id = '".$_id2."' LIMIT 1");
	}
	
	/* Accepterar relation-request $_id */
	function acceptRelationRequest($_id, $_other_user_id)
	{
		global $sql, $user, $l, $t;
		
		if (!is_numeric($_id)) return false;

		$c = $sql->queryResult("SELECT sent_cmt FROM {$t}userrelquest WHERE user_id = '".secureINS($l['id_id'])."' AND main_id = '".secureINS($_id)."' AND status_id = '0' LIMIT 1");
		if(empty($c) || !count($c)) {
			return 'Det finns ingen förfrågan.';
		}
		
		$isFriends = areTheyFriends($l['id_id'], $_other_user_id);
		if ($isFriends) {
			return 'Ni har redan en relation.';
		}
				
		$q = "INSERT INTO {$t}userrelation SET
		user_id = '".secureINS($l['id_id'])."',
		friend_id = '".secureINS($_other_user_id)."',
		rel_id = '".secureINS($c)."',
		activated_date = NOW()";
		$sql->queryInsert($q);

		$q = "INSERT INTO {$t}userrelation SET
		user_id = '".secureINS($_other_user_id)."',
		friend_id = '".secureINS($l['id_id'])."',
		rel_id = '".secureINS($c)."',
		activated_date = NOW()";
		$sql->queryInsert($q);

		$check = $sql->queryUpdate("UPDATE {$t}userrelquest SET status_id = '1' WHERE user_id = '".secureINS($l['id_id'])."' AND sender_id = '".secureINS($_other_user_id)."' AND status_id = '0' LIMIT 1");
		#if($check) sysMSG($u->id, 'Relation', 'Your relation with '.$s->alias.' is accepted!');
		#$user->spy($s['id_id'], $l['id_id'], 'MSG', array('Din relation med <b>'.$l['u_alias'].'</b> har accepterats.'));
		#$user->setRelCount($s['id_id']);
		#$user->setRelCount($l['id_id']);
		#$user->get_cache();
		$user->notifyDecrease('rel', $l['id_id']);
		$user->counterIncrease('rel', $l['id_id']);
		$user->counterIncrease('rel', $_other_user_id);
		return true;
	}

	/* Removes a relation, or a pending relation request from user $_user_id */
	function removeRelation($_id, $_user_id)
	{
		global $sql, $user, $l, $t;
		
		if (!is_numeric($_id) || !is_numeric($_user_id)) return false;
		
		$isFriends = areTheyFriends($l['id_id'], $_user_id);
		if($isFriends) {
			//Ta bort relation
			$sql->queryResult("UPDATE {$t}userrelquest SET status_id = 'D' WHERE user_id = '".secureINS($l['id_id'])."' AND sender_id = '".secureINS($_user_id)."'");
			$sql->queryResult("UPDATE {$t}userrelquest SET status_id = 'D' WHERE user_id = '".secureINS($_user_id)."' AND sender_id = '".secureINS($l['id_id'])."'");
			$sql->queryResult("DELETE FROM {$t}userrelation WHERE user_id = '".secureINS($l['id_id'])."' AND friend_id = '".secureINS($_user_id)."'");
			$sql->queryResult("DELETE FROM {$t}userrelation WHERE user_id = '".secureINS($_user_id)."' AND friend_id = '".secureINS($l['id_id'])."'");
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
			$sql->queryResult("UPDATE {$t}userrelquest SET status_id = 'D' WHERE user_id = '".secureINS($l['id_id'])."' AND sender_id = '".secureINS($_user_id)."'");
			$sql->queryResult("UPDATE {$t}userrelquest SET status_id = 'D' WHERE user_id = '".secureINS($_user_id)."' AND sender_id = '".secureINS($l['id_id'])."'");
			$sql->queryResult("DELETE FROM {$t}userrelation WHERE user_id = '".secureINS($l['id_id'])."' AND friend_id = '".secureINS($_user_id)."'");
			$sql->queryResult("DELETE FROM {$t}userrelation WHERE user_id = '".secureINS($_user_id)."' AND friend_id = '".secureINS($l['id_id'])."'");

			$c = $sql->queryResult("SELECT main_id FROM {$t}userrelquest WHERE user_id = '".secureINS($l['id_id'])."' AND sender_id = '".secureINS($_user_id)."' AND main_id = '".secureINS($_id)."' LIMIT 1");
			if(!empty($c) && count($c)) {
				$sql = $sql->queryResult("UPDATE {$t}userrelquest SET status_id = '2' WHERE user_id = '".secureINS($l['id_id'])."' AND sender_id = '".secureINS($_user_id)."'");
				#if(mysql_affected_rows()) sysMSG($s['id_id'], 'Relation', 'Your relation with '.$s->alias.' is denied!');
				$user->notifyDecrease('rel', $l['id_id']);
			} else {
				$c = $sql->queryResult("SELECT main_id FROM {$t}userrelquest WHERE user_id = '".secureINS($_user_id)."' AND sender_id = '".secureINS($l['id_id'])."' AND main_id = '".secureINS($_id)."' AND status_id = '0' LIMIT 1");
				if(!empty($c) && count($c)) {
					$sql = $sql->queryUpdate("UPDATE {$t}userrelquest SET status_id = '2' WHERE user_id = '".secureINS($_user_id)."' AND sender_id = '".secureINS($l['id_id'])."'");
					$user->notifyDecrease('rel', $_user_id);
				}
			}
			#$user->setRelCount($s['id_id']);
			#$user->setRelCount($l['id_id']);
			#$user->get_cache();

			return true;
		}
	}
	
	function unblockRelation($_id)
	{
		global $sql, $l, $t;
		if (!is_numeric($_id)) return false;

		$check = $sql->queryResult("SELECT friend_id FROM {$t}userblock WHERE main_id = '".secureINS($_id)."' AND user_id = '".$l['id_id']."' LIMIT 1");
		if($check) {
			$sql->queryUpdate("DELETE FROM {$t}userblock WHERE user_id = '".$l['id_id']."' AND friend_id = '".$check."' AND rel_id = 'u' LIMIT 1");
			$sql->queryUpdate("DELETE FROM {$t}userblock WHERE friend_id = '".$l['id_id']."' AND user_id = '".$check."' AND rel_id = 'f' LIMIT 1");
		}
		
		return true;
	}

?>