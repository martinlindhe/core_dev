<?php

require_once('config.php');
$session->requireAdmin();

if (!empty($_POST['lang'])) {
	addCategory(CATEGORY_LANGUAGE, $_POST['lang']);
}

require('design_head.php');
?>

<h2>Add new language</h2>

These languages already exist: <?=xhtmlSelectCategory(CATEGORY_LANGUAGE)?>
<br/><br/>

<form method="post" action="">
	language: <input type="text" name="lang"/>
	<input type="submit" class="button" value="Add"/>
</form>

<?php

require('design_foot.php');
?>
