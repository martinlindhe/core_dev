<?
	include('include_all.php');
	
	$show = '';
	if (isset($_GET['id']) && $_GET['id'] != $_SESSION['userId']) {
		$show = $_GET['id'];
		$showname = getUserName($db, $show);
		if (!$showname) {
			header('Location: '.$config['start_page']);
			die;
		}
	} else if ($_SESSION['userId']) {
		$show     = $_SESSION['userId'];
		$showname = $_SESSION['userName'];
	}
	
	if (substr($showname, -1) == 's') {
		$niceshowname = $showname."'";
	} else {
		$niceshowname = $showname.'s';
	}

	if ($_SESSION['userId'] == $show) {
		setUserStatus($db, 'Spanar in sina relationer');
	} else {
		setUserStatus($db, 'Spanar in '.$niceshowname.' relationer');
	}

	include('design_head.php');
	include('design_user_head.php');
	
		$content = displayFriendList($db, $show).'<br>';
		
		if ($_SESSION['userId'] == $show) {
			
			if (!empty($_GET['removesentrequest'])) {
				removeSentFriendRequest($db, $_GET['removesentrequest']);
			}

			$list = getSentFriendRequests($db, $show);
			if (count($list)) {
				$content .= '<br><br>Sende foresp&oslash;rsel:<br>';
				$content .= '<table width="100%" cellpadding=0 cellspacing=0 border=0>';
				for ($i=0; $i<count($list); $i++) {
					$content .= '<tr>';
					$content .= '<td>'.nameLink($list[$i]['recieverId'], $list[$i]['recieverName']).'</td>';
					$content .= '<td align="right"><a href="'.$_SERVER['PHP_SELF'].'?id='.$show.'&removesentrequest='.$list[$i]['reqId'].'">'.$config['text']['link_remove'].'</a></td>';
					$content .= '</tr>';
				}
				$content .= '</table>';
			}

			$list = getRecievedFriendRequests($db, $show);
			if (count($list)) {
				$content .= '<br><br>Mottatte foresp&oslash;rsler:<br>';
				$content .= '<table width="100%" cellpadding=0 cellspacing=0 border=0>';
				for ($i=0; $i<count($list); $i++) {
					$content .= '<a href="user_relationrequest.php?id='.$list[$i]['reqId'].'">'.$list[$i]['senderName'].'</a><br>';
				}
				$content .= '</table>';
			}

		}
		

		/* Skapa relation */
		if ($_SESSION['userId'] && ($_SESSION['userId'] != $show) && $show != $config['messages']['system_id']) {

			if (!isFriend($db, $show)) {
				$content .= '<a href="user_edit_relation.php?id='.$show.'">Legg til venner</a><br>';
			} else {
				$content .= 'Ni har en relation<br>';
				$content .= '<a href="user_edit_relation.php?id='.$show.'">Avbryt relationen</a><br>';
			}
		}

		if ($_SESSION['userId'] == $show) $title = 'Min venneliste';
		else $title = $niceshowname.' vennelista';

		echo '<div id="user_misc_content">';
		echo MakeBox($title, $content);
		echo '</div>';

	include('design_user_foot.php');
	include('design_foot.php');
?>