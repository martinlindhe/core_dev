<?
	$text = '';
	if (!empty($_POST['text'])) $text = $_POST['text'];

	do {
		$t = $text;
		$text = str_replace("\n", ' ', $text);
		$text = str_replace("\r", ' ', $text);
		$text = str_replace("\t", ' ', $text);
		$text = str_replace('  ', ' ', $text);
		$text = str_replace('; ', ';', $text);		//might cause problems some day
	} while ($t != $text);

?>

<form method="post" action="">
	<textarea name="text" cols="80" rows="30"><?=$text?></textarea>
	<br/>
	Language:
	<select name="lang">
		<option value="1">PHP</option>
	</select>
	<input type="submit" class="button" value="Obfuscate"/>
</form>