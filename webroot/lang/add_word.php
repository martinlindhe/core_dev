<?
	require_once('config.php');

	$session->requireLoggedIn();

	if (!empty($_POST['word']) && !empty($_POST['lang']) && is_numeric($_POST['lang']) && !empty($_POST['pron'])) {
		$wordId = addWord($_POST['lang'], $_POST['word'], $_POST['pron']);
		
		if (is_numeric($wordId)) {
			header('Location: word.php?id='.$wordId);
			die;
		}
	}

	require('design_head.php');
?>
	<h2>Add new word</h2>
	
	<form method="post" action="">
		Word: <input type="text" name="word"/><br/>
		Pronunciation: <input type="text" name="pron"/><br/>
		Language: <?=getCategoriesSelect(CATEGORY_LANGUAGE, 'lang')?>
		<input type="submit" class="button" value="Add"/>
	</form>

<?	
	require('design_foot.php');
	if (isset($wordId)) JS_Alert('FAILED TO ADD WORD!! word already exists');
?>