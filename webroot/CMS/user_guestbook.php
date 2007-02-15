<?
	include('include_all.php');
	
	if (!empty($_GET['id'])) {
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

	if ($_SESSION['isAdmin'] || $_SESSION['userId'] == $show) {
		if (!empty($_GET['remove'])) {
			removeGuestbookEntry($db, $_GET['remove']);
		}
	}


	if ($_SESSION['userId'] == $show) {
		setUserStatus($db, 'L&auml;ser sin g&auml;stbok');
	} else {
		setUserStatus($db, 'Spanar in '.$niceshowname.' g&auml;stbok');
		if (isset($_POST['body'])) {
			$body = $_POST['body'];
			addGuestbookEntry($db, $show, '', $body);
		}
	}

	if (isset($_GET['p'])) {
		$page = $_GET['p'];	//what page to show, 1, 2 etc
	} else {
		$page = 1;
	}

	include('design_head.php');
	include('design_user_head.php');

			$content = 'Gjesteboken inneholder '.getGuestbookSize($db, $show).' inlegg.<br><br>';

			$list = getGuestbook($db, $show, $page);
			for ($i=0; $i<count($list); $i++) {
				$content .= '<div class="guestbookentry">';

				$head = getRelativeTimeLong($list[$i]['timestamp']).', fra '.nameLink($list[$i]['authorId'], $list[$i]['authorName']);
				if ($_SESSION['userId'] == $show) {
					if ($list[$i]['entryRead'] == 0) $head .= ' <img border=0 align="absmiddle" width=14 height=10 border=0 src="gfx/brev_recv.gif" alt="Nytt inlägg">';
				}
				$content .= '<b>'.$head.'</b><br>';
				$content .= stripslashes($list[$i]['body']).'<br>';

				if ($_SESSION['isAdmin'] || $_SESSION['userId'] == $show) {
					$content .= '<a href="'.$_SERVER['PHP_SELF'].'?id='.$show.'&remove='.$list[$i]['entryId'].'">'.$config['text']['link_remove'].'</a>';
				}
				$content .= '</div><br>';
			}

			$content .= pageCounter(getGuestbookSize($db, $show), $config['guestbook']['items_per_page'], $_SERVER['PHP_SELF'].'?id='.$show, $page).'<br>';

			if ($_SESSION['loggedIn']) {

				if ($_SESSION['userId'] != $show) {
					$content .= 'Skriv et nytt innlegg:<br>';
					$content .= '<form name="addGuestbook" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$show.'">';
					$content .= '<textarea name="body" cols=40 rows=6></textarea><br><br>';
					$content .= '<input type="submit" class="button" value="'.$config['text']['link_save'].'">';
					$content .= '</form>';
				} else {
					/* Markera alla inlägg som lästa */
					markGuestbookRead($db);
				}
			}

		echo '<div id="user_guestbook_content">';
		echo MakeBox('Gjestebok', $content);
		echo '</div>';

	include('design_user_foot.php');
	include('design_foot.php');
?>