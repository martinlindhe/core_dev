<?
	require_once('atom_moderation.php');		//for automatic moderation of new blogs, and for "report blog" feature
	require_once('atom_comments.php');			//for comment support for blogs

	$config['blog']['moderation'] = true;		//enables automatic moderation of new blogs

	$config['blog']['allowed_tabs'] = array('Blog', 'BlogEdit', 'BlogDelete', 'BlogReport', 'BlogComment', 'BlogFiles');

	function addBlog($categoryId, $title, $body)
	{
		global $db, $session, $config;
		
		if (!$session->id || !is_numeric($categoryId)) return false;

		$title = $db->escape($title);
		$body = $db->escape($body);

		$q = 'INSERT INTO tblBlogs SET categoryId='.$categoryId.',userId='.$session->id.',blogTitle="'.$title.'",blogBody="'.$body.'",timeCreated=NOW()';
		$db->query($q);
		$blogId = $db->insert_id;

		/* Add entry to moderation queue */
		if ($config['blog']['moderation']) {
			if (isSensitive($title) || isSensitive($body)) addToModerationQueue(MODERATION_BLOG, $blogId, true);
		}

		return $blogId;
	}

	function deleteBlog($blogId)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($blogId)) return false;

		$q = 'UPDATE tblBlogs SET timeDeleted=NOW(),deletedBy='.$session->id.' WHERE blogId='.$blogId;
		$db->query($q);
	}

	function updateBlog($blogId, $categoryId, $title, $body)
	{
		global $db, $session, $config;
		
		if (!$session->id || !is_numeric($blogId) || !is_numeric($categoryId)) return false;

		$title = $db->escape($title);
		$body = $db->escape($body);

		$q = 'UPDATE tblBlogs SET categoryId='.$categoryId.',blogTitle="'.$title.'",blogBody="'.$body.'",timeUpdated=NOW() WHERE blogId='.$blogId;
		$db->query($q);

		/* Add entry to moderation queue */
		if ($config['blog']['moderation']) {
			if (isSensitive($title) || isSensitive($body)) addToModerationQueue(MODERATION_BLOG, $blogId, true);
		}
	}

	/* Sorterar resultat per kategori för snygg visning */
	function getBlogsByCategory($userId, $limit = 0)
	{
		global $db;

		if (!is_numeric($userId) || !is_numeric($limit)) return false;

		$q  = 'SELECT t1.*,t2.categoryName,t2.categoryPermissions FROM tblBlogs AS t1';
		$q .= ' LEFT JOIN tblCategories AS t2 ON (t1.categoryId=t2.categoryId AND t2.categoryType='.CATEGORY_BLOG.')';
		$q .= ' WHERE t1.userId='.$userId.' AND t1.deletedBy=0';

		/* Return order: First blogs categorized in global categories, then blogs categorized in user's categories, then uncategorized blogs */
		$q .= ' ORDER BY t2.categoryPermissions DESC, t1.categoryId ASC, t1.timeCreated DESC';
		if ($limit) $q .= ' LIMIT 0,'.$limit;

		return $db->getArray($q);
	}
	
	/* Returns the latest blogs posted on the site */
	function getLatestBlogs($_cnt = 5)
	{
		global $db;

		if (!is_numeric($_cnt)) return false;

		$q  = 'SELECT t1.*,t2.userName FROM tblBlogs AS t1 ';
		$q .= 'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$q .= 'WHERE t1.deletedBy=0 ';
		$q .= 'ORDER BY t1.timeCreated DESC';
		if ($_cnt) $q .= ' LIMIT '.$_cnt;

		return $db->getArray($q);
	}

	function getBlog($blogId)
	{
		global $db;

		if (!is_numeric($blogId)) return false;
		
		$q  = 'SELECT t1.*,t2.categoryName,t3.userName FROM tblBlogs AS t1 ';
		$q .= 'LEFT OUTER JOIN tblCategories AS t2 ON (t1.categoryId=t2.categoryId AND t2.categoryType='.CATEGORY_BLOG.') ';
		$q .= 'INNER JOIN tblUsers AS t3 ON (t1.userId=t3.userId) ';
		$q .= 'WHERE t1.blogId='.$blogId.' AND t1.deletedBy=0';

		return $db->getOneRow($q);
	}
	
	/* Returns all blogs from $userId for the specified month */
		//fixme: this function is broken, the SQL needs updating for DATETIME format change
	
	function getBlogsByMonth($userId, $month, $year, $order_desc = true)
	{
		global $db;

		if (!is_numeric($userId) || !is_numeric($year) || !is_numeric($month) || !is_bool($order_desc)) return false;

		$time_start = mktime(0, 0, 0, $month, 1, $year);			//00:00 at first day of month
		$time_end   = mktime(23, 59, 59, $month+1, 0, $year);	//23:59 at last day of month

		$q  = 'SELECT * FROM tblBlogs ';
		$q .= 'WHERE userId='.$userId.' AND deletedBy=0 ';
		$q .= 'AND timeCreated BETWEEN "'.sql_datetime($time_start).'" AND "'.sql_datetime($time_end).'"';
		if ($order_desc === true) {
			$q .= ' ORDER BY timeCreated DESC';
		} else {
			$q .= ' ORDER BY timeCreated ASC';
		}
		return $db->getArray($q);
	}

	/* Default display of the blog module. Shows one blog, with some built in features */
	function showBlog()
	{
		global $session, $files, $config;

		//Looks for formatted blog section commands, like: Blog:ID, BlogEdit:ID, BlogDelete:ID, BlogReport:ID, BlogComment:ID, BlogFiles:ID
		$cmd = fetchSpecialParams($config['blog']['allowed_tabs']);
		if ($cmd) list($current_tab, $_id) = $cmd;
		if (empty($_id) || !is_numeric($_id)) return false;

		if (isset($_POST['blog_cat']) && isset($_POST['blog_title']) && isset($_POST['blog_body'])) {
			updateBlog($_id, $_POST['blog_cat'], $_POST['blog_title'], $_POST['blog_body']);
		}

		$blog = getBlog($_id);
		if (!$blog) {
			echo 'The specified blog doesnt exist!<br/>';
			return false;
		}
		
		if ($blog['deletedBy']) {
			echo 'This blog has been deleted!<br/>';
			return false;
		}

		echo '<div class="blog">';

		echo '<div class="blog_head">';
		echo '<div class="blog_title">'.$blog['blogTitle'].'</div>';
		if ($blog['categoryName']) echo '(category <b>'.$blog['categoryName'].'</b>)<br/><br/>';
		else echo ' (no category)<br/><br/>';
		echo 'Published '. $blog['timeCreated'].' by '.nameLink($blog['userId'], $blog['userName']).'<br/>';
		echo '</div>'; //class="blog_head"

		$menu = array($_SERVER['PHP_SELF'].'?Blog:'.$_id => 'Show blog');
		if ($session->id == $blog['userId'] || $session->isAdmin) {
			$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogEdit:'.$_id => 'Edit blog'));
			$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogFiles:'.$_id => 'Attachments ('.$files->getFileCount(FILETYPE_BLOG, $_id).')'));
			$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogDelete:'.$_id => 'Delete blog'));
		} else {
			$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogReport:'.$_id => 'Report blog'));
		}
		$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogComment:'.$_id => 'Comments ('.getCommentsCount(COMMENT_BLOG, $_id).')'));

		createMenu($menu, 'blog_menu');

		echo '<div class="blog_body">';

		if ($current_tab == 'BlogEdit' && (($session->id && $session->id == $blog['userId']) || $session->isAdmin) ) {
			echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?BlogEdit:'.$_id.'">';
			echo '<input type="text" name="blog_title" value="'.$blog['blogTitle'].'" size="40" maxlength="40"/>';

			echo ' Category: ';
			echo getCategoriesSelect(CATEGORY_BLOG, 0, 'blog_cat', $blog['categoryId']);
			echo '<br/><br/>';

			$body = trim($blog['blogBody']);
			//convert | to &amp-version since it's used as a special character:
			$body = str_replace('|', '&#124;', $body);	//	|		vertical bar
			$body = $body."\n";	//always start with an empty line when getting focus

			echo '<textarea name="blog_body" cols="65" rows="25">'.$body.'</textarea><br/><br/>';
			echo '<input type="submit" class="button" value="Save changes"/><br/>';
			echo '</form>';

			if ($blog['timeUpdated']) {
				echo '<div class="blog_foot">Last updated '. $blog['timeUpdated'].'</div>';
			}

		} else if ($current_tab == 'BlogDelete' && (($session->id && $session->id == $blog['userId']) || $session->isAdmin) ) {
			//fixme: använd standard-are-you-sure funktionen

			if (isset($_GET['confirmed'])) {
				deleteBlog($_id);
				echo 'The blog has been deleted.<br/>';
			} else {
				echo 'Are you sure you want to delete this blog? <b>'.$blog['blogTitle'].'</b>?<br><br>';
				echo '<table width="100%"><tr>';
				echo '<td width="50%" align="center"><a href="'.$_SERVER['PHP_SELF'].'?BlogDelete:'.$_id.'&confirmed">Yes, im sure</a></td>';
				echo '<td align="center"><a href="javascript:history.go(-1);">No</a></td>';
				echo '</tr></table>';
			}
		} else if ($current_tab == 'BlogReport') {

			if (isset($_POST['blog_reportreason'])) {
				$queueId = addToModerationQueue(MODERATION_BLOG, $_id);
				addComment(COMMENT_MODERATION_QUEUE, $queueId, $_POST['blog_reportreason']);

				echo 'Your report has been recieved<br/>';
			} else {
				echo 'Report blog - <b>'.$blog['blogTitle'].'</b><br/><br/>';
	
				echo 'Why do you want to report this:<br/>';
				echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?BlogReport:'.$_id.'">';
				echo '<textarea name="blog_reportreason" cols="64" rows="6"></textarea><br/><br/>';
	
				echo '<input type="submit" class="button" value="Report"/>';
				echo '</form>';
			}

		} else if ($current_tab == 'BlogComment') {

			showComments(COMMENT_BLOG, $_id);

		} else if ($current_tab == 'BlogFiles') {

			echo $files->showFiles(FILETYPE_BLOG, $_id);

		} else {
			echo formatUserInputText($blog['blogBody']);

			if ($blog['timeUpdated']) {
				echo '<div class="blog_foot">Last updated '. $blog['timeUpdated'].'</div>';
			}
		}

		echo '</div>';

		echo '</div>'; //class="blog"
	}

	/* Displays one user's blogs overview */	
	function showUserBlogs()
	{
		global $session;


		if (isset($_GET['id']) && is_numeric($_GET['id'])) {
			$userId = $_GET['id'];
			echo 'User '.getUserName($userId).' - blogs:<br/>';
		} else {
			$userId = $session->id;
			echo 'Your blogs:<br/>';
		}

		$list = getBlogsByCategory($userId);

		$shown_category = false;
		foreach ($list as $row) {
			if ($row['categoryId'] != $shown_category) {
				if (!$row['categoryName']) echo '<div class="blogs_cathead">Uncategorized</div>';
				else echo '<div class="blogs_cathead">'.$row['categoryName'].'</div>';
				$shown_category = $row['categoryId'];
			}
			echo '<div class="X">';
			echo '<a href="blog.php?Blog:'.$row['blogId'].'">'.$row['blogTitle'].'</a><br/>';
			echo $row['timeCreated'];
			echo '</div>';
		}
	}
	
?>