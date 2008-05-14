<?php

require_once('config.php');
require('design_head.php');

if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

$record_id = $_GET['id'];

$band_id = getBandIdFromRecordId($record_id);

if ($band_id == 0) {
	echo '<b>V/A - '.getRecordName($record_id).'</b>';
} else {
	echo '<b><a href="show_band.php?id='.$band_id.'">'.htmlspecialchars(getBandName($band_id)).'</a>';
	echo ' - '.getRecordName($record_id).'</b>';
}
echo '<br/><br/>';

$list = getRecordTracks($record_id);

/* Then list the lyrics */
$active = 1;
for ($i=0; $i<count($list); $i++) {
	echo '<div class="faq_holder">';
	echo '<div class="faq_q" onclick="faq_focus('.$i.')">';
	echo '<b>'.$list[$i]['trackNumber'].'. ';

	if ($band_id == 0) {
		echo $list[$i]['bandName'] .' - '.stripslashes($list[$i]['lyricName']).'</b>';
	} else {
		echo stripslashes($list[$i]['lyricName']).'</b>';
	}

	if ($list[$i]['authorId'] != $list[$i]['bandId']) {
		echo ' (Cover by <a href="show_band.php?id='.$list[$i]['authorId'].'">'.getBandName($list[$i]['authorId']).'</a>)';
	}
	if ($session->id) echo ' <a href="edit_lyric.php?id='.$list[$i]['lyricId'].'">Edit</a>';
	echo '</div>';

	echo '<div class="faq_a" id="faq_'.$i.'" style="'.($list[$i]['trackNumber']!=$active?'display:none':'').'">';
	$lyric = stripslashes($list[$i]['lyricText']);
	if ($lyric) {
		$lyric = str_replace('&amp;', '&', $lyric);
		$lyric = str_replace('&', '&amp;', $lyric);
		echo nl2br($lyric);
	} else {
		echo 'Lyric missing.';
	}
	echo '</div>';
	echo '</div>';	//class="faq_holder"
}

require('design_foot.php');
?>
