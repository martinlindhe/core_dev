<?php
/**
 * Functions to get metainfo about media files
 */

/**
 * @return mimetype of filename
 */
function file_get_mime($filename)
{
	if (!file_exists($filename)) return false;

	$c = 'file --brief --mime-type '.escapeshellarg($filename);
	$res = exec($c);

	//XXX: use mediaprobe to distinguish between wmv/wma files.
	//FIXME: enhance mediaprobe to handle all media detection and stop use "file"
	if ($res == 'video/x-ms-wmv') {
		$c = 'mediaprobe '.escapeshellarg($filename);
		$res = exec($c);
	}

	if (!$res) {
		echo "file_get_mime FAIL on ".$filename.ln();
	}

	return $res;
}

/**
 * Returns id3 tag data
 */
function audio_get_tags($filename)
{
	if (!file_exists($filename)) return false;

	$c = 'ffprobe -show_tags '.escapeshellarg($filename).' 2> /dev/null';
	exec($c, $res);

	$out = array();
	foreach ($res as $row)
	{
		if (strpos($row, '=') === false)
			continue;

		$ex = explode('=', $row);
		$out[ $ex[0] ] = $ex[1];
	}

	return $out;
}

?>
