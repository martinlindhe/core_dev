<?php

namespace cd;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        // test url manipulation
        $url = new Url();
        $url->set('http://www.test.com/');
        $this->assertEquals($url->getPath(), '/');

        $url->setParam('cat', 1);
        $this->assertEquals($url->get(), 'http://www.test.com/?cat=1');
        $this->assertEquals($url->getPath(), '/?cat=1');

        $url->removeParam('cat');
        $url->setParam('t', 'kalas');
        $url->setParam('f', 'bas');
        $this->assertEquals($url->get(), 'http://www.test.com/?t=kalas&f=bas');
        $this->assertEquals($url->getPath(), '/?t=kalas&f=bas');

        $url = new Url('http://test.com/?param');
        $this->assertEquals($url->get(), 'http://test.com/?param');
        $this->assertEquals($url->getPath(), '/?param');

        $url = new Url('http://test.com/?p=n;hb=HEAD');
        $this->assertEquals($url->get(), 'http://test.com/?p=n;hb=HEAD');
        $this->assertEquals($url->getPath(), '/?p=n;hb=HEAD');

        $url = new Url('http://test.com/?q=minuter+p%E5+skoj');
        $this->assertEquals($url->get(), 'http://test.com/?q=minuter+p%E5+skoj');
        $this->assertEquals($url->getPath(), '/?q=minuter+p%E5+skoj');
    }
}
