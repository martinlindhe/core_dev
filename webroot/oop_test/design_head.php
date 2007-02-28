<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>x</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<script type="text/javascript" src="/js/functions.js"></script>
		<script type="text/javascript" src="/js/ajax.js"></script>
		<link rel="stylesheet" href="/css/functions.css" type="text/css">
	</head>
<body>
<?
	//todo: fixa denna sökväg
	require_once('../layout/zoom_layer.html');

	if (!$session->id) {
		$session->showLoginForm();
	}
?>