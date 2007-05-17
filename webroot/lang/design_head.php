<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>lang</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="/css/core.css" type="text/css"/>
	<link rel="stylesheet" href="/css/themes/<?=$session->theme?>" type="text/css"/>
	<link rel="stylesheet" href="<?=$session->web_root?>css/site.css" type="text/css"/>
	<script type="text/javascript" src="/js/ajax.js"></script>
	<script type="text/javascript" src="/js/functions.js"></script>
</head>

<body>
<script type="text/javascript">
var _ext_ref='<?=getProjectPath(2)?>';
</script>

<div id="menu_holder">
	<div id="leftmenu">
<?
	$menu = array(
			'index.php' => 'Home');
	createMenu($menu);

	if ($session->isAdmin) {
		$menu = array(
			'admin_addlang.php' => 'Add language',
			'/admin/admin.php'.getProjectPath(0) => 'Admin');
		createMenu($menu);
	}

	if ($session->id) {
		$menu = array(
			'add_word.php' => 'Add word',
			'add_text.php' => 'Add longer text',
			'show_words.php' => 'Show words',
			'guess_language.php' => 'Guess language',
			'?logout' => 'Log out');
	} else {
		$menu = array('?login' => 'Log in');
	}
	createMenu($menu);
?>
	</div>

	<div id="middle">
<!-- head end -->