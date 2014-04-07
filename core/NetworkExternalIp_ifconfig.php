<?php

// STATUS: worked but very slow 2014-04-07

namespace cd;

class NetworkExternalIp_ifconfig
{
    static function GetExternalIp()
    {
        $url = 'http://ifconfig.me/ip';

        $res = file_get_contents($url);

        // TODO verify its an ipv4
        return trim($res);
    }
}
