<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>process server</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="/css/functions.css" type="text/css"/>
	<link rel="stylesheet" href="<?=$session->web_root?>css/site.css" type="text/css"/>
	<script type="text/javascript" src="/js/functions.js"></script>
</head>
<body>

<div id="left_menu">
<?
	$menu = array(
		'index.php' => 'Home'
	);
	createMenu($menu);
	
	if ($session->isAdmin) {
		$menu = array(
			'perform_work.php' => 'Perform work',
			'/admin/admin.php'.getProjectPath(0) => 'Admin'
		);
		createMenu($menu);
	}

	if ($session->id) {
		$menu = array(
			'?logout' => 'Log out'
		);
		createMenu($menu);
	}
?>
</div>

<div id="main_body">
