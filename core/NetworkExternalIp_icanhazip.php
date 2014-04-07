<?php

// STATUS: worked & was reliable 2014-04-07

// TODO there is a ipv6.icanhazip.com subdomain

namespace cd;

class NetworkExternalIp_icanhazip
{
    static function GetExternalIp()
    {
        $url = 'http://icanhazip.com';

        $ext_ip = file_get_contents($url);

        // TODO verify its an ipv4

        return trim($ext_ip);
    }
}
