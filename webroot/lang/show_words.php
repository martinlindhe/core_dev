<?
	require_once('config.php');

	$session->requireLoggedIn();

	require('design_head.php');

	if (empty($_GET['lang']) || !is_numeric($_GET['lang'])) {
		echo '<h2>Show words</h2>Select language first<br/><br/>';

		$list = getCategories(CATEGORY_LANGUAGES);
		for ($i=0; $i<count($list); $i++) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?lang='.$list[$i]['categoryId'].'">'.$list[$i]['categoryName'].'</a><br/>';
		}
	} else {
		echo '<h2>Show words</h2>';

		$list = getWords($_GET['lang']);

		foreach ($list as $row) {
			echo $row['word'].'<br/>';
		}
	}

	require('design_foot.php');
?>