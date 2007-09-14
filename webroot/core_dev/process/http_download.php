<?
/*
	here you ask the server to fetch a remote media for later processing
*/

	require_once('config.php');

	require('design_head.php');

	if (!empty($_POST['url'])) {

		//fixme: en isURL() funktion som kollar om strängen är en valid url
		$fileId = processEvent(PROCESSFETCH_FORM, $_POST['url']);

		echo 'URL to process has been enqueued.';
		require('design_foot.php');
		die;
	}


	wiki('ProcessQueueDownload');

	echo 'Enter resource URL:<br/>';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo '<input type="text" name="url" size="60" id="url"/><img src="'.$config['core_web_root'].'gfx/arrow_next.png" align="absmiddle" onclick="expand_input(\'url\')"/><br/>';
	echo '<input type="submit" class="button" value="Add"/>';
	echo '</form>';

	require('design_foot.php');
?>