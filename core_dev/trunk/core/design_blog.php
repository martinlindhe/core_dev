<?php

//XXX DEPRECATE use Blog class instead

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
