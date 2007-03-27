<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<head>
	<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
<?
	if ($_SESSION['loggedIn'] == false) {
		echo 'You need to be logged in to submit changes/additions. <a href="login.php">Log in</a> | <a href="register.php">Register</a><br><br>';
	}
?>
<table cellpadding=0 cellspacing=0 border=0><tr><td class="subtitlemod" width=15>&nbsp;</td><td> Fields marked in this color means new additions/pending changes.</td></tr></table>
<br><br>