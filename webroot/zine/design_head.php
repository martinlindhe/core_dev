<?
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

	<link rel="stylesheet" href="inc/site.css" type="text/css">
	<link rel="stylesheet" href="/css/functions.css" type="text/css">

	<script type="text/javascript" src="/js/functions.js"></script>
</head>
<body>

<div id="header_holder">
	<div id="header_left">
		<? if (!$session->id) $session->showLoginForm(); ?>
	</div> <!-- header_left -->
	<?
	if ($session->id) {
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
			<li><a href="blogs.php">Blogs</a></li>
			<li><a href="blogs_archive.php?y=2006&m=7">Blog archive</a></li>
		</ul>
	</div> <!-- body_left -->

<!-- design_head.php end -->