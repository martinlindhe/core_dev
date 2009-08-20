<?php

$meta_search[] = array('url' => $config['app']['full_url'].'opensearch.php', 'name' => 'lyric search');
$meta_css[] = $config['app']['full_url'].'css/site.css';
createXHTMLHeader();
?>
<div id="left_nav">

<?php

$menu = array(
	'index.php' => 'Start page',
	'list_bands.php' => 'List bands',
	'missing_lyrics.php' => 'Missing lyrics',
	'incomplete_lyrics.php' => 'Incomplete lyrics');
createMenu($menu);

if ($session->id) {
	$menu = array(
		'add_band.php' => 'Add band',
		'add_record.php' => 'Add normal record',
		'add_record_comp.php' => 'Add comp. / split',
		'index.php?logout' => 'Log out');
	createMenu($menu);
}

echo xhtmlForm('search.php', 'post', 'search');
echo xhtmlInput('s', '', 16).'<br/>';
echo xhtmlSubmit('Search');
echo xhtmlFormClose();
?>

</div>

<div id="main">
