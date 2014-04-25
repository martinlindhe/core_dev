<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('output_xhtml.php');

class output_xhtmlTest extends \PHPUnit_Framework_TestCase
{
    function test1()
    {
        $ex = xmlentities('jag < vill > ibland & att " det blir sol');
        $this->assertEquals($ex, 'jag &amp;lt; vill &amp;gt; ibland &amp; att &amp;quot; det blir sol');
    }
}

