<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('NetworkExternalIp.php');

//$ip_1 = NetworkExternalIp_icanhazip::GetExternalIp();
//$ip_2 = NetworkExternalIp_whatismyipaddress::GetExternalIp();
//$ip_3 = NetworkExternalIp_dyndns::GetExternalIp();  // NOTE unreliable
//$ip_4 = NetworkExternalIp_curlmyip::GetExternalIp();
$ip_5 = NetworkExternalIp_ifconfig::GetExternalIp();

//echo "IP 1: ".$ip_1."\n";
//echo "IP 2: ".$ip_2."\n";
//echo "IP 3: ".$ip_3."\n";
//echo "IP 4: ".$ip_4."\n";
echo "IP 5: ".$ip_5."\n";
