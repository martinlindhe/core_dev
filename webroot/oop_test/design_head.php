<?
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
<head>
	<title>x</title>
	<script type="text/javascript" src="/js/functions.js"></script>
	<script type="text/javascript" src="/js/ajax.js"></script>
	<link rel="stylesheet" href="/css/functions.css" type="text/css"/>
</head>
<body>
<?
	if (!$session->id) {
		$session->showLoginForm();
	}
?>