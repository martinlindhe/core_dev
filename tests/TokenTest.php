<?php

namespace cd;

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
