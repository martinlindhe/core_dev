<?php

/**
 * Tool to generate random acronyms of selected language
 */

require_once('config.php');
$session->requireLoggedIn();

require('design_head.php');

echo '<h2>Generate acronyms</h2>';

if (!empty($_POST['acro'])) {
	$list = generateAcronyms($_POST['lang'], $_POST['acro'], $_POST['ammount']);
	echo 'Acronyms created:<br/>';
	d($list);
}

?>
Enter an acronym and choose the language you want.

<form method="post" action="">
	Acronym: <input type="text" name="acro"/><br/>
	Language: <?php echo xhtmlSelectCategory(CATEGORY_LANGUAGE,0,'lang')?><br/>
	Ammount: <?php echo xhtmlSelectNumeric('ammount',1,50)?><br/>
	<input type="submit" class="button" value="Generate"/>
</form>
<?php

require('design_foot.php');
?>
