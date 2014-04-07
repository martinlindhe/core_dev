<?php

require_once('NetworkExternalIp_curlmyip.php');
require_once('NetworkExternalIp_dyndns.php');
require_once('NetworkExternalIp_icanhazip.php');
require_once('NetworkExternalIp_ifconfig.php');
require_once('NetworkExternalIp_ipinfo.php');
require_once('NetworkExternalIp_whatismyipaddress.php');

class NetworkExternalIp
{
    static function GetExternalIp()
    {
        // ipinfo supports HTTPS
        $ip = NetworkExternalIp_ipinfo::GetExternalIp();
        if (!$ip)
            throw new \Exception ("no data returned");

        return $ip;
    }

}
