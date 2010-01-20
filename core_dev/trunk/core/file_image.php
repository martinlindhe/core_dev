<?php
/**
 * $Id$
 *
 * Object holding a image
 *
 */

//STATUS: draft

require_once('client_http.php');

class FileImage
{
	var $width;
	var $height;

	var $fileId;
	var $size;
	var $mime;

	/**
	 * Loads a image resource
	 *
	 * @param $uri if numeric, load fileId from database, else fetch & initialize the database
	 */
	function load($uri, $type = 0)
	{
		global $db;
		if (!is_numeric($type)) return false;

		if (is_numeric($uri))
			die('FIXME initialize object from db');

		if (!is_url($uri))
			die('FileImage->load() unhandled: '.$uri);

		if (!$type)
			$type = FILETYPE_GENERIC;

		//store file temporarely to disk
		$tmp_file = tempnam('', 'image');

		$http = new HttpClient();

		$http->getFile($url, $tmp_file);
		if ($http->getError()) {
			echo 'FileImage->load() failed '.$http->getError().' for '.$url.ln();
			return false;
		}

		//XXX use files class to create fileId entry and move file to permanent location
	}

	/**
	 * Create a rotated version of the image, as a new FileImage object
	 */
	function rotate($degrees)
	{
	}

	/**
	 * Creates a scaled version of the image, as a new FileImage object
	 */
	function scale($pct)
	{
	}

}

?>
