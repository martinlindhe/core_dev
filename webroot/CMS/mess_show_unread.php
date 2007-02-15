<?
	include('include_all.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');
	
	$content = '';

	$list = getUserNewMessages($db, $_SESSION['userId']);

	if (count($list) == 1) {
		$content .= '1 nytt meddelande.';
	} else {
		$content .= count($list).' nya meddelanden.';
	}
	$content .=  '<br><br>';
	
	for ($i=0; $i<count($list); $i++) {

		$content .= '<table width="100%" cellpadding=0 cellspacing=0 border=0>';
			$content .= '<tr><td colspan=3>';
				$title = getRelativeTimeLong($list[$i]['timestamp']);
				if ($list[$i]['messageSender'] != 0) {
					$title .= ', av '.nameLink($list[$i]['messageSender'], $list[$i]['otherName']);
				} else {
					$title .= ', av uReply';
				}
				if ($list[$i]['messageStatus'] == MESSAGE_UNREAD) $title .= ' <img border=0 align="absmiddle" width=14 height=10 border=0 src="gfx/brev_recv.gif" alt="Nytt meddelande">';

				$content .= '<b>'.$title.'</b><br>';
				if ($list[$i]['messageSender'] == $config['messages']['system_id']) {
					//dont convert HTML in system messages
					$content .= formatUserInputText($list[$i]['messageBody'], false);
				} else {
					$content .= formatUserInputText($list[$i]['messageBody']);
				}

			$content .= '</td></tr>';
			$content .= '<tr>';
				if ($list[$i]['messageSender'] != 0) {
					$content .= '<td><a href="mess_new.php?id='.$list[$i]['messageSender'].'">'.$config['text']['link_reply'].'</a></td>';
				} else {
					$content .= '<td>&nbsp;</td>';
				}
				$content .= '<td width="33%"><a href="mess_history.php?id='.$list[$i]['messageSender'].'">Historikk</a></td>';
				$content .= '<td width="33%"><a href="mess_history.php?id='.$list[$i]['messageSender'].'&remove='.$list[$i]['messageId'].'">'.$config['text']['link_remove'].'</a></td>';
			$content .= '</tr>';
		$content .= '</table><br>';

		markMessageAsRead($db, $_SESSION['userId'], $list[$i]['messageId']); 
	}

	echo '<div id="user_misc_content">';
	echo MakeBox('Nya meddelanden', $content);
	echo '</div>';

	include('design_user_foot.php');
	include('design_foot.php');
?>