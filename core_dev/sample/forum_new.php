<?
	/*
		forum_new.php - Create new forum threads and posts
		
		fixme: are the below documentation still accurate?
		
		The script takes one parameter "id", which specifies the parent ID.
		
		Parent ID = 0 means: create a top level category (admins only)
		Parent ID = category means: create a forum (admins only)
		Parent ID = forum means: create a thread (everyone)
		Parent ID = thread means: create a post (everyone)

		Vocabulary:
		root level category: A top level container, which contains forums
		forum: A container inside a root level forum, which contains topics
		topic: A whole discussion is refered as a "topic"
		post: A discussion contains one or more posts

		if _GET['q'] is set, this is the forum post to quote
	*/

	require_once('config.php');

	$session->requireLoggedIn();

	$itemId = 0;
	if (!empty($_GET['id']) && is_numeric($_GET['id'])) $itemId = $_GET['id'];
	if (!$itemId && !$session->isAdmin) die;	//invalid request

	if ($itemId) {
		$item = getForumItem($itemId);
		$parent = getForumItem($item['parentId']);
		if (($itemId && !$item) || $item['locked']) die;	//block attempt to create item with nonexisting parent
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

	$createdId = 0;

	$forum_error = '';

	if (!empty($_POST['subject']) || !empty($_POST['body'])) {

		if (strlen($writeBody) <= $config['forum']['maxsize_body']) {
			if ($session->isAdmin && ($itemId == 0 || $item['parentId'] == 0)) {
				//Create category or a forum
				if ($writeSubject) {
					$createdId = addForumFolder($itemId, $writeSubject, $writeBody);
	
					header('Location: forum.php?id='.$createdId);
					die;	
	
				} else {
					$forum_error = 'You must write a topic!';
				}
			} else {
				//Create a thread or a post
				if ($parent['parentId'] == 0 && !$writeSubject) {
					$forum_error = 'You must write a topic!';
				} else {
					$sticky = 0;
					if ($session->isAdmin && !empty($_POST['sticky'])) $sticky = $_POST['sticky'];
					$createdId = addForumMessage($itemId, $writeSubject, $writeBody, $sticky);
					
					if ($createdId) {
						//attach all FILETYPE_FORUM ownerId =0 to this id
						$q = 'UPDATE tblFiles SET ownerId='.$createdId.' WHERE fileType='.FILETYPE_FORUM.' AND ownerId=0 AND uploaderId='.$session->id;
						$db->update($q);
					}

					header('Location: forum.php?id='.$itemId.'#post'.$createdId);
					die;	
				}
			}
		} else {
			$forum_error = 'The post is too long, the max allowed length are '.$config['forum']['maxsize_body'].' characters, please try to shorten down your text a bit.';
		}

		if (!$forum_error) {
			if (!empty($_POST['subscribehere'])) {
				//Start a subscription of the created topic
				//fixme: make sure we are creating a topic so users cant subscribe to whole forums
				addSubscription(SUBSCRIPTION_FORUM, $itemId);
			}
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

	echo createMenu($forum_menu, 'blog_menu');

	$hide_subject = false;

	if (!empty($forum_error)) echo '<div class="critical">'.$forum_error.'</div>';

	echo '<form method="post" name="newpost" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
	
	if ($itemId == 0) {
		//Create root level category (admins only)
		echo 'Forum - Add new root level category<br/><br/>';
		echo 'Name: <input type="text" size="60" maxlength="50" name="subject" value="'.$writeSubject.'"/><br/>';

	} else if (!$item['parentId']) {
		//Create a category inside a "root level category" (admins only)
		echo 'Forum - Add new subcategory (under <b>'.getForumName($itemId).'</b>)<br/><br/>';

		echo 'Subject: <input type="text" size="60" maxlength="50" name="subject" value="'.$writeSubject.'"/><br/>';		
		echo 'Description:<br/>';
		echo '<input type="text" name="body" size="60" value="'.$writeBody.'"/><br/><br/>';		
	} else if ($parent['parentId'] == 0) {
		//Create a discussion thread (everyone)
		echo 'Add new discussion thread under '.getForumDepthHTML(FORUM_FOLDER, $itemId).'<br/><br/>';
		echo 'Subject: <input type="text" size="60" maxlength="50" name="subject" value="'.$writeSubject.'"/><br/>';
		echo '<textarea name="body" cols="60" rows="14">'.$writeBody.'</textarea><br/><br/>';

		if ($session->isAdmin) {
			//Allow admins to create stickies & announcements
			echo '<input name="sticky" type="radio" class="radio" value="0" id="r0" checked="checked"/><label for="r0">Create a normal thread</label><br/>';
			echo '<input name="sticky" type="radio" class="radio" value="1" id="r1"/><label for="r1">Admin only: Make the thread a sticky</label><br/>';
			echo '<input name="sticky" type="radio" class="radio" value="2" id="r2"/><label for="r2">Admin only: Make the thread an announcement</label><br/>';
		}
	} else {
		//Create a post (everyone)
		echo getForumDepthHTML(FORUM_FOLDER, $itemId).' - Add a response to this post<br/><br/>';
		echo showForumPost($item, '', false);

		//handle file upload
		if (!empty($_FILES['file1'])) {
			$files->handleUpload($_FILES['file1'], FILETYPE_FORUM, 0);	
		}
		
		$files->showAttachments(FILETYPE_FORUM, 0);

		echo '<div id="forum_new_attachment">';
		echo 'Attach a file: ';
		echo '<input type="file" name="file1"/>';
		echo '<input type="submit" class="button" value="Upload"/> ';
		echo '</div>';

		echo '<textarea name="body" cols="60" rows="14">'.$writeBody.'</textarea><br/><br/>';
	}

	$item = getForumItem($itemId);

	echo '<br/>';

	echo '<input type="submit" class="button" value="Save"/> ';

/*
	if (!isSubscribed($itemId, SUBSCRIBE_MAIL)) {
		$content .= '<input name="subscribehere" type="checkbox" class="checkbox">Subscribe to topic';
	} else {
		$content .= '<div class="critical">You are already subscribed to this topic</div>';
	}
*/
	echo '</form><br/>';
	echo '<a href="javascript:history.go(-1);">Go back</a>';
?>
<script type="text/javascript">
if (document.newpost.subject) document.newpost.subject.focus();
else if (document.newpost.body) document.newpost.body.focus();
</script>
<?
	require('design_foot.php');
?>