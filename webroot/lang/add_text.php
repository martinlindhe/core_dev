<?
	include('include_all.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	
	$selectedLang = 0;
	$text = '';

	if (!empty($_POST['text']) && !empty($_POST['lang']) && is_numeric($_POST['lang'])) {
		$selectedLang = $_POST['lang'];
		
		$text = $_POST['text'];

		analyzeText($db, $selectedLang, $text);
	}

?>
	<h2>Add text</h2>
	
	Here you can add longer chunks of text, and choose a language.<br>
	Each unique words, and their relations with other words within the sentences will be recorded.<br>
	Only useful for natural written language.<br>
	
	<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
		Language: <select name="lang"><?=getCategoriesHTML_Options($db, CATEGORY_LANGUAGES, $selectedLang)?></select><br>
		Text: <textarea name="text" rows=20 cols=70><?=$text?></textarea><br>
		<input type="submit" value="Add">
	</form>

<?	
	include('design_foot.php');
	if (isset($wordId)) JS_Alert('FAILED TO ADD WORD!! word already exists');
?>