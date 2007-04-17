<?
	require_once('config.php');

	$linked = false;

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	$record_id = $_GET['id'];

	$record_name = getRecordName($record_id);

	$band_id = getBandIdFromRecordId($record_id);
	if ($band_id) {
		$band_name = getBandName($band_id);
	} else {
		echo 'Only work for single artist.<br/>';
		die;
	}

	$title = 'inthc.net: "'.$band_name.' - '.$record_name.'" album, import track list';
	require('design_head.php');

	if (isset($_POST['tracks']) && $_POST['tracks']) {
		echo 'Trying to figure out titles.<br/><br/>';
		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$record_id.'">';
		echo '<input type="hidden" name="tracks" value="'.$_POST['tracks'].'"/>';
			
		$tracks = explode("\n", cleanupText(stripslashes($_POST['tracks'])) );

		for ($i=0; $i<count($tracks); $i++) {

			/* Remove some common parts in song titles to make matching easier */
			$tracks[$i] = strtolower($tracks[$i]).' ';
			$tracks[$i] = str_replace(' [demo version] ', '', $tracks[$i]);
			$tracks[$i] = str_replace(' [alternative version] ', '', $tracks[$i]);
			$tracks[$i] = str_replace(' [live] ', '', $tracks[$i]);
			$tracks[$i] = str_replace(' (live) ', '', $tracks[$i]);
			$tracks[$i] = trim($tracks[$i]);

			$songname = '';
			$temp = explode('.', $tracks[$i]);  /* Format: 12.Titel */
			$temp[0] = trim($temp[0]);
			if (isset($temp[1])) $songname = trim($temp[1]);

			if ($temp[0] != (string)($i+1)) {

				$temp = explode(')', $tracks[$i]);  /* Format: 12)Titel */
				$temp[0] = trim($temp[0]);
				if (isset($temp[1])) $songname = trim($temp[1]);

				if ($temp[0] != (string)($i+1)) {
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

			if ($temp[0] == (string)($i+1)) {
				$sql = "SELECT lyricId,lyricName FROM tblLyrics WHERE SOUNDEX(lyricName)=SOUNDEX('".$songname."') AND bandId=".$band_id;
				$check = $db->query($sql);
				if (dbNumRows($check)) {
					$row = dbFetchArray($check);
						
					if (isset($_POST['ck'.$i]) && $_POST['ck'.$i]) {
						/* Match was accepted, let's add it */
						echo 'Adding id '.$_POST['ck'.$i].' to track '.($i+1).'<br/>';
						linkLyric($record_id, ($i+1), $_POST['ck'.$i], $band_id);
						$linked = true;

					} else {

						echo '<input type="checkbox" name="ck'.$i.'" value="'.$row['lyricId'].'" checked="checked"/>';
						echo ($i+1).': <b>'.$songname.'</b> => ';
						echo '<a href="show_lyric.php?id='.$row['lyricId'].'">'.$row['lyricName'].'</a> ';

						$match = similar_text(strtolower($songname), strtolower($row['lyricName']), $p);
						echo '('.round($p,2).'%)';

						/* We should be pretty picky about guessing */
						if ($p < 95.0) {
							echo ' <b><font color="red">SUSPECTED MISMATCH</font></b>';
						}
						echo '<br/>';
					}

				} else {
					if (!isset($_POST['ck'.$i])) {
						echo '<input type="checkbox" name="ck'.$i.'" value="0"/>';
						echo ($i+1).'. <b>'.$songname.'</b> => <b><font color="red">NO MATCH</font></b><br/>';
					}
				}

			} else {
				echo 'Unknown format: '.$tracks[$i].'<br/>';
			}
		}

		if ($linked == true) {
			echo 'Record has been linked.<br/><br/>';
			echo '<a href="show_record.php?id='.$record_id.'">Go back to record overview.</a><br/>';
		} else {
			echo '<input type="submit" value="Link matches"></form>';
		}
		die;
	}

	echo 'Just paste a title list and hit Import.<br/><br/>';
	echo 'We can currently handle the following formats:<br/><br/>';
	echo 'Track.Title<br/><br/>';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$record_id.'">';
	echo '<textarea name="tracks" rows="16" cols="60"></textarea><br/>';
	echo '<input type="submit" value="Import"/><br/>';
	echo '</form>';
	
	require('design_foot.php');
?>