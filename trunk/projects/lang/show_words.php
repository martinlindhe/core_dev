<?php

require_once('config.php');
$session->requireLoggedIn();

require('design_head.php');

if (!empty($_GET['del'])) {
	deleteWord($_GET['del']);
}

if (empty($_GET['lang']) || !is_numeric($_GET['lang'])) {
	echo '<h2>Show words</h2>';
	echo 'Select language first<br/><br/>';

	$list = getCategories(CATEGORY_LANGUAGE);
	foreach ($list as $row) {
		echo '<a href="'.$_SERVER['PHP_SELF'].'?lang='.$row['categoryId'].'">'.$row['categoryName'].'</a> ('.getWordCount($row['categoryId']).' words)<br/>';
	}
} else {
	echo '<h2>Show words</h2>';

	$langId = $_GET['lang'];
	$list = getWords($langId);

	foreach ($list as $row) {
		coreButton('Delete', $_SERVER['PHP_SELF'].'?lang='.$langId.'&amp;del='.$row['id']);
		echo $row['word'].'<br/>';
	}
}

require('design_foot.php');
?>
