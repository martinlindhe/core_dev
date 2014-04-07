<?php

// http://ipinfo.io/developers

// STATUS: worked & supports HTTPS, 2014-04-07

namespace cd;

class NetworkExternalIp_ipinfo
{
    static function GetExternalIp()
    {
        $url = 'https://ipinfo.io/ip';

        $res = file_get_contents($url);

        // TODO verify its an ipv4
        return trim($res);
    }
}
