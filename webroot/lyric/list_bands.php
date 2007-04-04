<?
	require_once('config.php');
	require('design_head.php');	

	$list = getBands($db);

	$mod = 0;
	echo '<table width="400" cellpadding="3" cellspacing="0" border="1">';
	for ($i=0; $i<count($list); $i++)
	{
		$band_id = $list[$i]['bandId'];
		if (isModerated($band_id, MODERATION_BAND)) {
			echo '<tr><td class="titlemod">';
			$mod++;
		} else {
			echo '<tr><td class="title">';
		}

		echo '<a href="show_band.php?id='.$band_id.'">'.$list[$i]['bandName'].' ('.getBandRecordCount($band_id).' records)</a>';
		echo '</td></tr>';
	}
	echo '</table>';
	echo count($list).' bands displayed.<br/>';
	echo $mod.' of them are pending for approval.<br/>';

	echo '<br/>';
	echo '<a href="index.php">Back to main page.</a><br/>';

	require('design_foot.php');
?>