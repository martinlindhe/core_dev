<?php

/**
 * Default display of the blog module. Shows one blog, with some built in features
 */
function showBlog()
{
	global $h, $config;

	//Looks for formatted blog section commands, like: Blog:ID, BlogEdit:ID, BlogDelete:ID, BlogReport:ID, BlogComment:ID, BlogFiles:ID
	$cmd = fetchSpecialParams($config['blog']['allowed_tabs']);
	if ($cmd) list($current_tab, $_id) = $cmd;
	if (empty($_id) || !is_numeric($_id)) return false;

	$blog = getBlog($_id);
	if (!$blog) {
		echo 'The specified blog doesnt exist!<br/>';
		return false;
	}

	if ($blog['deletedBy']) {
		echo 'This blog has been deleted!<br/>';
		return false;
	}

	if (($h->session->id == $blog['userId'] || $h->session->isAdmin) && isset($_POST['blog_cat']) && isset($_POST['blog_title']) && isset($_POST['blog_body'])) {
		updateBlog($_id, $_POST['blog_cat'], $_POST['blog_title'], $_POST['blog_body']);
		$blog = getBlog($_id);
	}

	echo '<div class="blog">';

	echo '<div class="blog_head">';
	echo '<div class="blog_title">'.$blog['subject'].'</div>';
	if ($blog['categoryName']) echo '(category <b>'.$blog['categoryName'].'</b>)<br/><br/>';
	else echo ' (no category)<br/><br/>';
	echo 'Published '. $blog['timeCreated'].' by '.Users::link($blog['userId'], $blog['userName']).'<br/>';
	echo '</div>'; //class="blog_head"

	$menu = array($_SERVER['PHP_SELF'].'?Blog:'.$_id => 'Show blog');
	if ($h->session->id == $blog['userId'] || $h->session->isSuperAdmin) {
		$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogEdit:'.$_id => 'Edit blog'));
		$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogFiles:'.$_id => 'Attachments ('.$h->files->getFileCount(FILETYPE_BLOG, $_id).')'));
		$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogDelete:'.$_id => 'Delete blog'));
	}
	if ($h->session->id && $h->session->id != $blog['userId']) {
		$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogReport:'.$_id => 'Report blog'));
	}
	$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogComment:'.$_id => 'Comments ('.getCommentsCount(COMMENT_BLOG, $_id).')'));

	createMenu($menu, 'blog_menu');

	echo '<div class="blog_body">';

	if ($current_tab == 'BlogEdit' && ($h->session->id == $blog['userId'] || $h->session->isAdmin) ) {
		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?BlogEdit:'.$_id.'">';
		echo '<input type="text" name="blog_title" value="'.$blog['subject'].'" size="40" maxlength="40"/>';

		echo ' Category: ';
		echo xhtmlSelectCategory(CATEGORY_BLOG, 0, 'blog_cat', $blog['categoryId']);
		echo '<br/><br/>';

		$body = trim($blog['body']);
		//convert | to &amp-version since it's used as a special character:
		$body = str_replace('|', '&#124;', $body);	//	|		vertical bar
		$body = $body."\n";	//always start with an empty line when getting focus

		echo '<textarea name="blog_body" cols="65" rows="25">'.$body.'</textarea><br/><br/>';
		echo '<input type="submit" class="button" value="Save changes"/><br/>';
		echo '</form>';

		if ($blog['timeUpdated']) {
			echo '<div class="blog_foot">Last updated '. $blog['timeUpdated'].'</div>';
		}

	} else if ($current_tab == 'BlogDelete' && (($h->session->id && $h->session->id == $blog['userId']) || $h->session->isAdmin) ) {

		if (confirmed('Are you sure you want to delete this blog?', 'BlogDelete:'.$_id)) {
			deleteBlog($_id);
			echo 'The blog has been deleted.<br/>';
		}

	} else if ($current_tab == 'BlogReport' && $h->session->id) {

		if (isset($_POST['blog_reportreason'])) {
			$queueId = addToModerationQueue(MODERATION_BLOG, $_id);
			addComment(COMMENT_MODERATION, $queueId, $_POST['blog_reportreason']);

			echo 'Your report has been recieved<br/>';
		} else {
			echo 'Report blog - <b>'.$blog['subject'].'</b><br/><br/>';

			echo 'Why do you want to report this:<br/>';
			echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?BlogReport:'.$_id.'">';
			echo '<textarea name="blog_reportreason" cols="64" rows="6"></textarea><br/><br/>';

			echo '<input type="submit" class="button" value="Report"/>';
			echo '</form>';
		}

	} else if ($current_tab == 'BlogComment') {

		echo showComments(COMMENT_BLOG, $_id);

	} else if ($current_tab == 'BlogFiles' && ($h->session->id == $blog['userId'] || $h->session->isAdmin)) {

		echo showFiles(FILETYPE_BLOG, $_id);

	} else {

		echo formatUserInputText($blog['body']);

		if ($blog['timeUpdated']) {
			echo '<div class="blog_foot">Last updated '. $blog['timeUpdated'].'</div>';
		}

		if ($config['blog']['allow_rating']) {
			echo '<div class="news_rate">';
			if ($h->session->id != $blog['userId']) {
				echo ratingGadget(RATE_BLOG, $_id);
			} else {
				echo showRating(RATE_BLOG, $_id);
			}
			echo '</div>';
		}
	}

	echo '</div>';
	echo '</div>'; //class="blog"
}

/**
 * Displays one user's blogs overview
 */
function showUserBlogs($_userid_name = '')
{
	global $h;

	if ($_userid_name && isset($_GET[$_userid_name]) && is_numeric($_GET[$_userid_name])) {
		$userId = $_GET[$_userid_name];
		echo 'Blogs:'.Users::getName($userId).'<br/>';
	} else {
		$userId = $h->session->id;
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
		echo '<a href="blog.php?Blog:'.$row['blogId'].'">'.$row['subject'].'</a><br/>';
		echo $row['timeCreated'];
		echo '</div>';
	}
}

?>
