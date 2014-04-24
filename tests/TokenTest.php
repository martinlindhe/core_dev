<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('Token.php');

require_once('/home/ml/dev/fmf/textfeed/config.php'); //XXX for database

class TokenTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        // TODO make proper test
        $tok = new Token();
        $val = $tok->generate(1000, 'bajs');

        echo $val."\n";
    }
}
