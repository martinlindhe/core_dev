<?
	require_once('config.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	require('design_head.php');
	
	$text = '';
	if (!empty($_POST['text'])) {
		$text = $_POST['text'];
		
		guessLanguage($db, $text);
	}
	
?>
	<h2>Guess language</h2>
	
	Enter some text and see if the program can guess the language that the text were written in.
	
	<form method="post" action="">
		Text: <textarea name="text" rows=20 cols=70><?=$text?></textarea><br>
		<input type="submit" value="Submit">
	</form>

<?	
	require('design_foot.php');
?>