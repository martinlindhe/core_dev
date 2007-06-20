<?
	require_once('config.php');
	
	$session->requireLoggedIn();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;	//invalid request
	$itemId = $_GET['id'];

	$item = getForumItem($itemId);
	if (!$item) die;	//object dont exist, invalid request

	/* Lägg till en kommentar till anmälan */
	if (isset($_POST['motivation'])) {

		/* Rapportera inlägget till abuse */
		$queueId = addToModerationQueue(MODERATION_FORUM, $itemId);
		addComment(COMMENT_MODERATION, $queueId, $_POST['motivation']);

		header('Location: forum.php?id='.$item['parentId']);
		die;
	}

	require('design_head.php');

	wiki('Forum abuse reporting');

	echo showForumPost($item, '', false).'<br>';

	echo '<form name="abuse" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
	echo 'Motivate the reason:<br>';
	echo '<textarea name="motivation" cols="50" rows="5"></textarea><br><br>';
	echo '<input type="submit" class="button" value="Report">';
	echo '</form><br><br>';
	echo '<a href="javascript:history.go(-1);">Return</a>';

	require('design_foot.php');
?>