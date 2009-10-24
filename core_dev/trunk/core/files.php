<?php
/**
 * $Id$
 *
 * Misc file-related functions
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

/**
 * Returns the file extension for given filename
 *
 * @return file extension, example ".jpg"
 */
function file_suffix($filename)
{
	$pos = strrpos($filename, '.');
	if ($pos === false) return '';

	return substr($filename, $pos);
}

/**
 * Calculates estimated download times for common internet connection speeds
 *
 * @param $size file size in bytes
 * @return array of estimated download times
 */
function estimateDownloadTime($size)
{
	if (!is_numeric($size)) return false;

	$arr = array();
	$arr[56]   = ceil($size / ((  56*1024)/8)); //56k modem
	$arr[512]  = ceil($size / (( 512*1024)/8)); //0.5mbit
	$arr[1024] = ceil($size / ((1024*1024)/8)); //1mbit
	$arr[8196] = ceil($size / ((8196*1024)/8)); //8mbit

	return $arr;
}

?>
