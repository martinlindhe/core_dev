<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>lang</title>
	<script type="text/javascript" src="js/functions.js"></script>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="/css/functions.css" type="text/css"/>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
	<script type="text/javascript" src="/js/ajax.js"></script>
	<script type="text/javascript" src="/js/functions.js"></script>

</head>

<body>

<div id="menu_holder">
	<div id="menu_left">
		<a href="index.php">Start</a><br/>
		<br/>
		<? if ($session->id) { ?>
			<a href="addword.php">Add word</a><br/>
			<a href="add_text.php">Add longer text</a><br/>
			<a href="show_words.php">Show words</a><br/>
			<a href="guess_language.php">Guess language</a><br/>
			<br/>
			<? if ($session->isAdmin) { ?>
				<a href="admin_addlang.php">Add language</a><br/>
				<br/>
			<? } ?>
			
			<a href="?logout">Log out</a>
		<? } ?>
	</div>

	<div id="menu_middle">
<!-- head end -->