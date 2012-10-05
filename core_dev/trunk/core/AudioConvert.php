<?php
/**
 * $Id$
 *
 * Utility class wrapped around SoX to convert audio files between different formats.
 * Conversions is forked so the calls are non-blocking
 *
 * WARNING: sox on Ubuntu requires libsox-fmt-mp3
 */

//STATUS: wip

namespace cd;

class AudioConvert
{
    public static function toMp3($in_file, $out_file)
    {
        if (!file_exists($in_file)) {
            dp('FAIL toMp3: input file not found: '.$in_file);
            return;
        }

        // the "&" spawns this command in a new process
        $c = 'sox "'.$in_file.'" --type mp3 --comment "" "'.$out_file.'" >/dev/null 2>/dev/null &';
        shell_exec($c);
    }

    public static function toOgg($in_file, $out_file)
    {
        if (!file_exists($in_file)) {
            dp('FAIL toOgg: input file not found: '.$in_file);
            return;
        }

        // the "&" spawns this command in a new process
        $c = 'sox "'.$in_file.'" --type ogg --comment "" "'.$out_file.'" >/dev/null 2>/dev/null &';
        shell_exec($c);
    }

}

?>
