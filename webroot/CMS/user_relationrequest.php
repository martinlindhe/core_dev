<?
	include('include_all.php');
	
	if ($_SESSION['loggedIn'] && !empty($_GET['id']) && is_numeric($_GET['id'])) {
		$requestId = $_GET['id'];
	} else {
		header('Location: '.$config['start_page']);
		die;
	}

	$data = getFriendRequest($db, $requestId);
	if (!$data) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_GET['deny'])) {
		denyFriendRequest($db, $requestId);
		header('Location: user_relations.php?id='.$_SESSION['userId']);
		die;
	}

	if (isset($_GET['accept'])) {
		acceptFriendRequest($db, $requestId);
		header('Location: user_relations.php?id='.$_SESSION['userId']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');

		if ($data['recieverId'] == $_SESSION['userId']) {
			$senderName = getUserName($db, $data['senderId']);

			//$content  = 'F&ouml;rfr&aring;gan om att skapa relation fr&aring;n '.nameLink($data['senderId'], $senderName).':<br><br>';
			$content  = 'Ny foresp&oslash;rsel: '.nameLink($data['senderId'], $senderName).' vil gjerne bli venner med deg!<br><br>';
			
			$content .= '<div class="relation_request">'.$data['msg'].'</div><br>';
			$content .= '<table width="100%" cellpadding=0 cellspacing=0><tr>';
			$content .= '<td><a href="'.$_SERVER['PHP_SELF'].'?id='.$requestId.'&accept">Aksepter</a></td>';
			$content .= '<td><a href="'.$_SERVER['PHP_SELF'].'?id='.$requestId.'&deny">Avsl&aring;</a></td>';
			$content .= '</tr></table>';
		} else {
			$content = 'hmxmx';
		}

		echo '<div id="user_misc_content">';
		echo MakeBox('Venner|Foresp&oslash;rsel', $content);
		echo '</div>';

	include('design_user_foot.php');
	include('design_foot.php');
?>