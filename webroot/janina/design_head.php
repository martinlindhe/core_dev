<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<!-- Design and coding by Martin Lindhe, martin_lindhe at yahoo dot se -->

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<script type="text/javascript" src="/js/functions.js"></script>
		<script type="text/javascript" src="/js/ajax.js"></script>
		<link rel="stylesheet" href="/css/functions.css" type="text/css">
		<link rel="stylesheet" href="css/main.css" type="text/css">
<?
		$showCategory = 1;
		if (!empty($_GET['c']) && is_numeric($_GET['c'])) $showCategory = $_GET['c'];

		echo '<title>Janina Magnusson - '.$files->getCategoryName($showCategory).'</title>';
?>
	</head>
<body>
