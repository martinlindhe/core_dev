<?php

namespace cd;

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
        //$ip = NetworkExternalIp_ipinfo::GetExternalIp(); // TODO: returns 401 unauthorized
        $ip = NetworkExternalIp_whatismyipaddress::GetExternalIp();

        if (!$ip)
            throw new \Exception ("no data returned");

        // TODO: is_ipv4()
        if (strlen($ip) > 30)
            throw new \Exception ("invalid data");

        return $ip;
    }

}
