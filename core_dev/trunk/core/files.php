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
 * @param $filename string with input filename
 * @return replaces $filename suffix with $suffix
 */
function file_set_suffix($filename, $suffix) //XXX rename function to not indicate that the input file is changed ("set")
{
    $len = strlen(file_suffix($filename));
    return substr($filename, 0, -$len).$suffix;
}

/**
 * Returns a mimetype based on the file extension
 *
 * @param $name a filename or full URL
 */
function file_get_mime_by_suffix($name)
{
    if (!$name) return;

    $ext = file_suffix($name);
    switch ($ext)
    {
    case '.jpg': return 'image/jpeg';
    case '.png': return 'image/png';
    case '.gif': return 'image/gif';

    case '.mov': return 'video/quicktime';
    default:
        dp('file_get_mime_by_suffix unhandled ext: '.$ext);
        return 'application/octet-stream'; //unknown type
    }
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

/**
 * @return array with recursive directory tree
 */
function dir_get_tree($outerDir)
{
    $dirs = array_diff( scandir($outerDir), array('.', '..') );
    $res = array();

    foreach ($dirs as $d)
    {
        if (is_dir($outerDir.'/'.$d) )
            $res[$d] = dir_get_tree( $outerDir.'/'.$d );
        else
            $res[] = $d;
    }

    return $res;
}

/**
 * Returns array with files filtered on extension
 *
 * @param $path path to directory to look in
 * @param $filter_ext array of extensions including dot, example: array(".avi", ".mkv")
 * @param $prefix require prefix in filenames
 * @param $full_path true to return full paths in matches
 */
function dir_get_by_extension($path, $filter_ext = array(), $prefix = '', $full_path = false)
{
    $e = scandir($path);

    $out = array();
    foreach ($e as $name)
    {
        if ($name == '.' || $name == '..')
            continue;

        if ($prefix && strpos($name, $prefix) !== 0)
            continue;

        $suffix = file_suffix($name);

        if (!$filter_ext || in_array($suffix, $filter_ext))
            $out[] = ($full_path ? $path.$name : $name);
    }

    return $out;
}

?>
