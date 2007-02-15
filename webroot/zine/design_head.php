<?
	if ($_SESSION['browser']['name'] == 'Internet Explorer') {
		$browser_css = 'main_ie.css';
	} else {
		$browser_css = 'main_ff.css';
	}

	$browser_css = 'main.css';
	
	$title = 'Zine';
	$keywords = 'web zine, fuck off';
	$description = 'Web zine go away';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?=$title?></title>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="keywords" content="<?=$keywords?>">
<meta name="description" content="<?=$description?>">

<link rel="stylesheet" href="inc/<?=$browser_css?>" type="text/css">
<link rel="stylesheet" href="inc/functions.css" type="text/css">
<link rel="stylesheet" href="inc/esp.css" type="text/css">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

<script type="text/javascript" src="inc/functions.js"></script>

<?
	/* Learn the current sessions screen resolution */
	if (empty($_SESSION['browser']) || !$_SESSION['browser']['width']) {
?>
<script type="text/javascript">
if (screen.width&&screen.height){SetCookie('BrowserWidth',screen.width);SetCookie('BrowserHeight',screen.height);}
</script>
<?
	}
?>

</head>
<body>

<div id="header_holder">
	<div id="header_left">
		<?
		if (!$_SESSION['loggedIn']) {
			echo
			'<form method="post" action="login.php">'.
			'&nbsp;Username: <input type="text" name="usr" size=10> '.
			'Password: <input type="password" name="pwd" size=10> '.
			'<input type="submit" class="button" value="Log in"> ';
	
			if (!empty($_SESSION['lastError'])) {
				echo '<span class="objectCritical">'.$_SESSION['lastError'].'</span>';
				unset($_SESSION['lastError']);
			}
			echo '</form>';
		} else {
			echo 'Welcome '.$_SESSION['userName'].'!';
		}
		?>
	</div> <!-- header_left -->
	<?
	if (!$_SESSION['loggedIn']) {
		echo '<a href="newuser.php">Register</a>';
	} else {
		echo '<a href="'.$_SERVER['PHP_SELF'].'?logout">Log out</a>';
	}
	?>
&nbsp;
</div> <!-- header_holder -->

<div id="body_holder">
	<div id="body_left">
		<ul>
			<li><a href="index.php">Startsidan</a></li>
<?
			if ($_SESSION['isAdmin']) echo '<li><a href="admin.php">Admin</a></li>';
?>
			<li><a href="blogs.php">Bloggar</a></li>
			<li><a href="settings.php">Inst&auml;llningar</a></li>
		</ul>
	</div> <!-- body_left -->

<!-- design_head.php end -->