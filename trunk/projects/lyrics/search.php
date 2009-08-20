<?php

require_once('config.php');

$q = '';
if (!empty($_GET['s'])) $q = $_GET['s'];
if (!empty($_POST['s'])) $q = $_POST['s'];

require('design_head.php');

echo '<h1>Search results</h1>';

$lyrics = searchLyrics($q);
if (strlen($q) > 3 && count($lyrics) == 1) {
	goLoc('show_lyric.php?id='.$lyrics[0]['lyricId'].'&highlight='.urlencode($q));
	die;
}

if ($lyrics) {
	echo '<b>'.count($lyrics).'</b> lyrics found:<br/>';
	foreach ($lyrics as $row) {
		echo '<a href="show_band.php?id='.$row['bandId'].'">'.htmlspecialchars($row['bandName']).'</a> - ';
		echo '<a href="show_lyric.php?id='.$row['lyricId'].'&amp;highlight='.urlencode($q).'">'.htmlspecialchars(stripslashes($row['lyricName'])).'</a><br/>';
	}
	unset($lyrics);
} else {
	echo 'No matching lyrics found<br/>';
}
echo '<br/>';

$bands = searchBands($q);
if (strlen($q) > 3 && count($bands) == 1) {
	goLoc('show_band.php?id='.$bands[0]['bandId']);
	die;
}

if ($bands) {
	echo '<b>'.count($bands).'</b> bands found:<br/>';
	foreach ($bands as $row) {
		echo '<a href="show_band.php?id='.$row['bandId'].'">'.htmlspecialchars($row['bandName']).'</a><br/>';
	}
	unset($bands);
} else {
	echo 'No matching bands found<br/>';
}
echo '<br/>';

$records = searchRecords($q);
if (strlen($q) > 3 && count($records) == 1) {
	goLoc('show_record.php?id='.$records[0]['recordId']);
	die;
}
if ($records) {
	echo '<b>'.count($records).'</b> records found:<br/>';
	foreach ($records as $row) {
		echo '<a href="show_record.php?id='.$row['recordId'].'">'.htmlspecialchars($row['recordName']).'</a><br/>';
	}
	unset($records);
} else {
	echo 'No matching records found<br/>';
}
echo '<br/>';


require('design_foot.php');
?>
