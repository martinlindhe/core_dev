<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//XXX gzdecode() dont exist in php 5.3, where is it?

/**
 * Decompresses gzip compressed data
 */
function gzdecode($data)   //XXXX why is this needed??? why wont gzuncompress() or gzinflate() work?
{
    //check for gzip header
    if (strlen($data) < 18 || strcmp(substr($data, 0, 2),"\x1f\x8b"))
        return false;

    $tmp_file = tempnam('', 'gzdec-');

    $fd = fopen($tmp_file, 'w');
    fwrite($fd, $data);
    fclose($fd);

    $fd = gzopen($tmp_file, 'r');

    $res = '';
    while (!feof($fd))
        $res .= gzread($fd, 65536);

    unlink($tmp_file);

    return $res;
}

?>
