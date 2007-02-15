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

	include('include_all.php');
	
	if (!$_SESSION['loggedIn'] || !isset($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: forum.php');
		die;
	}

	$itemId = $_GET['id'];
	$item = getForumItem($db, $itemId);
	$parent = getForumItem($db, $item['parentId']);
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
		$quoteItem = getForumItem($db, $quoteId);
		//$quoteName = $quoteItem['authorName'];
		$quoteName = getUserdataByFieldname($db, $quoteItem['authorId'], 'Nickname');
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
					$itemId = addForumFolder($db, $itemId, $writeSubject, $writeBody);
				} else {
					$forum_error = 'Du m&aring;ste ange en rubrik!';
				}
			} else {
				//Create a thread or a post
				if ($parent['parentId'] == 0 && !$writeSubject) {
					$forum_error = 'Du m&aring;ste ange en rubrik!';
				} else {
					$sticky = 0;
					if ($_SESSION['isAdmin'] && !empty($_POST['sticky'])) $sticky = $_POST['sticky'];
					$itemId = addForumMessage($db, $itemId, $writeSubject, $writeBody, $sticky);
				}
			}
		} else {
			$forum_error = 'Inl&auml;gget &auml;r f&ouml;r l&aring;ngt, den till&aring;tna maxl&auml;ngden &auml;r '.$config['forum']['maxsize_body'].' tecken, var god f&ouml;rs&ouml;k att korta ner texten lite.';
		}

		if (!isset($forum_error)) {
			/*
			if (!empty($_POST['subscribehere'])) {
				//Slå på bevakning för det nyskapade inlägget/diskussionen
				addSubscription($db, $itemId, SUBSCRIBE_MAIL);
			}*/
			if ($itemId == 0 || $item['parentId'] == 0) {
				header('Location: forum.php?id='.$itemId);
			} else {
				$item = getForumItem($db, $itemId);
				header('Location: forum.php?id='.$item['parentId'].'#post'.$itemId);
			}
			die;
		}
	}

	/*
	if (userAccess($db, 'forum_global_cant_post')) {
		echo 'du kan inte skriva inl&auml;gg';
		die;
	}*/

	include('design_head.php');
	include('design_forum_head.php');

	$content = '';
	
	$hide_body = false;
	$hide_subject = false;

	if ($itemId == 0)
	{
		//Create top level category (admins only)
		$content .= '<a href="forum.php">Forum</a> - Lage ny kategori<br><br>';
		$title = 'Ny kategori';
		$hide_body = true;
	} else if ($item['parentId'] == 0)
	{
		//Create a forum (admins only)
		$content .= '<a href="forum.php">Forum</a> - Lage nytt forum<br><br>';
		$title = 'Nytt forum';
	} else if ($parent['parentId'] == 0)
	{
		//Create a discussion thread (everyone)
		$content .= getForumFolderDepthHTML($db, $itemId).' - Lage ny diskusjonstr&aring;d<br><br>';
		$title = 'Ny diskusjonstr&aring;d';
	} else
	{
		//Create a post (everyone)
		$content .= getForumFolderDepthHTML($db, $itemId).' - Skiv et svar til dette innlegget<br><br>';
		$content .= showForumPost($db, $item, '', false);
		$hide_subject = true;
		$title = 'Nytt innlegg';
	}

	if (!empty($forum_error)) $content .= '<span class="objectCritical">'.$forum_error.'</span><br>';

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
		$content .= '<input name="sticky" type="radio" class="radio" value="0" checked>Laga en vanlig tr&aring;d<br>';
		$content .= '<input name="sticky" type="radio" class="radio" value="1">Gj&oslash;r tr&aring;den til sticky<br>';
		$content .= '<input name="sticky" type="radio" class="radio" value="2">Lage tr&aring;den til en kunngj&oslash;ring<br>';
	}
	
	$content .= '<br>';
	$content .= '<input type="submit" class="button" value="Lagre"> ';

/*
	if (!isSubscribed($db, $itemId, SUBSCRIBE_MAIL)) {
		$content .= '<input name="subscribehere" type="checkbox" class="checkbox">Overv&aring;ke tr&aring;d';
	} else {
		$content .= '<span class="objectCritical">Du har redan en bevakning h&auml;r eller h&ouml;gre upp i diskussionstr&aring;den</span>';
	}
*/
	$content .= '</form><br>';


	$nickname = getUserdataByFieldname($db, $_SESSION['userId'], 'Nickname');
	if (!$nickname) {
		
		$content  = 'Du m&aring; fylle ut Nickname i min  profil f&oslash;r du kan skrive i forumet.<br><br>';
		$content .= '<a href="user_edit.php">Klikk her</a> for &aring; lage et nickname.<br><br>';
	}


	$content .= '<a href="javascript:history.go(-1);">'.$config['text']['link_return'].'</a>';

	echo '<div id="user_forum_content">';
	echo MakeBox('<a href="forum.php">Forum</a>|'.$title, $content, 500);
	echo '</div>';

	include('design_forum_foot.php');
	include('design_foot.php');
?>
<script type="text/javascript">
if (document.newpost.subject) document.newpost.subject.focus();
else if (document.newpost.body) document.newpost.body.focus();
</script>