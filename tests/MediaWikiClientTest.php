<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('MediaWikiClient.php');

class MediaWikiClientTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $title = MediaWikiClient::getArticleTitle('http://en.wikipedia.org/wiki/C%2B%2B');

        $this->assertEquals($title, 'C++');
    }

    public function test2()
    {
        $this->assertEquals( is_mediawiki_url('http://sv.wiktionary.org/wiki/bestick'), true);
        $this->assertEquals( is_mediawiki_url('https://en.wikipedia.org/wiki/Cutlery'), true);

        $this->assertEquals( is_mediawiki_url('http://en.wikipedia.org/wwapw'), false);
        $this->assertEquals( is_mediawiki_url('http://www.www.com/wwapw'), false);
    }
}
