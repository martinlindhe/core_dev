<?php

$header = new xhtml_header();
echo $header->render();
?>
<div id="menu_holder">
	<div id="leftmenu">
<?php

$menu = array('index.php' => 'Home');
createMenu($menu);

if ($session->isAdmin) {
	$menu = array(
		'admin_addlang.php' => 'Add language',
		$config['core']['web_root'].'admin/admin.php'.getProjectPath(0) => 'Admin'
	);
	createMenu($menu);
}

if ($session->id) {
	$menu = array(
		'add_word.php' => 'Add word',
		'add_text.php' => 'Add longer text',
		'show_words.php' => 'Show words',
		'guess_language.php' => 'Guess language',
		'acronym.php' => 'Make acronyms',
		'?logout' => 'Log out'
	);
	createMenu($menu);
}
?>
	</div>

	<div id="middle">
<!-- head end -->
