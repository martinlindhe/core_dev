<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('IShortUrlClient.php');

class ShortUrlClientTest extends \PHPUnit_Framework_TestCase
{
    var $url1 = 'http://developer.yahoo.com/yui/editor/';

    public function test1()
    {
        $this->assertEquals(
            ShortUrlClientIsGd::shorten($this->url1),
            'http://is.gd/wmNgf8'
        );
    }

    public function test2()
    {
        $this->assertEquals(
            ShortUrlClientTinyUrl::shorten($this->url1),
            'http://tinyurl.com/yo8mvg'
        );
    }

    public function test3()
    {
        $this->assertEquals(
            ShortUrlClientBitLy::shorten($this->url1),
            'http://yhoo.it/hh2DIJ'
        );
    }

    public function test4()
    {
        $this->assertEquals(
            ShortUrlClientGooGl::shorten($this->url1),
            'http://goo.gl/wMko'
        );
    }

}
