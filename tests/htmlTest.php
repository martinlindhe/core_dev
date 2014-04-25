<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('html.php');

class htmlTest extends \PHPUnit_Framework_TestCase
{
    public function testHtmlCharsDecode()
    {
        $this->assertEquals(htmlchars_decode('ja&nbsp;ha'), 'ja ha'); // TODO hex encode nbsp character
        $this->assertEquals(htmlchars_decode('reg&reg;me'), 'reg®me');
        $this->assertEquals(htmlchars_decode('&#039;'), "'");
    }

    public function testIsHtmlColor()
    {
        $this->assertEquals(is_html_color('#fft0ff'), false);
        $this->assertEquals(is_html_color('#ff0ff'), false);
        $this->assertEquals(is_html_color('#ffff'), false);
        $this->assertEquals(is_html_color('#ff'), false);
        $this->assertEquals(is_html_color('#f'), false);
        $this->assertEquals(is_html_color('aff'), false);
        $this->assertEquals(is_html_color(''), false);

        $this->assertEquals(is_html_color('#fff'), true);
        $this->assertEquals(is_html_color('#FFF'), true);
        $this->assertEquals(is_html_color('#ff00ff'), true);
        $this->assertEquals(is_html_color('#FF00FF'), true);

    }

    public function testRelUrl()
    {
        $this->assertEquals(relurl('/'), '/');
        $this->assertEquals(relurl('?val'), '?val');
        $this->assertEquals(relurl('abp://subscribe'), 'abp://subscribe');
    }

    public function testStripHtml()
    {

        $s =
        'hello '.
        '<style>.box h1 {text-align:left;}</style>'.
        '<style type="text/css">.xxx</style>'.
        '<script>strip me</script>'.
        '<script language="text/javascript">strip me</script>'.
        'world';
        $this->assertEquals(strip_html($s), 'hello world');

        $this->assertEquals(strip_html('hi<!--comment--> bye'), 'hi bye');

        $s = '<!--[if gte mso 9]><x>val</x><![endif]--> res <br/>';
        $this->assertEquals(strip_html($s), ' res ');

        $s = '<!--[if gte mso 9]><w>0</w><![endif]
        -->';
        $this->assertEquals(strip_html($s), '');

        $s = '<!-- c1 -->SHOULD_SHOW<!-- c2 -->ALWAYS_SHOWS';
        $this->assertEquals(strip_html($s), 'SHOULD_SHOWALWAYS_SHOWS');
    }

    public function testIsJson()
    {
        $this->assertEquals(is_json('[]'), true);
        $this->assertEquals(is_json('[1]'), true);
        $this->assertEquals(is_json('[1.23]'), true);
        $this->assertEquals(is_json('["a"]'), true);
        $this->assertEquals(is_json('["a",1]'), true);
        $this->assertEquals(is_json('[1,"a",["b"]]'), true);

        $this->assertEquals(is_json('x'), false);
        $this->assertEquals(is_json('['), false);
        $this->assertEquals(is_json('[]]'), false);
        $this->assertEquals(is_json('[[]'), false);
        $this->assertEquals(is_json('["a"'), false);

    }
}
