<?php
/**
 * $Id$
 *
 * Misc file-related functions
 *
 * @author Martin Lindhe, 2009-2012 <martin@startwars.org>
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
    // images
    case '.jpg': case '.jpeg': return 'image/jpeg';
    case '.png': return 'image/png';
    case '.gif': return 'image/gif';
    case '.ico': return 'image/vnd.microsoft.icon';

    // documents
    case '.txt': return 'text/plain';

    // video
    case '.mov': return 'video/quicktime';
    case '.flv': return 'video/x-flv';

    // audio
    case '.wav': return 'audio/wav';
    case '.mp3': return 'audio/mp3';
    case '.ogg': return 'audio/ogg';
    case '.m4a': return 'audio/x-m4a';

    default:
        echo 'WARNING: file_get_mime_by_suffix unhandled ext: '.$name."\n";
        return 'application/octet-stream'; //unknown type
    }
}

/**
 * @return mimetype of filename
 */
function get_mimetype_of_data($data)
{
    $tmp_name = tempnam("/tmp", "mime-check");

    $fp = fopen($tmp_name, "w");
    fwrite($fp, $data);
    fclose($fp);

    $res = get_mimetype_of_file($tmp_name);

    unlink($tmp_name);

    return $res;
}

/**
 * @return mimetype of filename
 */
function get_mimetype_of_file($filename)
{
    if (!file_exists($filename))
        return false;

    $c = 'file --brief --mime-type '.escapeshellarg($filename);
    $res = exec($c);

    //TODO: use ffprobe to distinguish between wmv/wma files
    //  or if not works, use mediaprobe

/*
    if ($res == 'video/x-ms-wmv' || $res == 'video/x-ms-asf') {
        $c = 'mediaprobe '.escapeshellarg($filename);
        $res = exec($c);
    }
*/
    if (!$res)
        throw new \Exception ('file_get_mime FAIL on '.$filename);

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
 * @return string filename of one randomly select file, or false if no match
 */
function file_get_random($dir)
{
    $dir = realpath($dir);

    $dirs = array_diff( scandir($dir), array('.', '..') );
    $res = array();

    foreach ($dirs as $d)
    {
        $p = $dir.'/'.$d;
        if (is_dir($p) )
            continue;

        $res[] = $p;
    }

    if (!$res)
        return false;

    // get an random index:
    $rand = mt_rand(0, count($res)-1);

    return $res[$rand];
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
        throw new \Exception ('haystack must be array: '.$haystack);

    foreach ($haystack as $h)
    {
        $p1 = strpos($h, '*');

        if ($p1 === false)
            throw new \Exception ('arg_match() haystack filters require wildcards, change ".avi" TO "*.avi"');

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
        throw new \Exception ('FIXME be recursive');
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
            throw new \Exception ('XXX respect $haystack');

        return explode("\n", trim($in)); // newline-separated list of filenames with full path
    }*/

    // expand from $in = "/media/downloads/part-of-name*.avi"
    if (is_string($in) && !$haystack)
        return dir_get_matches( dirname($in), array(basename($in)) );

    throw new \Exception ('Unknown input: '.$in);
}

/**
 * Generates image thumbnails from specified video file
 */
function generate_video_thumb($fileId, $where = '10%')
{
    if (!is_numeric($fileId))
        return false;

    if (!file_exists(File::getUploadPath($fileId)))
        throw new \Exception ('file '.File::getUploadPath($fileId).' dont exist!');

    $c = 'avprobe '.File::getUploadPath($fileId).' 2>&1 | /bin/grep Duration | cut -d, -f1'; // returns: "Duration: 00:00:08.50"
    //echo "Executing: ".$c."\n";

    $x = exec($c);
    $xx = explode(': ', $x);
    $duration = in_seconds($xx[1]); // 00:00:08.50   => 8.5

    $pos_val = intval($where) / 10;

    $pos = $duration * $pos_val;

    $tmpimg = tempnam('/tmp', 'vid-thumb');

    $c = 'avconv -i '.File::getUploadPath($fileId).' -ss '.$pos.' -vframes 1 -f image2 '.$tmpimg.' 2> /dev/null';
//    echo "$ ".$c."\n";
    exec($c);

    $key = array(
        'tmp_name'=>$tmpimg,
        'name'=>'thumbnail',
        'type'=>'image/jpeg', /// XXXX: force jpeg output in above avconv command
        'size'=>filesize($tmpimg)
    );

    $thumbId = File::importImage(THUMB, $key, $fileId, true);
    return $thumbId;
}


?>
