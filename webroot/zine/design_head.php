<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>zine</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" href="inc/site.css" type="text/css">
	<link rel="stylesheet" href="/css/functions.css" type="text/css">
	<link rel="stylesheet" href="css/site.css" type="text/css">
<?linkRSSfeeds()?>
	<script type="text/javascript" src="/js/functions.js"></script>
</head>
<body>
<script type="text/javascript">
var _ext_ref='<?=getProjectPath()?>';
</script>

<div id="left_menu">
<?
	$menu = array(
			'index.php' => 'Home',
			'blogs.php' => 'Blogs',
			'blogs_archive.php?y=2006&amp;m=7' => 'Blog archive');

	createMenu($menu);
		
	if ($session->isAdmin) {
		$menu = array(
			'/admin/admin.php'.getProjectPath(false) => 'Admin');
		createMenu($menu);
	}

	if ($session->id) {
		$menu = array(
			'?logout' => 'Logout');
		createMenu($menu);
	}
?>
</div>

<div id="main_body">
