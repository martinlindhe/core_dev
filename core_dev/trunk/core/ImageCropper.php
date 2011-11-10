<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */
 
//STATUS: rewrite this to a class extending from Image, like ImageResizer works

die('XXX NEED REWRITE');

/**
 * Crops selected image to the requested dimensions
 *
 * @param $in_file filename of input image
 * @param $in_file filename of output image
 * @param $x1 coordinate x1
 * @param $y1 coordinate y1
 * @param $x2 coordinate x2
 * @param $y2 coordinate y2
 * @return true on success
 */
function cropImage($in_file, $out_file, $x1, $y1, $x2, $y2)
{
    global $h, $config;
    if (!is_numeric($x1) || !is_numeric($y1) || !is_numeric($x2) || !is_numeric($y2)) return false;

    $mime = $h->files->lookupMimeType($in_file);

    if (!$h->files->image_convert) return false;

    $crop = ($x2-$x1).'x'.($y2-$y1).'+'.$x1.'+'.$y1;

    //Crop with imagemagick
    switch ($mime) {
    case 'image/jpeg':
        $c = 'convert -crop '.$crop. ' -quality '.$config['image']['jpeg_quality'].' '.escapeshellarg($in_file).' JPG:'.escapeshellarg($out_file);
        break;

    case 'image/png':
        $c = 'convert -crop '.$crop. ' '.escapeshellarg($in_file).' PNG:'.escapeshellarg($out_file);
        break;

    case 'image/gif':
        $c = 'convert -crop '.$crop. ' '.escapeshellarg($in_file).' GIF:'.escapeshellarg($out_file);
        break;

    default:
        throw new Exception ('unhandled mimetype "'.$mime.'"');
    }
    //echo 'Executing: '.$c.'<br/>';
    exec($c);
    if (!file_exists($out_file)) return false;
    return true;
}

?>