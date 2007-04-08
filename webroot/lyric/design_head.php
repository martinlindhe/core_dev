<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?=isset($title)?$title:'lyrics'?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<script type="text/javascript" src="/js/functions.js"></script>
	<link rel="stylesheet" type="text/css" href="/css/functions.css"/>
	<link rel="stylesheet" type="text/css" href="css/main.css"/>
</head>
<body>
<div id="left_nav">

<a href="index.php">Start page</a><br/>
<br/>
<?
	if ($session->id) {
?>
	<a href="add_band.php">Add band</a><br/>
	<a href="add_record.php">Add normal record</a><br/>
	<a href="add_record_comp.php">Add comp. / split</a><br/>
	<a href="?logout">Log out</a><br/>
<?
	} else {
?>
	<a href="login.php">Log in</a><br/>
<?
	}
?>

<a href="list_bands.php">List bands</a><br/>
<br/>

<a href="missing_lyrics.php">List missing lyrics</a><br/>
<a href="incomplete_lyrics.php">List incomplete lyrics</a><br/>
<br/>

<form name="search" method="post" action="search.php">
	<input type="text" size="18" name="query"/><br/>
	<input type="submit" value="Search" class="button"/>
</form>

<?
	if ($session->isAdmin) {
		echo '<br/>';
		echo 'Moderation:<br/>';

		echo countNewAdditions().' new additions.<br/>';
		echo countPendingChanges().' pending changes.<br/>';
		echo '<a href="moderate.php">Go moderate</a>';
	} else {
?>

	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="subtitlemod" width="15">&nbsp;</td>
			<td>Fields marked in this color means new additions/pending changes.</td>
		</tr>
	</table>
	<br/><br/>
<?
	}
?>
</div>

<div id="main">