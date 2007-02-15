<?
	include('include_all.php');

	if (!$_SESSION['loggedIn'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$itemId = $_GET['id'];
	$item = getForumItem($db, $itemId);

	if (!$item || $item['locked'] || (!$_SESSION['isAdmin'] && ($item['authorId'] != $_SESSION['userId']))) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$subject = '';
	$body = '';
	
	if (!empty($_POST['subject'])) $subject = $_POST['subject'];
	if (!empty($_POST['body'])) $body = $_POST['body'];

	if ($subject || $body) {
		$sticky = 0;
		if ($_SESSION['isAdmin'] && !empty($_POST['sticky'])) $sticky = 1;

		forumUpdateItem($db, $itemId, $subject, $body, $sticky);
		
		if (forumItemIsMessage($db, $itemId)) {		
			header('Location: forum.php?id='.$item['parentId'].'#post'.$itemId);
		} else {
			header('Location: forum.php?id='.$itemId);
		}
		die;
	}

	include('design_head.php');
	include('design_forum_head.php');

	if (forumItemIsMessage($db, $itemId)) {
		$content = 'Redigere innlegg:<br><br>';
	} else {
		$content = 'Redigere tr&aring;d:<br><br>';
	}
	
	$content .= '<form name="change" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
	//if (forumItemIsDiscussion($db, $itemId)) {
		$content .= 'Tema:<br>';
		$content .= '<input name="subject" size=72 value="'.$item['itemSubject'].'"><br><br>';
	//}
	$content .= '<textarea name="body" cols=110 rows=25>'.$item['itemBody'].'</textarea><br><br>';
	if ($_SESSION['isAdmin'] && forumItemIsDiscussion($db, $itemId)) {
		$content .= '<input type="checkbox" class="checkbox" value="1" name="sticky"';
		if ($item['sticky'] == 1) $content .= ' checked';
		$content .= '> Tr&aring;den er en sticky<br><br>';
	}
	$content .= '<input type="submit" class="button" value="Lagre">';
	$content .= '</form><br><br>';
	$content .= '<a href="javascript:history.go(-1);">'.$config['text']['link_return'].'</a>';

		echo '<div id="user_forum_content">';
		$title = getForumFolderDepthHTML($db, $itemId);
		echo MakeBox($title.'|'.$config['text']['link_edit'], $content, 500);
		echo '</div>';

	include('design_forum_foot.php');
	include('design_foot.php');
?>