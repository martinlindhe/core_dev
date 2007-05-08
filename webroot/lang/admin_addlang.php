<?
	require_once('config.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	if (!empty($_POST['lang'])) {
		$langId = addCategory($db, CATEGORY_LANGUAGES, $_POST['lang']);
	}

	require('design_head.php');
?>
	<h2>Add new language</h2>
	
	These languages already exist: <select name="lang"><?=getCategoriesHTML_Options($db, CATEGORY_LANGUAGES)?></select>
	<br><br>
	
	<form method="post" action="">
		language: <input type="text" name="lang">
		<input type="submit" value="Add">
	</form>

<?	
	require('design_foot.php');

	if (!empty($langId)) JS_Alert('Language added');
?>