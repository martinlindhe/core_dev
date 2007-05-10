<?
	require_once('config.php');

	$session->requireLoggedIn();

	require('design_head.php');

	echo 'Create a new category...<br/><br/>';
	echo 'The already existing categories are: <br/>';
	echo getCategoriesSelect(CATEGORY_BLOG);

	echo makeNewCategoryDialog(CATEGORY_BLOG);

/*
	if (isset($_POST['name'])) {
		$global = false;
		if (!empty($_POST['global'])) $global = true;
		$categoryId = addCategory(CATEGORY_BLOGS, $_POST['name'], $global);
		if ($categoryId) {
			header('Location: blogs.php');
			die;
		} else {
			echo '<span class="critical">Problems creating blog category!</span>';
		}
	}

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo 'Category name:<br><input type="text" name="name" size="42" maxlength="40"><br/><br/>';
	if ($session->isAdmin) {
		echo '<input type="checkbox" name="global" value="1"/>Global category<br/><br/>';
	}
	echo '<input type="submit" class="button" value="Save"/>';
	echo '</form><br/>';
*/

	require('design_foot.php');
?>