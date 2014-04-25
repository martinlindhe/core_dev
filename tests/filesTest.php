<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('files.php');

class filesTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $this->assertSame( arg_match( 'file123.jpg', array('file*.jpg') ), true);
        $this->assertSame( arg_match( 'test-filename.jpg', array('test-*') ), true);
        $this->assertSame( arg_match( 'test-filename.jpg', array('test-*.jpg') ), true);

        $this->assertSame( arg_match( 'filname ending with.php', array('*.php' )), true);
        $this->assertSame( arg_match( 'file.jpg', array('*.gif', '*.jpg') ), true);
        $this->assertSame( arg_match( 'file.bmp', array('*.gif', '*.jpg') ), false);
    }

/*
    public function test2()
    {
        $x = expand_arg_files('/var/log/boot.log', array('*.log') );
        if (count($x) != 1 || $x[0] != '/var/log/boot.log') echo "FAIL x\n";

        $x = expand_arg_files('/home/ml/dev/core_dev/tests/test.files.php', array('*.php') );
        if (count($x) != 1 || $x[0] != '/home/ml/dev/core_dev/tests/test.files.php') echo "FAIL x\n";
    }
*/

    public function test3()
    {
        // verifies current directory holds at least 50 tests

        $tests = dir_get_matches('.', array('*Test.php') );

        $this->assertGreaterThan(50, count($tests));
    }



    /*
    $x = expand_arg_files('/media/downloads/dump/V.2009.S02E0*.avi');
    d($x);
    */


    /*
    $x = expand_arg_files('/var/log', array('*.log', '*.err') );
    d( $x );
    */


    /*
    $x = file_get_random('/home/ml/dev/martin/asterisk/recordings/mojo/');
    d($x);
    */

}
