<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('GeoLookupClient.php');

class GeoLookupClientTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        // TODO make proper test
        $x = GeoLookupClient::get(59.332169, 18.062429); //= sthlm
        d($x);
    }
}
