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
	<link rel="stylesheet" href="css/site.css" type="text/css">

	<script type="text/javascript" src="/js/functions.js"></script>
</head>
<body>

<div id="left_menu">
<?
	$menu = array(
			'index.php' => 'Home',
			'blogs.php' => 'Blogs',
			'blogs_archive.php?y=2006&m=7' => 'Blog archive');

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
</div> <!-- header_holder -->

<div id="main_body">


<!-- design_head.php end -->