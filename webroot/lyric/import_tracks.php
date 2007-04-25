<?
	require_once('config.php');

	$linked = false;

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	$record_id = $_GET['id'];

	$record_name = getRecordName($record_id);

	$band_id = getBandIdFromRecordId($record_id);
	if (!$band_id) die('Only works for single artist.');

	$band_name = getBandName($band_id);

	if (!empty($_POST['tracks']))

	$title = 'inthc.net: "'.$band_name.' - '.$record_name.'" album, import track list';
	require('design_head.php');

	$tracks_text = '';	
	if (!empty($_POST['tracks']))
	{
		$tracks_text = trim(strip_tags($_POST['tracks']));
		echo 'If you check a "NO MATCH", a new entry will be created with that title';

		echo 'Trying to figure out titles.<br/><br/>';
		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$record_id.'">';
		echo '<input type="hidden" name="tracks" value="'.$tracks_text.'"/>';
			
		$tracks = explode("\n", $tracks_text);

		for ($i=0; $i<count($tracks); $i++) {	//fixme: foreach

			/* Remove some common parts in song titles to make matching easier */
			$tracks[$i] = trim(strtolower($tracks[$i]));
			$tracks[$i] = str_replace('[demo version]', '', $tracks[$i]);
			$tracks[$i] = str_replace('[alternative version]', '', $tracks[$i]);
			$tracks[$i] = str_replace('[interlude]', '', $tracks[$i]);
			$tracks[$i] = str_replace('[live]', '', $tracks[$i]);
			$tracks[$i] = str_replace('(live)', '', $tracks[$i]);
			$tracks[$i] = trim($tracks[$i]);

			$songname = '';
			$temp = explode('.', $tracks[$i]);  /* Format: 12.Titel */
			$temp[0] = trim($temp[0]);
			if (isset($temp[1])) $songname = trim($temp[1]);

			if ($temp[0] != ($i+1)) {

				$temp = explode(')', $tracks[$i]);  /* Format: 12)Titel */
				$temp[0] = trim($temp[0]);
				if (isset($temp[1])) $songname = trim($temp[1]);

				if ($temp[0] != ($i+1)) {
					$temp = explode(' ', $tracks[$i]);  /* Format: 12 Titel */
					$temp[0] = trim($temp[0]);
					if (isset($temp[1])) {
						$songname = '';
						for ($j=1; $j<count($temp); $j++) {
							$songname .= ' '.$temp[$j];
						}
					}
				}
			}

			if ($songname == '') {
				/* Format: Titel */
				$songname = $tracks[$i];
			}

			if ($temp[0] == ($i+1))
			{

				$q = 'SELECT lyricId,lyricName FROM tblLyrics WHERE SOUNDEX(lyricName)=SOUNDEX("'.$db->escape($songname).'") AND bandId='.$band_id;
				$row = $db->getOneRow($q);

				if (isset($_POST['ck'.$i]))
				{
					if ($row) {
						/* Match was accepted, let's add it */
						echo 'Linking track '.($i+1).' to existing lyric "'.$row['lyricName'].'"<br/>';
						linkLyric($record_id, ($i+1), $_POST['ck'.$i], $band_id);
						$linked = true;
					} else {
						/* Add new entry using found title */
						echo 'Creating new lyric entry for song titled '.ucfirst($songname).' and linking to track '.($i+1).'<br/>';
						$new_id = addLyric($band_id, $record_id, ($i+1), ucfirst($songname));
						linkLyric($record_id, ($i+1), $new_id, $band_id);
					}

				} else {
					//todo: visa inga checkboxar för lyrics som redan är länkad
					
					echo '<input type="checkbox" name="ck'.$i.'" id="ck'.$i.'" value="'.$row['lyricId'].'"'.($row?' checked="checked"':'').'/>';

					echo '<label for="ck'.$i.'">';
					if ($row) {
						echo ($i+1).': <b>'.$songname.'</b>';

						echo ' => <a href="show_lyric.php?id='.$row['lyricId'].'">'.$row['lyricName'].'</a> ';

						$match = similar_text(strtolower($songname), strtolower($row['lyricName']), $p);
						echo '('.round($p,2).'%)';

						/* We should be pretty picky about guessing */
						if ($p < 95.0) {
							echo ' <b><font color="red">SUSPECTED MISMATCH</font></b>';
						}
					} else {
						echo ($i+1).'. <b>'.$songname.'</b> => <b><font color="red">NO MATCH</font></b>';
					}
					echo '</label><br/>';
				}
			}
		}

		echo '<input type="submit" value="Link matches"/>';
		echo '</form>';
	}

	echo 'Just paste a title list and hit Import.<br/><br/>';
	echo 'We can currently handle the following formats:<br/><br/>';
	echo 'Track.Title<br/><br/>';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$record_id.'">';
	echo '<textarea name="tracks" rows="16" cols="60">'.$tracks_text.'</textarea><br/>';
	echo '<input type="submit" value="Import"/><br/>';
	echo '</form>';
	
	echo '<a href="show_record.php?id='.$record_id.'">Back to record overview</a>';
	
	require('design_foot.php');
?>