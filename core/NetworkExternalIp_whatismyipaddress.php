<?php

// STATUS: worked & was reliable 2014-04-07

namespace cd;

class NetworkExternalIp_whatismyipaddress
{
    static function GetExternalIp()
    {
        $url = 'http://bot.whatismyipaddress.com';

        $ext_ip = file_get_contents($url);

        // TODO verify its an ipv4

        return trim($ext_ip);
    }
}
