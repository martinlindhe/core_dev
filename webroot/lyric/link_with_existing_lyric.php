<?
	include('include_all.php');
	
	if (!empty($_GET['record']) && !empty($_GET['track']) && is_numeric($_GET['record']) && is_numeric($_GET['track']))
	{
		$record_id = $_GET['record'];
		$track = $_GET['track'];
		
		if (isset($_GET['band']) && $_GET['band']) {
			$band_id = $_GET['band'];
		} else {
			$band_id = getBandIdFromRecordId($db, $record_id);
		}
		
		if (isset($_POST['lyricid']) && $_POST['lyricid'])
		{
			$lyric_id = $_POST['lyricid'];
			
			if (!linkLyric($db, $record_id, $track, $lyric_id, $band_id))
			{
				echo 'Failed to add lyric link';
			}
			else
			{
				if ($_SESSION['userMode'] == 0) {
					/* Add to pending changes queue */
					addPendingChange($db, MODERATIONCHANGE_LYRICLINK, $record_id, $track);
				}
				
				header('Location: show_record.php?id='.$record_id);
				die;
			}
		}
		
	}
	else
	{
		echo 'Bad id';
		die;
	}

	include('body_header.php');

	if ($band_id == 0) {
		/* Skivan vi ska länka en text på är en split/compilation */
		
		echo 'Since this is a comp/split you first need to select a band.<br>';
		echo 'Then you\'ll be able to select a lyric from that band to link to this track.<br>';
		
		echo '<form>';
		echo '<select name="url" pnChange="location.href=form.url.options[form.url.selectedIndex].value">';
		echo '<option>--- Select band ---';
		$list = getBands($db);
		for ($i=0; $i<count($list); $i++)
		{
			echo '<option value="'.$_SERVER['PHP_SELF'].'?record='.$record_id.'&track='.$track.'&band='.$list[$i]['bandId'].'">'.$list[$i]['bandName'];
		}
		echo '</select><br>';
		echo '</form>';

	} else {

		echo 'Here is existing lyrics in database with the band <b>'.getBandName($db, $band_id).'</b>,<br>';
		echo 'select one to associate it with track <b>'.$track.'</b> on <b>'.getRecordName($db, $record_id).'</b>.<br>';

		$list = getBandLyrics($db, $band_id);
		echo '<form name="linklyric" method="post" action="'.$_SERVER['PHP_SELF'].'?record='.$record_id.'&track='.$track.'&band='.$band_id.'">';
		echo '<select name="lyricid">';
		for ($i=0; $i<count($list); $i++)
		{
			echo '<option value="'.$list[$i]['lyricId'].'">'.$list[$i]['lyricName'];
		}
		echo '</select><br>';
		echo '<input type="submit" value="Save link" class="buttonstyle">';
		echo '</form>';
?>
<script type="text/javascript">
document.linklyric.lyricid.focus();
</script>
<?
	}
	
	include('body_footer.php');
?>