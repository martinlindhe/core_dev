<?
	echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="se" lang="se">
<head>
	<title>x</title>
	<script type="text/javascript" src="/js/functions.js"/>
	<script type="text/javascript" src="/js/ajax.js"/>
	<link rel="stylesheet" href="/css/functions.css" type="text/css"/>
</head>
<body>
<?
	if (!$session->id) {
		$session->showLoginForm();
	}
?>