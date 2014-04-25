<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('Json.php');


class JsonTest extends \PHPUnit_Framework_TestCase
{
    function test_encode1()
    {
        $o = new \StdClass();
        $o->x = 1;
        $o->y = 2;
        $o->z = 3;
        // JSON objects are encoded as {prop1:val,prop2:val}
        $this->assertEquals(JSON::encode($o), '{"x":1,"y":2,"z":3}');
    }

    function test_encode2()
    {
        $this->assertEquals(JSON::encode( 1 ), '1');
        $this->assertEquals(JSON::encode( 'hello' ), '"hello"');
        $this->assertEquals(JSON::encode( 'häger' ), '"häger"');

        $this->assertEquals(JSON::encode(array('5-1' => 'a b c')),   '{"5-1":"a b c"}' );
        $this->assertEquals(JSON::encode(array('0123' => 'abc')),    '{"0123":"abc"}' );
        $this->assertEquals(JSON::encode(array('0123' => 0.5)),      '{"0123":0.5}' );
        $this->assertEquals(JSON::encode(array('0123' => 0)),        '{"0123":0}' );
        $this->assertEquals(JSON::encode(array('0123' => 1)),        '{"0123":1}' );
        $this->assertEquals(JSON::encode(array('123' => '0123')),    '{"123":"0123"}' );
        $this->assertEquals(JSON::encode(array('123' => 'a.b')),     '{"123":"a.b"}' );
        $this->assertEquals(JSON::encode(array('a.b' => '123')),     '{"a.b":"123"}' );
        $this->assertEquals(JSON::encode(array(0 => '0123')),        '{"0":"0123"}' );
        $this->assertEquals(JSON::encode(array(1 => '0123')),        '{"1":"0123"}' );
        $this->assertEquals(JSON::encode(array('a' => 1, 'b' => 2)), '{"a":1,"b":2}' );
    }
}



