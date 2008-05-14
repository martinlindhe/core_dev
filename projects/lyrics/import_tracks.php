<?php

require_once('config.php');

$session->requireLoggedIn();

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
if (!empty($_POST['tracks'])) {
	$tracks_text = trim(strip_tags($_POST['tracks']));
	echo 'If you check a "NO MATCH", a new entry will be created with that title';

	echo 'Trying to figure out titles.<br/><br/>';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$record_id.'">';
	echo '<input type="hidden" name="tracks" value="'.$tracks_text.'"/>';

	$tracks = explode("\n", $tracks_text);

	for ($i=0; $i<count($tracks); $i++) {
		/* Remove some common parts in song titles to make matching easier */
		$tracks[$i] = str_ireplace('[demo version]', '', $tracks[$i]);
		$tracks[$i] = str_ireplace('[alternative version]', '', $tracks[$i]);
		$tracks[$i] = str_ireplace('[interlude]', '', $tracks[$i]);
		$tracks[$i] = str_ireplace('[live]', '', $tracks[$i]);
		$tracks[$i] = str_ireplace('(live)', '', $tracks[$i]);
		$tracks[$i] = trim($tracks[$i]);

		$temp = explode('.', $tracks[$i]);

		$index = trim(array_shift($temp));	//The first number

		if (isset($temp[0]) && is_numeric($index)) {
			/* Format: 12.Titel - Used at gracenote.com */
			$songname = trim(implode('.', $temp));
		} else {
			/* Format: Titel */
			$songname = $tracks[$i];
		}

		if (!is_numeric($index)) $index = $i+1;

		if ($index != ($i+1)) continue;

		$q = 'SELECT lyricId,lyricName FROM tblLyrics WHERE SOUNDEX(lyricName)=SOUNDEX("'.$db->escape($songname).'") AND bandId='.$band_id;
		$row = $db->getOneRow($q);

		if (isset($_POST['ck'.$i])) {
			if ($row) {
				/* Match was accepted, let's add it */
				echo 'Linking track '.$index.' to existing lyric "'.$row['lyricName'].'"<br/>';
				linkLyric($record_id, $index, $_POST['ck'.$i], $band_id);
				$linked = true;
			} else {
				/* Add new entry using found title */
				echo 'Creating new lyric entry for song titled '.ucfirst($songname).' and linking to track '.$index.'<br/>';
				$new_id = addLyric($band_id, $record_id, $index, ucfirst($songname));
				linkLyric($record_id, $index, $new_id, $band_id);
			}

		} else {
			//TODO: visa inga checkboxar för lyrics som redan är länkad
			echo '<input type="checkbox" name="ck'.$i.'" id="ck'.$i.'" value="'.$row['lyricId'].'"'.($row?' checked="checked"':'').'/>';

			echo '<label for="ck'.$i.'">';
			if ($row) {
				echo ($index).': <b>'.$songname.'</b>';

				echo ' => <a href="show_lyric.php?id='.$row['lyricId'].'">'.$row['lyricName'].'</a> ';

				$match = similar_text(strtolower($songname), strtolower($row['lyricName']), $p);
				echo '('.round($p,2).'%)';

				/* We should be pretty picky about guessing */
				if ($p < 95.0) {
					echo ' <b><font color="red">SUSPECTED MISMATCH</font></b>';
				}
			} else {
				echo ($index).'. <b>'.$songname.'</b> => <b><font color="red">NO MATCH</font></b>';
			}
			echo '</label><br/>';
		}
	}

	echo '<input type="submit" class="button" value="Link matches"/>';
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
