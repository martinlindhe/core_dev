<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('NetworkExternalIp.php');

class NetworkExternalIpTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $ip = NetworkExternalIp_ifconfig::GetExternalIp();
        echo $ip."\n";
    }

    /*
    public function test2()
    {
        $ip = NetworkExternalIp_icanhazip::GetExternalIp();
        echo $ip."\n";
    }

    public function test3()
    {
        $ip = NetworkExternalIp_whatismyipaddress::GetExternalIp();
        echo $ip."\n";
    }

    public function test4()
    {
        $ip = NetworkExternalIp_dyndns::GetExternalIp();  // NOTE unreliable
        echo $ip."\n";
    }

    public function test5()
    {
        $ip = NetworkExternalIp_curlmyip::GetExternalIp();
        echo $ip."\n";
    }
    */
}
