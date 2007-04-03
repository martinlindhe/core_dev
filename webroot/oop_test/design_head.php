<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
<head>
	<title>x</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
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