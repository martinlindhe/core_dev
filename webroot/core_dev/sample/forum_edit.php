<?
	require_once('config.php');

	$session->requireLoggedIn();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;	//invalid request
	$itemId = $_GET['id'];

	$item = getForumItem($itemId);

	if (!$item || $item['locked'] || (!$session->isAdmin && ($item['authorId'] != $session->id))) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$subject = '';
	$body = '';
	
	if (!empty($_POST['subject'])) $subject = $_POST['subject'];
	if (!empty($_POST['body'])) $body = $_POST['body'];

	if ($subject || $body) {
		$sticky = 0;
		if ($session->isAdmin && !empty($_POST['sticky'])) $sticky = 1;

		forumUpdateItem($itemId, $subject, $body, $sticky);
		
		if (forumItemIsMessage($itemId)) {		
			header('Location: forum.php?id='.$item['parentId'].'#post'.$itemId);
		} else {
			header('Location: forum.php?id='.$itemId);
		}
		die;
	}

	require('design_head.php');

	$title = getForumFolderDepthHTML($itemId);
	echo 'Title: '.$title;

	if (forumItemIsMessage($itemId)) {
		echo 'Edit post:<br><br>';
	} else {
		echo 'Edit thread:<br><br>';
	}
	
	echo '<form name="change" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
	//if (forumItemIsDiscussion($itemId)) {
		echo 'Subjet:<br>';
		echo '<input name="subject" size=72 value="'.$item['itemSubject'].'"><br><br>';
	//}
	echo '<textarea name="body" cols=110 rows=25>'.$item['itemBody'].'</textarea><br><br>';
	if ($session->isAdmin && forumItemIsDiscussion($itemId)) {
		echo '<input type="checkbox" class="checkbox" value="1" name="sticky"'.($item['sticky']?' checked="checked"':'').'/>';
		echo ' The thread is a sticky sticky<br><br>';
	}
	echo '<input type="submit" class="button" value="Save">';
	echo '</form><br><br>';
	echo '<a href="javascript:history.go(-1);">Return</a>';

	require('design_foot.php');
?>