<?php

namespace cd;

class networkTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $this->assertEquals(IPv4_to_GeoIP('192.168.0.1'), 3232235521);
        $this->assertEquals(GeoIP_to_IPv4(IPv4_to_GeoIP('192.168.0.1')), '192.168.0.1');
    }

    public function test2()
    {
        $allowed = array(
            '192.168.0.1',
            '80.0.0.0/8',
            '240.0.0.0/8',
            '230.230.0.0/16',
            '220.220.220.0/24',
        );

        $this->assertEquals( match_ip('220.220.220.42', $allowed), true);
        $this->assertEquals( match_ip('230.230.11.42', $allowed), true);
        $this->assertEquals( match_ip('240.212.11.42', $allowed), true);

        $this->assertEquals( match_ip('230.212.11.42', $allowed), false);
        $this->assertEquals( match_ip('220.220.11.42', $allowed), false);
        $this->assertEquals( match_ip('241.212.11.42', $allowed), false);
        $this->assertEquals( match_ip('240.212.11.42.1', $allowed), false);
        $this->assertEquals( match_ip('111.111.111.111/8', $allowed), false);
        $this->assertEquals( match_ip('300.111.111.111', $allowed), false);
    }

    public function test3()
    {
        $valid_urls = array(
        'http://www.google.se/search?hl=sv&source=hp&q=&btnI=Jag+har+tur&meta=&aq=f&aqi=&aql=&oq=&gs_rfai=',
        'https://some-url.com/?query=&name=joe?filter=*.*#some_anchor',
        'http://valid-url.without.space.com',
        'http://127.0.0.1/test',
        'http://hh-1hallo.msn.blabla.com:80800/test/test/test.aspx?dd=dd&id=dki',
        'http://web5.uottawa.ca/admingov/reglements-methodes.html',
        'ftp://username:password@example.com:21/file.zip',
        'http://www.esa.int',
        'http://at.activation.com/track/me;1442:PPS35:tta/',
        'http://maps.google.com/maps/geo?ll=11.11,11.11&output=json&key=2sddf-d3d3-d3d3d',
        'http://url.com/path|path2',
        'http://url.net/What\'s%20new%20in%20V4.9a.txt',
        'http://server.com/file.php',
        'https://server.com/file.php',
        'http://server.com:1000/file.php',
        'http://server.com:80/file.php',
        'http://server.com/',
        'http://server.com/~xx/file.htm',
        'http://server.com/path?arg=value',
        'http://server.com/path?arg=value#anchor',
        'http://server.com/path?arg=value&arg2=4',
        'http://server.com/path?arg=value&amp;arg2=4',
        'http://username@server.com/path?arg=value',
        'http://username:password@server.com/path?arg=value',
        'http://digg.com/submit?phase=2&url=http&#37;3A&#37;2F&#37;2Fexample.com%2Fpath%2F2on%2F%3Fdomain%3Dp1&p2=text%3A+string',
        'http://en.wikipedia.org/wiki/Yahoo!',
        'http://server.com/aaa@aaa@bbb',
        'HTTP://server.com/aaa@aaa@bbb',
        );

        foreach ($valid_urls as $url) {
            $this->assertEquals( is_url($url), true, "URL FAIL BUT IS VALID ".$url);
        }

        $invalid_urls = array(
        'x',
        'x.com',
        'x.x',
        'http:/invalid.com/test',
        'http://-invalid.leading-char.com',
        'http:// invalid with spaces.com',
        'http://invalid.url-with a.space.com',
        'http://space in url.com/path.php',
        'http://good-domain.com/bad url with space',
        // 'https://ssl.',   //XXX is detected as valid
        );

        foreach ($invalid_urls as $url) {
            $this->assertEquals( is_url($url), false, "URL SUCCESS BUT IS INVALID: ".$url);
        }

    }
}
