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
    if (!$name) return false;

    $ext = file_suffix($name);
    switch ($ext)
    {
    case '.jpg': return 'image/jpeg';
    case '.png': return 'image/png';
    case '.gif': return 'image/gif';
    case '.ico': return 'image/vnd.microsoft.icon';
    case '.txt': return 'text/plain';

    case '.mov': return 'video/quicktime';
    default:
        echo 'WARNING: file_get_mime_by_suffix unhandled ext: '.$name."\n";
        return 'application/octet-stream'; //unknown type
    }
}

/**
 * @return mimetype of filename
 */
function file_get_mime_by_content($filename)
{
    if (!file_exists($filename))
        return false;

    $c = 'file --brief --mime-type '.escapeshellarg($filename);
    $res = exec($c);

    //XXX: use mediaprobe to distinguish between wmv/wma files.
    //FIXME: enhance mediaprobe to handle all media detection and stop use "file"
    if ($res == 'video/x-ms-wmv' || $res == 'video/x-ms-asf') {
        $c = 'mediaprobe '.escapeshellarg($filename);
        $res = exec($c);
    }

    if (!$res)
        throw new Exception ('file_get_mime FAIL on '.$filename);

    return $res;
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
    $arr[512]  = ceil($size / (( 512*1024)/8)); //0.5 mbit
    $arr[1024] = ceil($size / ((1024*1024)/8)); //1 mbit
    $arr[8196] = ceil($size / ((8196*1024)/8)); //8 mbit

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
 * @param $haystack array describing allowed strings, eg: test*.php, *.jpg, test*, *test
 * @param $prefix require prefix in filenames
 * @param $full_path true to return full paths in matches
 * @param $include_dirs should we return directories?
 */
function dir_get_matches($path, $haystack = array(), $prefix = '', $full_path = false, $include_dirs = true)
{
    $e = scandir($path);

    $out = array();
    foreach ($e as $name)
    {
        if ($name == '.' || $name == '..')
            continue;

        $path = realpath($path);

        if (!$include_dirs && is_dir($path.'/'.$name))
            continue;

        if ($prefix && strpos($name, $prefix) !== 0)
            continue;

        if (!$haystack || arg_match($name, $haystack))
            $out[] = ($full_path ? $path.'/'.$name : $name);
    }

    return $out;
}

/**
 * Matches input argument towards multiple allowed input string using markup tags
 *
 * @param $needle string eg. test-filename.jpg
 * @param $haystack array describing allowed strings, eg: test*.php, *.jpg, test*, *test
 * @return true if $needle is allowed according to one of the rules in $haystack
 */
function arg_match($needle, $haystack)
{
    if (!is_array($haystack))
        throw new Exception ('haystack must be array: '.$haystack);

    foreach ($haystack as $h)
    {
        $p1 = strpos($h, '*');

        if ($p1 === false)
            throw new Exception ('arg_match() haystack filters require wildcards, change ".avi" TO "*.avi"');

        $i1 = substr($h, 0, $p1);
        $i2 = substr($h, $p1 +  1);

        $o1 = substr($needle, 0, strlen($i1));
        $o2 = strlen($i2) ? substr($needle, -strlen($i2) ) : '';

//d('i1: '.$i1); d('i2: '.$i2); d('o1: '.$o1); d('o2: '.$o2);

        if ($i1 == $o1 && $i2 == $o2)
            return true;
    }

    return false;
}

/**
 * Expands a input argument to a file of lists matching certain extensions
 *
 * @param $in input argument (full path to file/directory, array with multiple entries)
 * @param $haystack array describing allowed strings, eg: test*.php, *.jpg, test*, *test
 * @return array with filenames
 */
function expand_arg_files($in, $haystack = array() )
{
    if (is_array($in)) {
        throw new Exception ('FIXME be recursive');
/*        $res = array();
        foreach ($in as $f)
            if (in_array( file_suffix($f), $haystack))
                $res[] = $f;

        return $res;*/
    }

    if (is_file($in)) {
        if (arg_match($in, $haystack))
            return array( realpath($in) );  // expands to full file path
        else
            return array();
    }

    if (is_dir($in))
        return dir_get_matches($in, $haystack, '', true, false);

    /* if (strpos($in, "\n") !== false) {
        if ($haystack)
            throw new Exception ('XXX respect $haystack');

        return explode("\n", trim($in)); // newline-separated list of filenames with full path
    }*/

    // expand from $in = "/media/downloads/part-of-name*.avi"
    if (is_string($in) && !$haystack)
        return dir_get_matches( dirname($in), array(basename($in)) );

    throw new Exception ('Unknown input: '.$in);
}

?>
