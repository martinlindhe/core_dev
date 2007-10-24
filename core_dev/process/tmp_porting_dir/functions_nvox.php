<?
	function nvoxHandleIncoming($_user_id, $_days, $_level)
	{
		global $db, $user_db, $session, $config;
		if (!is_numeric($_user_id) || !is_numeric($_days) || !is_numeric($_level)) return false;
		if ($_level > 3) return false;

		//identifiera användaren
		$q = 'SELECT u_alias FROM s_user WHERE id_id='.$_user_id;
		$username = $user_db->getOneItem($q);
		if (!$username) {
			echo '1';	//error code
			$session->log('Specified user dont exist: '.$_user_id, LOGLEVEL_WARNING);
			die;
		}
		
		//Acknowledgment - Tell NVOX that the data was received
		echo '0';	//ok code

		$internal_msg = 'Ditt konto har uppgraderats med '.$_days.' dagar VIP';
		if ($_level == 3) $internal_msg .= ' Deluxe';

		addVIP($_user_id, $_level, $_days);

		if ($_level == 3) {
			$log_msg = 'Gave '.$username.' '.$_days.' days of VIP deluxe from NVOX';
		} else {
			$log_msg = 'Gave '.$username.' '.$_days.' days of VIP from NVOX';
		}
		$session->log($log_msg);
			
		//Leave a confirmation message in the users inbox
		$internal_title = 'VIP-bekräftelse';
		$q = 'INSERT INTO s_usermail SET sender_id=0, user_id='.$_user_id.',sent_ttl="'.$internal_title.'",sent_cmt="'.$internal_msg.'",sent_date=NOW()';
		$user_db->insert($q);

		return true;
	}
?>
