<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('io_ini.php');

class io_initTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $ini_file = '/tmp/test.ini';

        $x = new ini($ini_file);

        $x->set('Category', 'spex', 17 );
        $val = $x->get('Category', 'spex');

        unlink($ini_file);

        $this->assertEquals($val, 17);
    }
}
