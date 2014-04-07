<?php

// STATUS: worked but was blocking after like TWO requests, 2014-04-07

namespace cd;

class NetworkExternalIp_dyndns
{
    static function GetExternalIp()
    {
        $url = 'http://checkip.dyndns.org/';

        $res = file_get_contents($url);
        $res = strip_tags($res);

        // Current IP Address: 1.2.3.4
        $x = explode(": ", $res);
        if (count($x) != 2)
            throw new \Exception ("parse error");

        // TODO verify its an ipv4
        return trim($x[1]);
    }
}
