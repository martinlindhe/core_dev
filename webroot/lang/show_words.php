<?
	require_once('config.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	require('design_head.php');
	
	if (empty($_GET['lang']) || !is_numeric($_GET['lang'])) {
		?><h2>Show words</h2>Select language first<br><br><?

		$list = getCategories($db, CATEGORY_LANGUAGES);
		for ($i=0; $i<count($list); $i++) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?lang='.$list[$i]['categoryId'].'">'.$list[$i]['categoryName'].'</a><br>';
		}
	} else {
		?><h2>Show words</h2><?
		
		$list = getWords($db, $_GET['lang']);
		
		for ($i=0; $i<count($list); $i++) {
			echo $list[$i]['word'].'<br>';
		}
	}

	require('design_foot.php');
?>