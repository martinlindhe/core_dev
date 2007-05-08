<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>lang</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="css/site.css" type="text/css"/>
	<link rel="stylesheet" href="/css/functions.css" type="text/css"/>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
	<script type="text/javascript" src="/js/ajax.js"></script>
	<script type="text/javascript" src="/js/functions.js"></script>
</head>

<body>
<script type="text/javascript">
var _ext_ref='<?=getProjectPath()?>';
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
			'/admin/admin.php'.getProjectPath(false) => 'Admin');
		createMenu($menu);
	}

	if ($session->id) {
		$menu = array(
			'add_word.php' => 'Add word',
			'add_text.php' => 'Add longer text',
			'show_words.php' => 'Show words',
			'guess_language.php' => 'Guess language',
			'?logout' => 'Logout');
		createMenu($menu);
	}
?>
	</div>

	<div id="middle">
<!-- head end -->