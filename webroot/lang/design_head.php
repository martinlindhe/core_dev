<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
<title>lang</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="css/main.css" type="text/css">
<link rel="shortcut icon" href="design/ai_icon.ico" type="image/x-icon">
<script type="text/javascript" src="js/functions.js"></script>
</head>

<body>

<div id="menu_holder">
	<div id="menu_left">
		<a href="index.php">Start</a><br>
		<br>
		<? if ($_SESSION['loggedIn']) { ?>
			<a href="addword.php">Add word</a><br>
			<a href="add_text.php">Add longer text</a><br>
			<a href="show_words.php">Show words</a><br>
			<a href="guess_language.php">Guess language</a><br>
			<br>
			<? if ($_SESSION['isAdmin']) { ?>
				<a href="admin_addlang.php">Add language</a><br>
				<br>
			<? } ?>
			
			<a href="<?=$_SERVER['PHP_SELF'].'?logout'?>">Log out</a>
		<? } else { ?>
			<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
				usr: <input type="text" name="usr"><br>
				pwd: <input type="password" name="pwd"><br>
				<input type="submit" value="login">
			</form>
		<? } ?>

	</div><!-- end menu_left -->
	<div id="menu_middle">
<!-- head end -->