<?
	require_once('config.php');
	
	$band_id = 0;
	if (!empty($_GET['band']) && is_numeric($_GET['band'])) $band_id = $_GET['band'];


	if (!empty($_GET['record']) && !empty($_GET['track']) && is_numeric($_GET['record']) && is_numeric($_GET['track']))
	{
		$record_id = $_GET['record'];
		$track = $_GET['track'];
		
		/* Will be 0 if this is a comp/split */
		$coverband_id = getBandIdFromRecordId($db, $record_id);
		if (!empty($_GET['coverband']) && is_numeric($_GET['coverband'])) $coverband_id = $_GET['coverband'];

		if (!empty($_POST['lyricid']) && is_numeric($_POST['lyricid']))
		{
			$lyric_id = $_POST['lyricid'];

			if (!linkLyric($db, $record_id, $track, $lyric_id, $coverband_id))
			{
				echo 'Failed to add lyric link';
			}
			else
			{
				//if ($_SESSION['userMode'] == 0) {
					/* Add to pending changes queue */
					addPendingChange($db, MODERATIONCHANGE_LYRICLINK, $record_id, $track);
				//}

				header('Location: show_record.php?id='.$record_id);
				die;
			}
		}
		
	}
	else
	{
		die('Bad id');
	}
	
	require('design_head.php');	
	
	if ($coverband_id == 0) {
		/* Bandet som gör covern */
		
		echo '1. Select the band who is doing the cover in the dropdown below.<br/>';
		echo '(Adding cover to track <b>'.$track.'</b> on <b>'.getRecordName($db, $record_id).'</b>).<br/><br/>';
		
		echo '<form action="">';
		echo '<select name="url" onchange="location.href=form.url.options[form.url.selectedIndex].value">';
		echo '<option>--- Select band ---</option>';
		$list = getBands($db);
		for ($i=0; $i<count($list); $i++)
		{
			echo '<option value="'.$_SERVER['PHP_SELF'].'?record='.$record_id.'&amp;track='.$track.'&amp;coverband='.$list[$i]['bandId'].'">'.$list[$i]['bandName'].'</option>';
		}
		echo '</select><br/>';
		echo '</form>';

	} else if ($band_id == 0) {
		/* Bandet som gjort covern från början */

		echo '2. Select the band who did the orginal song that <b>'.getBandName($db, $coverband_id).'</b> covers.<br/>';
		echo '(Adding cover to track <b>'.$track.'</b> on <b>'.getRecordName($db, $record_id).'</b>).<br/><br/>';

		echo '<form action="">';
		echo '<select name="url" onchange="location.href=form.url.options[form.url.selectedIndex].value">';
		echo '<option>--- Select band ---</option>';
		$list = getBands($db);
		for ($i=0; $i<count($list); $i++)
		{
			echo '<option value="'.$_SERVER['PHP_SELF'].'?record='.$record_id.'&amp;track='.$track.'&amp;coverband='.$coverband_id.'&amp;band='.$list[$i]['bandId'].'">'.$list[$i]['bandName'].'</option>';
		}
		echo '</select><br/>';
		echo '</form>';
		
		echo '<a href="'.$_SERVER['PHP_SELF'].'?record='.$record_id.'&amp;track='.$track.'">Back to step 1</a><br/>';

	} else {

		echo '3. Select the original song by <b>'.getBandName($db, $band_id).'</b> that <b>'.getBandName($db, $coverband_id).'</b> covers from the dropdown below.<br/>';
		echo '(Adding cover to track <b>'.$track.'</b> on <b>'.getRecordName($db, $record_id).'</b>).<br/><br/>';

		$list = getBandLyrics($db, $band_id);
		echo '<form name="linklyric" method="post" action="'.$_SERVER['PHP_SELF'].'?record='.$record_id.'&amp;track='.$track.'&amp;coverband='.$coverband_id.'">';
		echo '<select name="lyricid">';
		for ($i=0; $i<count($list); $i++)
		{
			echo '<option value="'.$list[$i]['lyricId'].'">'.$list[$i]['lyricName'].'</option>';
		}
		echo '</select><br/>';
		echo '<input type="submit" value="Save link" class="buttonstyle"/>';
		echo '</form>';

		echo '<a href="'.$_SERVER['PHP_SELF'].'?record='.$record_id.'&amp;track='.$track.'&amp;coverband='.$coverband_id.'">Back to step 2</a><br/>';
	}

	echo '<a href="show_record.php?id='.$record_id.'">Back to record overview</a>';

	require('design_foot.php');
?>