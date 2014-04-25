<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('Version.php');

class VersionTest extends \PHPUnit_Framework_TestCase
{
    public function testStrpreExact1()
    {
        $x = new Version('4.0.32.233');
        echo $x->get()."\n";


        $x = new Version('r123');
        echo $x->get()."\n";

    }
}
