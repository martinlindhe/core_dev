<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
<title>ajax upload</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="design/site.css" type="text/css">
<link rel="shortcut icon" href="design/ai_icon.ico" type="image/x-icon">
<script type="text/javascript" src="js/functions.js"></script>
</head>

<body onLoad="ImagePreload()">

<div id="menu_holder">
	<div id="menu_left">
		<center><a href="<?=$config['start_page']?>">logo-placeholder</a></center><br>

		<form name="upload" method="post" enctype="multipart/form-data" action="upload.php">
		<input type="file" name="file">
		<input type="submit" value="Upload">
		</form>
		<br>

<?
		if (!$_SESSION['loggedIn']) {
			echo 'You are not logged in.<br><br>';
			echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
			echo 'Username: <input type="text" name="usr" size=10><br><br>';
			echo 'Password: <input type="password" name="pwd" size=10><br><br>';
			echo '<input type="submit" class="button" value="Log in">';
			echo '</form>';
		} else {
			
			$script = basename($_SERVER['SCRIPT_NAME']);

			if ($_SESSION['isSuperAdmin']) {
				if ($script == 'admin_tasks.php') echo '<b>';
				echo '&nbsp;&raquo; <a href="admin_tasks.php">Administrative tasks</a><br>';
				if ($script == 'admin_tasks.php') echo '</b>';
			}
			
			echo '<a href="'.$_SERVER['PHP_SELF'].'?logout">Log out</a>';
		}
?>
		<br>
	</div><!-- end menu_left -->
	<div id="menu_footer">
		AJAX Upload 1.0-dev<br>
		&copy; 2006 <a href="http://martin-lindhes.blogspot.com/" target="_blank">Martin Lindhe</a>
	</div>	
	<div id="menu_middle">
<!-- head end -->