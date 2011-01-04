<?php
/**
 * $Id$
 *
 * Misc file-related functions
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

/**
 * @return file extension for given filename, example ".jpg"
 */
function file_suffix($filename)
{
    $pos = strrpos($filename, '.');
    if ($pos === false) return '';

    return substr($filename, $pos);
}

/**
 * @return filename without file extension for given filename
 */
function no_suffix($filename)
{
    $pos = strrpos($filename, '.');
    if ($pos === false) return $filename;

    return substr($filename, 0, $pos);
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
    case '.txt': return 'text/plain';

    case '.mov': return 'video/quicktime';
    default:
        echo 'WARNING: file_get_mime_by_suffix unhandled ext: '.$name."\n";
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
            foreach (dir_get_tree( $outerDir.'/'.$d ) as $r)
                $res[] = $r;
        else
            $res[] = $outerDir.'/'.$d;
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
 * @param $include_dirs should we return directories?
 */
function dir_get_by_extension($path, $filter_ext = array(), $prefix = '', $full_path = false, $include_dirs = true)
{
    $e = scandir($path);

    $out = array();
    foreach ($e as $name)
    {
        if ($name == '.' || $name == '..')
            continue;

        if (!$include_dirs && is_dir($path.'/'.$name))
            continue;

        if ($prefix && strpos($name, $prefix) !== 0)
            continue;

        $suffix = file_suffix($name);

        if (!$filter_ext || in_array($suffix, $filter_ext))
            $out[] = ($full_path ? $path.'/'.$name : $name);
    }

    return $out;
}

/**
 * Expands a input argument to a file of lists matching certain extensions
 *
 * @param $in input argument
 * @param $filter_ext array of extensions including dot, example: array(".avi", ".mkv")
 * @return array with filenames
 */
function expand_arg_files($in, $filter_ext = array() )
{
    if (is_array($in)) {
        $res = array();
        foreach ($in as $f)
            if (in_array( file_suffix($f), $filter_ext))
                $res[] = $f;

        return $res;
    }

    if (is_file($in))
        if (in_array(file_suffix($in), $filter_ext))
            return array($in);        //XXX: expand to full path

    if (is_dir($in))
        return dir_get_by_extension($in, $filter_ext, '', true, false);

    if (strpos($in, "\n") !== false) {
        if ($filter_ext)
            throw new Exception ('XXX respect $filter_ext');

        return explode("\n", trim($in)); // newline-separated list of filenames with full path
    }

    throw new Exception ('Unknown input');
}

?>
