<?
	include('include_all.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_POST['name'])) {
		$global = false;
		if (!empty($_POST['global'])) $global = true;
		$categoryId = addCategory($db, CATEGORY_BLOGS, $_POST['name'], $global);
		if ($categoryId) {
			header('Location: blogs.php');
			die;
		} else {
			JS_Alert('Problems creating blog category!');
		}
	}

	include('design_head.php');
	include('design_user_head.php');

	$content  = $config['blog']['text']['new_blog_category_desc'].'<br><br>';
	$content .= $config['text']['old_categories'].':<br>';
	$content .= '<select>';
	$content .= getCategoriesHTML_Options($db, CATEGORY_BLOGS);
	$content .= '</select>';
	$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	$content .= $config['text']['category_name'].':<br><input type="text" name="name" size=42 maxlength=40><br><br>';
	if ($_SESSION['isAdmin']) {
		$content .= '<input type="checkbox" name="global" value="1">'.$config['text']['global_category'].'<br><br>';
	}
	$content .= '<input type="submit" class="button" value="'.$config['text']['link_save'].'">';
	$content .= '</form><br>';

	$content .= '<a href="javascript:history.go(-1);">'.$config['text']['link_return'].'</a>';

	echo '<div id="user_blog_content">';
	echo MakeBox('<a href="blogs.php">'.$config['blog']['text']['blogs'].'</a>|'.$config['blog']['text']['new_blog_category'], $content);
	echo '</div>';

	include('design_blog_foot.php');
	include('design_foot.php');
?>