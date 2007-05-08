<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
<title>blog</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="css/site.css" type="text/css">
<link rel="shortcut icon" href="design/ai_icon.ico" type="image/x-icon">
<script type="text/javascript" src="js/functions.js"></script>
</head>

<body>

<div id="menu_holder">
	<div id="menu_left">
		<a href="index.php">Start</a><br>
		<br>
		<? if ($_SESSION['loggedIn']) { ?>
			<a href="blog_new.php">Ny blogg</a><br>
			<a href="blog_categories.php">Kategorier</a><br>
			<br>

			<a href="<?=$_SERVER['PHP_SELF'].'?logout'?>">Logga ut</a>
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