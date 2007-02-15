<?
	include('include_all.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (!empty($_POST['word']) && !empty($_POST['lang']) && is_numeric($_POST['lang']) && !empty($_POST['pron'])) {
		$wordId = addWord($db, $_POST['lang'], $_POST['word'], $_POST['pron']);
		
		if (is_numeric($wordId)) {
			header('Location: word.php?id='.$wordId);
			die;
		}
	}

	include('design_head.php');
?>
	<h2>Add new word</h2>
	
	<form method="post" action="">
		Word: <input type="text" name="word"><br>
		Pronunciation: <input type="text" name="pron"><br>
		Language: <select name="lang"><?=getCategoriesHTML_Options($db, CATEGORY_LANGUAGES)?></select>
		<input type="submit" value="Add">
	</form>

<?	
	include('design_foot.php');
	if (isset($wordId)) JS_Alert('FAILED TO ADD WORD!! word already exists');
?>