<?
	require_once('config.php');
	require('design_head.php');

	$list = getBandsWithRecordCount();

	echo '<table width="400" cellpadding="3" cellspacing="0" border="1">';
	foreach ($list as $band)
	{
		echo '<tr><td class="title">';
		echo '<a href="show_band.php?id='.$band['bandId'].'">'.htmlspecialchars($band['bandName']).' ('.$band['cnt'].' records)</a>';
		echo '</td></tr>';
	}
	echo '</table>';
	echo count($list).' bands displayed.<br/>';

	require('design_foot.php');
?>