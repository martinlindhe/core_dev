<?
	require_once('config.php');

	$session->requireLoggedIn();

	if (isset($_POST['name'])) {
		$global = false;
		if (!empty($_POST['global'])) $global = true;
		$categoryId = addCategory(CATEGORY_BLOGS, $_POST['name'], $global);
		if ($categoryId) {
			header('Location: blogs.php');
			die;
		} else {
			JS_Alert('Problems creating blog category!');
		}
	}

	include('design_head.php');

	$content  = 'Create a new category...<br><br>';
	$content .= 'The already existing categories are: <br>';
	$content .= getCategoriesSelect(CATEGORY_BLOGS);
	$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	$content .= 'Category name:<br><input type="text" name="name" size="42" maxlength="40"><br/><br/>';
	if ($session->isAdmin) {
		$content .= '<input type="checkbox" name="global" value="1"/>Global category<br/><br/>';
	}
	$content .= '<input type="submit" class="button" value="Save"/>';
	$content .= '</form><br/>';

	echo $content;

	include('design_foot.php');
?>