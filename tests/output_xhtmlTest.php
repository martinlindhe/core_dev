<?php

namespace cd;

class output_xhtmlTest extends \PHPUnit_Framework_TestCase
{
    function test1()
    {
        $ex = xmlentities('jag < vill > ibland & att " det blir sol');
        $this->assertEquals($ex, 'jag &amp;lt; vill &amp;gt; ibland &amp; att &amp;quot; det blir sol');
    }
}
