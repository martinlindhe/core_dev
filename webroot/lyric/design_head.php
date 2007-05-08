<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?=isset($title)?$title:'lyrics'?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<script type="text/javascript" src="/js/functions.js"></script>
	<link rel="stylesheet" type="text/css" href="/css/functions.css"/>
	<link rel="stylesheet" type="text/css" href="css/site.css"/>
</head>
<body>
<div id="left_nav">

<?
	$menu = array(
		'index.php' => 'Start page',
		'login.php' => 'Log in',
		'list_bands.php' => 'List bands',
		'missing_lyrics.php' => 'Missing lyrics',
		'incomplete_lyrics.php' => 'Incomplete lyrics');
	createMenu($menu, 'side-nav');
	
	if ($session->id) {
		$menu = array(
			'add_band.php' => 'Add band',
			'add_record.php' => 'Add normal record',
			'add_record_comp.php' => 'Add comp. / split',
			'?logout' => 'Log out');
		createMenu($menu, 'side-nav');
	}
?>


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
	}
?>

	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="subtitlemod" width="15">&nbsp;</td>
			<td>Fields marked like this is new additions / pending changes.</td>
		</tr>
	</table>

</div>

<div id="main">