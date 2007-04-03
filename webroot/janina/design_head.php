<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
<!-- Design and coding by Martin Lindhe, martin_lindhe at yahoo dot se -->
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<script type="text/javascript" src="/js/functions.js"/>
	<script type="text/javascript" src="/js/ajax.js"/>
	<link rel="stylesheet" href="/css/functions.css" type="text/css"/>
	<link rel="stylesheet" href="css/main.css" type="text/css"/>
<?
		$showCategory = 1;
		if (!empty($_GET['c']) && is_numeric($_GET['c'])) $showCategory = $_GET['c'];

		echo '<title>Janina Magnusson - '.$files->getCategoryName($showCategory).'</title>';
?>
	</head>
<body>
