<?php
/**
 * $Id
 *
 * A class to extract useful infromation from a HTTP user agent
 */

//STATUS: wip

//TODO 1!!!!!: identify Internet Explorer, Opera!
//TODO 2: parse os & arch from HttpUserAgent into WebBrowser

require_once('core.php');

class WebBrowser
{
    var $name;      ///< string "Chrome", "Firefox", "Internet Explorer"
    var $vendor;    ///< string "Microsoft", "Google", "Mozilla"
    var $version;   ///< string "13.0.782.112", "6.0"
//    var $os;        ///< string "Linux", "Windows", "MacOS X"
//    var $arch;      ///< string "x86_64"
}

class HttpUserAgent
{
    public static function getBrowser($s)
    {
        $o = new WebBrowser();

        if (instr($s, 'Chrome'))
        {
            $o->vendor = 'Google';
            $o->name = 'Chrome';

            // XXX FIXME use a regexp
            $x = explode('Chrome/', $s, 2);
            $y = explode(' ', $x[1]);

            $o->version = $y[0];
        }

        if (instr($s, 'Firefox'))
        {
            $o->vendor = 'Mozilla';
            $o->name = 'Firefox';

            // XXX FIXME use a regexp
            $x = explode('Firefox/', $s, 2);
            $y = explode(' ', $x[1]);

            $o->version = $y[0];
        }

        return $o;
    }

}

?>
