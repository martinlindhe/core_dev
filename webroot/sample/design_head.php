<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>sample site</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="/css/functions.css" type="text/css"/>
	<link rel="stylesheet" href="<?=$session->web_root?>css/site.css" type="text/css"/>
<?linkRSSfeeds()?>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
	<script type="text/javascript" src="/js/ajax.js"></script>
	<script type="text/javascript" src="/js/functions.js"></script>
</head>
<body>
<script type="text/javascript">
var _ext_ref='<?=getProjectPath(2)?>';
</script>
<div id="left-bg"></div>
<div id="header">
	<div id="header-logo">
		<img src="<?=$session->web_root?>gfx/logo.png" alt="Filterset Database"/>
	</div>
	<div id="header-items">
		<a href="<?=$session->web_root?>wiki.php?View:About">About</a>
		<a href="<?=$session->web_root?>wiki.php?View:Help">Help</a>
	</div>
</div>
<div id="leftmenu">
<?
	$menu = array(
			'index.php' => 'Home',
			'news.php' => 'News',
			'blogs.php' => 'Blogs');
	createMenu($menu);
	
	if ($session->isAdmin) {
		$menu = array(
			'/admin/admin.php'.getProjectPath(0) => 'Admin');
		createMenu($menu);
	}

	if ($session->id) {
		$menu = array(
			'user.php' => 'My profile',
			'?logout' => 'Logout');
	} else {
		$menu = array('?login' => 'Log in');
	}
	createMenu($menu);

?>
</div>

<div id="middle">
