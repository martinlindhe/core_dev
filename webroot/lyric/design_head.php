<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?=isset($title)?$title:'lyrics'?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<script type="text/javascript" src="/js/functions.js"></script>

	<link rel="stylesheet" href="/css/core.css" type="text/css"/>
	<link rel="stylesheet" href="/css/themes/default.css" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="css/site.css"/>
</head>
<body>
<div id="left_nav">

<?
	$menu = array(
		'index.php' => 'Start page',
		'list_bands.php' => 'List bands',
		'missing_lyrics.php' => 'Missing lyrics',
		'incomplete_lyrics.php' => 'Incomplete lyrics');
	createMenu($menu);
	
	if ($session->id) {
		$menu = array(
			'add_band.php' => 'Add band',
			'add_record.php' => 'Add normal record',
			'add_record_comp.php' => 'Add comp. / split',
			'?logout' => 'Log out');
	} else {
		$menu = array('?login' => 'Log in');
	}
	createMenu($menu);
?>


<form name="search" method="post" action="search.php">
	<input type="text" size="18" name="query"/><br/>
	<input type="submit" value="Search" class="button"/>
</form>

</div>

<div id="main">