<?php

// STATUS: worked & was reliable 2014-04-07

namespace cd;

class NetworkExternalIp_curlmyip
{
    static function GetExternalIp()
    {
        $url = 'http://curlmyip.com/';

        $res = file_get_contents($url);

        // TODO verify its an ipv4
        return trim($res);
    }
}
