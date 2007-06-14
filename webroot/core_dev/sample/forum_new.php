<?
	/*
		forum_new.php - Create new forum threads and posts
		
		The script takes one parameter "id", which specifies the parent ID.
		
		Parent ID = 0 means: create a top level category (admins only)
		Parent ID = category means: create a forum (admins only)
		Parent ID = forum means: create a thread (everyone)
		Parent ID = thread means: create a post (everyone)

		Vocabulary:
		category: A top level container, which contains folders
		forum: A container with parent = a category, which contains threads
		thread: A whole discussion is refered as a "thread"
		post: A discussion contains one or more posts

		if _GET['q'] is set, this is the forum post to quote
	*/

	require_once('config.php');
	
	$session->requireLoggedIn();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$itemId = $_GET['id'];

	$item = getForumItem($itemId);
	$parent = getForumItem($item['parentId']);
	if (($itemId && !$item) || $item['locked']) {
		//block attempt to create item with nonexisting parent
		header('Location: forum.php');
		die;
	}
	
	$quoteId = 0;
	if (!empty($_GET['q']) && is_numeric($_GET['q'])) $quoteId = $_GET['q'];

	$writeSubject = '';
	$writeBody = '';
	
	if ($quoteId) {
		/* Quote another message */
		$quoteItem = getForumItem($quoteId);
		$quoteName = $quoteItem['authorName'];
		if ($quoteName && trim($quoteItem['itemBody'])) {
			$writeBody = '[quote name='.$quoteName.']'.$quoteItem['itemBody']."[/quote]\n\n";
		}
	}
	

	if (!empty($_POST['subject']))	$writeSubject = $_POST['subject'];
	if (!empty($_POST['body']))			$writeBody = $_POST['body'];

	if (!empty($_POST['subject']) || !empty($_POST['body'])) {

		if (strlen($writeBody) <= $config['forum']['maxsize_body']) {
			if ($_SESSION['isAdmin'] && ($itemId == 0 || $item['parentId'] == 0)) {
				//Create category or a forum
				if ($writeSubject) {
					$itemId = addForumFolder($itemId, $writeSubject, $writeBody);
				} else {
					$forum_error = 'You must write a topic!';
				}
			} else {
				//Create a thread or a post
				if ($parent['parentId'] == 0 && !$writeSubject) {
					$forum_error = 'You must write a topic!';
				} else {
					$sticky = 0;
					if ($_SESSION['isAdmin'] && !empty($_POST['sticky'])) $sticky = $_POST['sticky'];
					$itemId = addForumMessage($itemId, $writeSubject, $writeBody, $sticky);
				}
			}
		} else {
			$forum_error = 'The post is too long, the max allowed length are '.$config['forum']['maxsize_body'].' characters, please try to shorten down your text a bit.';
		}

		if (!isset($forum_error)) {
			/*
			if (!empty($_POST['subscribehere'])) {
				//Slå på bevakning för det nyskapade inlägget/diskussionen
				addSubscription($itemId, SUBSCRIBE_MAIL);
			}*/
			if ($itemId == 0 || $item['parentId'] == 0) {
				header('Location: forum.php?id='.$itemId);
			} else {
				$item = getForumItem($itemId);
				header('Location: forum.php?id='.$item['parentId'].'#post'.$itemId);
			}
			die;
		}
	}

	require('design_head.php');

	$content = '';
	
	$hide_body = false;
	$hide_subject = false;

	if ($itemId == 0)
	{
		//Create top level category (admins only)
		$content .= '<a href="forum.php">Forum</a> - Add new category<br><br>';
		$title = 'New category';
		$hide_body = true;
	} else if ($item['parentId'] == 0)
	{
		//Create a forum (admins only)
		$content .= '<a href="forum.php">Forum</a> - Add new forum<br><br>';
		$title = 'New forum';
	} else if ($parent['parentId'] == 0)
	{
		//Create a discussion thread (everyone)
		$content .= getForumFolderDepthHTML($itemId).' - Add new discussion thread<br><br>';
		$title = 'New discussion thread';
	} else
	{
		//Create a post (everyone)
		$content .= getForumFolderDepthHTML($itemId).' - Add a response to this post<br><br>';
		$content .= showForumPost($item, '', false);
		$hide_subject = true;
		$title = 'New post';
	}

	if (!empty($forum_error)) $content .= '<div class="critical">'.$forum_error.'</div>';

	$content .= '<form method="post" name="newpost" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
	if (!$hide_subject) {
		$content .= 'Tema: &nbsp;<input type="text" size=60 maxlength=50 name="subject" value="'.$writeSubject.'"><br>';
	}
	if (!$hide_body) {
		$content .= '<br>';
		$content .= 'Innlegg:<br>';
		$content .= '<textarea cols=80 rows=15 name="body">'.$writeBody.'</textarea><br>';
	}
	
	if ($_SESSION['isAdmin'] && $parent['parentId'] == 0) {
		//Allow admins to create stickies & announcements
		$content .= '<input name="sticky" type="radio" class="radio" value="0" checked>Create a normal thread tr&aring;d<br>';
		$content .= '<input name="sticky" type="radio" class="radio" value="1">Make the thread a sticky<br>';
		$content .= '<input name="sticky" type="radio" class="radio" value="2">Make the thread an announcement<br>';
	}
	
	$content .= '<br>';
	$content .= '<input type="submit" class="button" value="Lagre"> ';

/*
	if (!isSubscribed($itemId, SUBSCRIBE_MAIL)) {
		$content .= '<input name="subscribehere" type="checkbox" class="checkbox">Overv&aring;ke tr&aring;d';
	} else {
		$content .= '<span class="objectCritical">Du har redan en bevakning h&auml;r eller h&ouml;gre upp i diskussionstr&aring;den</span>';
	}
*/
	$content .= '</form><br>';


	$content .= '<a href="javascript:history.go(-1);">Go back</a>';

	echo $title, $content;

	require('design_foot.php');
?>
<script type="text/javascript">
if (document.newpost.subject) document.newpost.subject.focus();
else if (document.newpost.body) document.newpost.body.focus();
</script>