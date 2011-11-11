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

//TODO: parse more os & arch from HttpUserAgent into WebBrowser

/* XXX Android Webkit browser

Mozilla/5.0 (Linux; U; Android 1.6; ar-us; SonyEricssonX10i Build/R2BA026) AppleWebKit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1
Mozilla/5.0 (Linux; U; Android 2.2.1; en-gb; HTC_DesireZ_A7272 Build/FRG83D) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1
Mozilla/5.0 (Linux; U; Android 2.2; fr-lu; HTC Legend Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1
Mozilla/5.0 (Linux; U; Android 2.3.3; zh-tw; HTC_Pyramid Build/GRI40) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1
Mozilla/5.0 (Linux; U; Android 2.3.4; fr-fr; HTC Desire Build/GRJ22) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1
*/

/*
XXX Windows Mobile

Mozilla/4.0 (compatible; MSIE 7.0; Windows Phone OS 7.0; Trident/3.1; IEMobile/7.0; Nokia;N70)
Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0)
*/

/*
XXX Opera Mini

Opera/9.80 (iPhone; Opera Mini/5.0.019802/22.414; U; de) Presto/2.5.25 Version/10.54
Opera/9.80 (J2ME/MIDP; Opera Mini/9 (Compatible; MSIE:9.0; iPhone; BlackBerry9700; AppleWebKit/24.746; U; en) Presto/2.5.25 Version/10.54
Opera/9.80 (J2ME/MIDP; Opera Mini/9.80 (J2ME/23.377; U; en) Presto/2.5.25 Version/10.54
*/

/**
XXX Opera Mobile

Opera/9.80 (Android 2.3.4; Linux; Opera Mobi/build-1107180945; U; en-GB) Presto/2.8.149 Version/11.10
Opera/9.80 (Android 2.2; Linux; Opera Mobi/ADR-2093533312; U; pl) Presto/2.7.60 Version/10.5
Opera/9.80 (S60; SymbOS; Opera Mobi/447; U; en) Presto/2.4.18 Version/10.00
*/

require_once('core.php');

class WebBrowser
{
    var $vendor;    ///< string "Microsoft", "Google", "Mozilla"
    var $name;      ///< string "Chrome", "Firefox", "Internet Explorer"
    var $version;   ///< string "13.0.782.112", "6.0"
    var $os;        ///< string "Linux", "Windows", "Macintosh", "iPhone", "iPad", "iPod"
//    var $os_version; // string
    var $arch;      ///< string "x86_64", "CPU OS 3_2 like Mac OS X"
    var $language;  ///< "en", "en-us"
}

class HttpUserAgent
{
    /**
     * @return true if UA is a recent "smartphone" (Andriod, iOS, Windows Mobile 7)
     */
    public static function isSmartphone($s)
    {
        throw new Exception ('FIXME implement');
    }

    /**
     * @return true if UA is a iOS device
     */
    public static function isIOS($s)
    {
        $b = self::getBrowser($s);
        if ($b->os == 'iPhone' || $b->os == 'iPod' || $b->os == 'iPad')
            return true;

        return false;
    }

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

            $tok1 = 'Mozilla/5.0 (';
            $p1 = strpos($s, $tok1);
            if ($p1 !== false) {
                $s1 = substr($s, $p1 + strlen($tok1) );

                $p2 = strpos($s1, ')');
                $s2 = substr($s1, 0, $p2);

                $x = explode(';', $s2);

                // (iPhone; U; CPU OS 3_2 like Mac OS X; en-us)
                // (Macintosh; U; Intel Mac OS X; en)
                // (Windows; U; Windows NT 6.1; en-US)
                $o->os   = trim($x[0]);
                $o->arch = trim($x[2]);
                $o->language = strtolower(trim($x[3]));
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
        else
            dp('XXX unrecognized UA string: '.$s);

        return $o;
    }

}

?>
