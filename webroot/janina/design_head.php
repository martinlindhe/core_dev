<?
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
<head>
	<script type="text/javascript" src="/js/functions.js"></script>
	<script type="text/javascript" src="/js/ajax.js"></script>
	<link rel="stylesheet" type="text/css" href="/css/functions.css"/>
	<link rel="stylesheet" type="text/css" href="css/main.css"/>
<?
		$showCategory = 1;
		if (!empty($_GET['c']) && is_numeric($_GET['c'])) $showCategory = $_GET['c'];

		echo '<title>Janina Magnusson - '.getCategoryName($showCategory).'</title>';
?>
	</head>
<body>
