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

	$content  = 'Her kan du lage nye blogg-kategorier.<br><br>';
	$content .= 'Disse kategoriene finnes fra f&oslash;r.<br>';
	$content .= '<select>';
	$content .= getCategoriesHTML_Options($db, CATEGORY_BLOGS);
	$content .= '</select>';
	$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	$content .= 'Navn p&aring; kategorien.<br><input type="text" name="name" size=42 maxlength=40><br><br>';
	if ($_SESSION['isAdmin']) {
		$content .= '<input type="checkbox" name="global" value="1">Gj&oslash;r denne kategorien global(tilgjengelig for alle)<br><br>';
	}
	$content .= '<input type="submit" class="button" value="Lagre">';
	$content .= '</form><br>';

	$content .= '<a href="javascript:history.go(-1);">'.$config['text']['link_return'].'</a>';

	echo '<div id="user_blog_content">';
	echo MakeBox('<a href="blogs.php">Blogger</a>|Ny blogg-kategori', $content);
	echo '</div>';

	include('design_blog_foot.php');
	include('design_foot.php');
?>