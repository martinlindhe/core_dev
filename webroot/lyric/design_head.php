<?
	//<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?=isset($title)?$title:'lyrics'?></title>
	<!-- <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> -->
	<script type="text/javascript" src="/js/functions.js"></script>
	<link rel="stylesheet" type="text/css" href="/css/functions.css"/>
	<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<?
	if (!$session->id) {
		echo 'You need to be logged in to submit changes/additions. <a href="login.php">Log in</a> | <a href="register.php">Register</a><br/><br/>';
	}
?>
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td class="subtitlemod" width="15">&nbsp;</td>
		<td>Fields marked in this color means new additions/pending changes.</td>
	</tr>
</table>
<br/><br/>