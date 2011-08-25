<?php
/**
 * $Id
 *
 * A helper class to parse useful infromation from a HTTP user agent string
 *
 * See http://www.useragentstring.com/
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: parse os & arch from HttpUserAgent into WebBrowser

require_once('core.php');

class WebBrowser
{
    var $vendor;    ///< string "Microsoft", "Google", "Mozilla"
    var $name;      ///< string "Chrome", "Firefox", "Internet Explorer"
    var $version;   ///< string "13.0.782.112", "6.0"
//    var $os;        ///< string "Linux", "Windows", "MacOS X"
//    var $arch;      ///< string "x86_64"
}

class HttpUserAgent
{
    public static function getBrowser($s)
    {
        $o = new WebBrowser();

        $o->vendor  = 'Unknown vendor';
        $o->name    = 'Unknown browser';
        $o->version = 'Unknown version';

        if (instr($s, 'Firefox'))
        {
            $o->vendor = 'Mozilla';
            $o->name   = 'Firefox';

            // XXX FIXME use a regexp
            $x = explode('Firefox/', $s, 2);
            $y = explode(' ', $x[1]);

            $o->version = $y[0];
        }
        else if (instr($s, 'Chrome'))
        {
            $o->vendor = 'Google';
            $o->name   = 'Chrome';

            // XXX FIXME use a regexp
            $x = explode('Chrome/', $s, 2);
            $y = explode(' ', $x[1]);

            $o->version = $y[0];
        }
        else if (instr($s, 'Safari'))
        {
            $o->vendor = 'Apple';
            $o->name   = 'Safari';

            // Beginning from version 3.0, the version number is part of the UA string as "Version/xxx"
            if (instr($s, 'Version/'))
            {
                $x = explode('Version/', $s, 2);
                $y = explode(' ', $x[1]);
                $o->version = $y[0];
            }
            else
            {
                // XXX FIXME use a regexp
                $x = explode('Safari/', $s, 2);
                $y = explode(' ', $x[1]);

                switch ($y[0]) {
                case '419.3':  $o->version = '2.0.4'; break;
                default: $o->version = 'build '.$y[0].' (unknown version)';
                }
            }
        }
        else if (instr($s, 'Opera'))
        {
            $o->vendor = 'Opera Software';
            $o->name   = 'Opera';

            // Beginning from version 10.00, the version number is part of the UA string as "Version/xxx"
            if (instr($s, 'Version/'))
            {
                $x = explode('Version/', $s, 2);
                $y = explode(' ', $x[1]);
                $o->version = $y[0];
            }
            else
            {
                // XXX FIXME use a regexp
                $x = explode('Opera/', $s, 2);
                $y = explode(' ', $x[1]);
                $o->version = $y[0];
            }
        }
        else if (instr($s, 'MSIE'))
        {
            $o->vendor = 'Microsoft';
            $o->name   = 'Internet Explorer';

            $x = explode('MSIE ', $s, 2);
            $y = explode(';', $x[1]);
            $o->version = $y[0];
        }

        return $o;
    }

}

?>
