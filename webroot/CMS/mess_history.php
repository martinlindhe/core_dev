<?
	include('include_all.php');
	
	if (!$_SESSION['loggedIn'] || empty($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$from = $_GET['id'];
	if ($from == 0) {
		$fromname = 'Systemmeddelanden';
	} else {
		$fromname = getUserName($db, $from);
		if (!$fromname) {
			header('Location: '.$config['start_page']);
			die;
		}
	}
	
	if (isset($_GET['p'])) {
		$page = $_GET['p'];
	} else {
		$page = 1;
	}
	
	if (isset($_GET['remove'])) {
		removeUserMessage($db, $_SESSION['userId'], $_GET['remove']);
	}

	include('design_head.php');
	include('design_user_head.php');


		$content  = 'Historikk mellom deg og '.nameLink($from, $fromname).'.<br>';
		$content .= $config['messages']['items_per_page'].' meldinger per side.<br><br>';
	
		$list = getMessageHistory($db, $_SESSION['userId'], $from, $page);
	
		if (count($list)) {
			$content .= '<table width="100%" cellpadding=0 cellspacing=0 border=0>';
	
			$xcnt = $config['messages']['items_per_page'];
			if ($config['messages']['items_per_page'] > count($list)) $xcnt=count($list);
			for ($i=0; $i<$xcnt; $i++) {
				$content .= '<tr><td>';
					$content .= '<table width="100%" cellpadding=0 cellspacing=0 border=0>';
						$content .= '<tr><td>';
							$title = getRelativeTimeLong($list[$i]['timestamp']);
							if ($list[$i]['messageReceiver'] == $_SESSION['userId']) {
								if ($from != 0) {
									$title .= ', fra '.nameLink($from, $fromname);
								}
								if ($list[$i]['messageStatus'] == MESSAGE_UNREAD) $title .= ' <img border=0 align="absmiddle" width=14 height=10 border=0 src="gfx/brev_recv.gif" alt="Nytt meddelande">';
							} else {
								$title .= ', fra '.nameLink($_SESSION['userId'], $_SESSION['userName']);
							}
							$content .= '<b>'.$title.'</b><br>';
	
							$content .= nl2br(dbStripSlashes($list[$i]['messageBody']));
	
						$content .= '</td></tr>';
						if ($list[$i]['messageReceiver'] == $_SESSION['userId'] && ($from != 0)) {
							$content .= '<tr><td><a href="mess_new.php?id='.$from.'">'.$config['text']['link_reply'].'</a></td></tr>';
						}
						$content .= '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?id='.$from.'&remove='.$list[$i]['messageId'].'&p='.$page.'">'.$config['text']['link_remove'].'</a></td></tr>';
						$content .= '<tr><td>&nbsp;</td></tr>';
						$content .= '</table>';
				$content .= '</td></tr>';
				markMessageAsRead($db, $_SESSION['userId'], $list[$i]['messageId']); 
			}
			$content .= '</table>';
	
			$msgcnt = getMessageHistoryCount($db, $_SESSION['userId'], $from);
			$content .= pageCounter($msgcnt, $config['messages']['items_per_page'], $_SERVER['PHP_SELF'].'?id='.$from, $page, 8);
	
		} else {
			$content .= 'Inga meddelanden att visa.<br>';
		}
		
		echo '<div id="user_misc_content">';
		echo MakeBox('Meldinger|Historikk', $content);
		echo '</div>';

	include('design_user_foot.php');
	include('design_foot.php');
?>