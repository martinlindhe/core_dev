<?
	require_once('config.php');
	require('design_head.php');

	echo 'Quickjump: ';
	for ($i = 'a'; $i < 'z'; $i++) {		// fixme: $i <= 'z' expose some shitty php bug (php 5.2.3)
		echo '<a href="#n_'.$i.'">'.$i.'</a> ';
	}


	$list = getBandsWithRecordCount();
	echo '<table width="400" cellpadding="3" cellspacing="0" border="1">';
	foreach ($list as $band)
	{
		echo '<tr><td class="title">';
		$letter = strtolower(substr($band['bandName'], 0, 1));
		if (empty($shown[$letter])) {
			echo '<a name="n_'.$letter.'"></a>';
			$shown[$letter] = true;
		}
		echo '<a href="show_band.php?id='.$band['bandId'].'">'.htmlspecialchars($band['bandName']).' ('.$band['cnt'].' records)</a>';

		echo '</td></tr>';
	}
	echo '</table>';
	echo count($list).' bands displayed.<br/>';

	require('design_foot.php');
?>