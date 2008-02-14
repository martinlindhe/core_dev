<?
	/* ajax_search_cities.php - returns XHTML block for city selection */

	require_once('find_config.php');

	if ((!$session->id && !$files->anon_uploads) || empty($_GET['i']) || !is_numeric($_GET['i'])) die('bad');

	echo ZipLocation::citySelect($_GET['i']);
?>