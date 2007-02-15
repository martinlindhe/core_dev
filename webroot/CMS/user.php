<?
	include('include_all.php');

	if (!$_SESSION['loggedIn']) {
		//since user.php is start_page, this would be forever-redirect:
		header('Location: index.php');
		die;
	}

	$show = $_SESSION['userId'];
	$showname = $_SESSION['userName'];

	if (substr($showname, -1) == 's') {
		$niceshowname = $showname."'";
	} else {
		$niceshowname = $showname.'s';
	}

	setUserStatus($db, 'Ser p&aring; sin side');

	include('design_head.php');
	include('design_user_head.php');

		$content = getInfoField($db, 'om forumet').
/*			'Om Forumet.<br>'.
			'SoIP Games on demand forumet kan du sp&oslash;rre sp&oslash;rsm&aring;l om spill og andre tjenster i SoIP Games on demand portalen. '.
			'Det er ogs&aring; mulig &aring; legge opp eget bilde eller velge et av v&aring;re Avatar bilder. I tilllegg kan du lage en egen signatur p&aring; meldingene dine. '.
			'Dette gj&oslash;res under lag profil oppe til venster p&aring; skjermen.'.*/
			'|<a href="forum.php">Til Forum</a>';

		echo '<div id="user_forum_desc">';
			echo MakeBox('<a href="forum.php">Forum</a>|'.formatShortDate(), $content);
		echo '</div>';

		$content = '';
		$list = getLastForumPosts($db, 10);
		for ($i=0; $i<count($list); $i++) {
			//$content .= getForumFolderDepthHTML($db, $list[$i]['itemId']);
			//$content .= showForumPost($db, $list[$i], $i+1);
			$subject = $list[$i]['itemSubject'];
			if (!$subject) $subject = $list[$i]['parentSubject'];
			if (mb_strlen($subject) > 21) {
				$subject = mb_substr($subject, 0, 18).'...';
			}
			if ($list[$i]['itemSubject']) {
				$content .= '<a href="forum.php?id='.$list[$i]['itemId'].'">'.$subject.'</a> av '.nameLink($list[$i]['authorId'], $list[$i]['authorName']);
			} else {
				$content .= '<a href="forum.php?id='.$list[$i]['parentId'].'#post'.$list[$i]['itemId'].'">'.$subject.'</a> av '.nameLink($list[$i]['authorId'], $list[$i]['authorName']);
			}
			$content .= ' '.getRelativeTimeLong($list[$i]['timestamp']).'<br>';
		}
		echo '<div id="user_forum_latest">';
			echo MakeBox('De siste innlegg i forumet', $content);
		echo '</div>';

	include('design_user_foot.php');
	include('design_foot.php');
?>