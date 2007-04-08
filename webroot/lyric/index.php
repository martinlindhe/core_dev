<?
	require_once('config.php');
	require('design_head.php');

	echo 'There are '.bandCount().' bands, '.recordCount().' records, '.trackCount().' tracks and '.lyricCount().' lyrics in database.<br/>';
	echo '<br/>';

	require('design_foot.php');
?>
