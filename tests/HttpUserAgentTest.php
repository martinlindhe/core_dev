<?php

namespace cd;

class HttpUserAgentTest extends \PHPUnit_Framework_TestCase
{
    public function testFirefox1()
    {
        $s = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; it; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Mozilla');
        $this->assertEquals($b->name, 'Firefox');
        $this->assertEquals($b->version, '2.0.0.11');
    }

    public function testFirefox2()
    {
        $s = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.8) Gecko/20100723 Ubuntu/10.04 (lucid) Firefox/3.6.8';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Mozilla');
        $this->assertEquals($b->name, 'Firefox');
        $this->assertEquals($b->version, '3.6.8');
    }

    public function testFirefox3()
    {
        $s = 'Mozilla/5.0 (X11; Linux x86_64; rv:6.0) Gecko/20100101 Firefox/6.0';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Mozilla');
        $this->assertEquals($b->name, 'Firefox');
        $this->assertEquals($b->version, '6.0');
        $this->assertEquals($b->os, 'X11');
        $this->assertEquals($b->arch, 'Linux x86_64');
    }

    public function testFirefox4()
    {
        $s = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Mozilla');
        $this->assertEquals($b->name, 'Firefox');
        $this->assertEquals($b->version, '9.0.1');
        $this->assertEquals($b->os, 'Windows NT 6.1');
        $this->assertEquals($b->arch, 'WOW64');
    }

    public function testFirefox5()
    {
        $s = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:10.0) Gecko/20100101 Firefox/10.0';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Mozilla');
        $this->assertEquals($b->name, 'Firefox');
        $this->assertEquals($b->version, '10.0');
        $this->assertEquals($b->os, 'X11');
        $this->assertEquals($b->arch, 'Linux x86_64');
    }

    public function testFirefox6()
    {
        $s = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:10.0) Gecko/20100101 Firefox/10.0';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Mozilla');
        $this->assertEquals($b->name, 'Firefox');
        $this->assertEquals($b->version, '10.0');
        $this->assertEquals($b->os, 'Macintosh');
        $this->assertEquals($b->arch, 'Intel Mac OS X 10.7');
    }

    public function testFirefox7()
    {
        $s = 'Mozilla/5.0 (X11; Linux x86_64; rv:28.0) Gecko/20100101 Firefox/28.0';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Mozilla');
        $this->assertEquals($b->name, 'Firefox');
        $this->assertEquals($b->version, '28.0');
        $this->assertEquals($b->os, 'X11');
        $this->assertEquals($b->arch, 'Linux x86_64');
    }

    public function testFirefox8()
    {
        // latest stable as of 2014-04-24
        $s = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:28.0) Gecko/20100101 Firefox/28.0';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Mozilla');
        $this->assertEquals($b->name, 'Firefox');
        $this->assertEquals($b->version, '28.0');
        $this->assertEquals($b->os, 'Macintosh');
        $this->assertEquals($b->arch, 'Intel Mac OS X 10.9');
    }

    public function testChrome1()
    {
        $s = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_7) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/11.0.696.68 Safari/534.24';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Google');
        $this->assertEquals($b->name, 'Chrome');
        $this->assertEquals($b->version, '11.0.696.68');
        $this->assertEquals($b->os, 'Macintosh');
        $this->assertEquals($b->arch, 'Intel Mac OS X 10_6_7');
    }

    public function testChrome2()
    {
        $s = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.113 Safari/534.30';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Google');
        $this->assertEquals($b->name, 'Chrome');
        $this->assertEquals($b->version, '12.0.742.113');
        $this->assertEquals($b->os, 'Windows NT 6.1');
    }

    public function testChrome3()
    {
        $s = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.75 Safari/535.7';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Google');
        $this->assertEquals($b->name, 'Chrome');
        $this->assertEquals($b->version, '16.0.912.75');
        $this->assertEquals($b->os, 'Windows NT 6.1');
        $this->assertEquals($b->arch, 'WOW64');
    }

    public function testChrome4()
    {
        $s = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.77 Safari/535.7';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Google');
        $this->assertEquals($b->name, 'Chrome');
        $this->assertEquals($b->version, '16.0.912.77');
        $this->assertEquals($b->os, 'X11');
        $this->assertEquals($b->arch, 'Linux x86_64');
    }

    public function testChrome5()
    {
        $s = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.77 Safari/535.7';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Google');
        $this->assertEquals($b->name, 'Chrome');
        $this->assertEquals($b->version, '16.0.912.77');
        $this->assertEquals($b->os, 'Macintosh');
        $this->assertEquals($b->arch, 'Intel Mac OS X 10_7_3');
    }

    public function testChrome6()
    {
        // latest stable as of 2014-04-24
        $s = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Google');
        $this->assertEquals($b->name, 'Chrome');
        $this->assertEquals($b->version, '34.0.1847.116');
        $this->assertEquals($b->os, 'X11');
        $this->assertEquals($b->arch, 'Linux x86_64');
    }

    public function testSafari1()
    {
        $s = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en) AppleWebKit/418.8 (KHTML, like Gecko) Safari/419.3';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Apple');
        $this->assertEquals($b->name, 'Safari');
        $this->assertEquals($b->version, '2.0.4');
        $this->assertEquals($b->os, 'Macintosh');
        $this->assertEquals($b->arch, 'Intel Mac OS X');
    }

    public function testSafari2()
    {
        $s = 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US) AppleWebKit/525.28 (KHTML, like Gecko) Version/3.2.2 Safari/525.28.1';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Apple');
        $this->assertEquals($b->name, 'Safari');
        $this->assertEquals($b->version, '3.2.2');
        $this->assertEquals($b->os, 'Windows');
        $this->assertEquals($b->arch, 'Windows NT 5.2');
    }

    public function testSafari3()
    {
        $s = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/4.0.5 Safari/531.22.7';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Apple');
        $this->assertEquals($b->name, 'Safari');
        $this->assertEquals($b->version, '4.0.5');
        $this->assertEquals($b->os, 'Windows');
        $this->assertEquals($b->arch, 'Windows NT 6.0');
    }

    public function testSafari4()
    {
        $s = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Apple');
        $this->assertEquals($b->name, 'Safari');
        $this->assertEquals($b->version, '5.0.4');
        $this->assertEquals($b->os, 'Windows');
        $this->assertEquals($b->arch, 'Windows NT 6.1');
    }

    public function testSafari5()
    {
        $s = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/534.53.11 (KHTML, like Gecko) Version/5.1.3 Safari/534.53.10';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Apple');
        $this->assertEquals($b->name, 'Safari');
        $this->assertEquals($b->version, '5.1.3');
        $this->assertEquals($b->os, 'Macintosh');
        $this->assertEquals($b->arch, 'Intel Mac OS X 10_7_3');
    }

    public function testSafari6()
    {
        // latest stable as of 2014-04-24
        $s = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/537.75.14';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Apple');
        $this->assertEquals($b->name, 'Safari');
        $this->assertEquals($b->version, '7.0.3');
        $this->assertEquals($b->os, 'Macintosh');
        $this->assertEquals($b->arch, 'Intel Mac OS X 10_9_2');
    }

    public function testSafariiOS1()
    {
        $s = 'Mozilla/5.0 (iPhone; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.10';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals(HttpUserAgent::isIOS($s), true);
        $this->assertEquals($b->vendor, 'Apple');
        $this->assertEquals($b->name, 'Safari');
        $this->assertEquals($b->version, '4.0.4');
        $this->assertEquals($b->os, 'iPhone');
        $this->assertEquals($b->arch, 'CPU OS 3_2 like Mac OS X');
    }

    public function testSafariiOS2()
    {
        $s = 'Mozilla/5.0 (iPad;U;CPU OS 3_2_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B500 Safari/531.21.10';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals(HttpUserAgent::isIOS($s), true);
        $this->assertEquals($b->vendor, 'Apple');
        $this->assertEquals($b->name, 'Safari');
        $this->assertEquals($b->version, '4.0.4');
        $this->assertEquals($b->os, 'iPad');
        $this->assertEquals($b->arch, 'CPU OS 3_2_2 like Mac OS X');
    }

    public function testSafariiOS3()
    {
        $s = 'Mozilla/5.0 (iPod; U; CPU iPhone OS 4_3_3 like Mac OS X; ja-jp) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals(HttpUserAgent::isIOS($s), true);
        $this->assertEquals($b->vendor, 'Apple');
        $this->assertEquals($b->name, 'Safari');
        $this->assertEquals($b->version, '5.0.2');
        $this->assertEquals($b->os, 'iPod');
        $this->assertEquals($b->arch, 'CPU iPhone OS 4_3_3 like Mac OS X');
    }

    public function testSafariiOS4()
    {
        $s = 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A405 Safari/7534.48.3';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals(HttpUserAgent::isIOS($s), true);
        $this->assertEquals($b->vendor, 'Apple');
        $this->assertEquals($b->name, 'Safari');
        $this->assertEquals($b->version, '5.1');
        $this->assertEquals($b->os, 'iPhone');
        $this->assertEquals($b->arch, 'CPU iPhone OS 5_0_1 like Mac OS X');
    }

    public function testSafariiOS5()
    {
        // version shipped with iOS 7.0.4, at 2014-04-24
        $s = 'Mozilla/5.0 (iPhone; CPU iPhone OS 7_0_4 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11B554a Safari/9537.53';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals(HttpUserAgent::isIOS($s), true);
        $this->assertEquals($b->vendor, 'Apple');
        $this->assertEquals($b->name, 'Safari');
        $this->assertEquals($b->os, 'iPhone');
        $this->assertEquals($b->arch, 'CPU iPhone OS 7_0_4 like Mac OS X');
    }

    public function testIe1()
    {
        $s = 'Mozilla/4.0 (compatible; MSIE 4.01; Windows 98)';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Microsoft');
        $this->assertEquals($b->name, 'Internet Explorer');
        $this->assertEquals($b->version, '4.01');
    }

    public function testIe2()
    {
        $s = 'Mozilla/4.0 (compatible; MSIE 5.23; Mac_PowerPC)';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Microsoft');
        $this->assertEquals($b->name, 'Internet Explorer');
        $this->assertEquals($b->version, '5.23');
    }

    public function testIe3()
    {
        $s = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Microsoft');
        $this->assertEquals($b->name, 'Internet Explorer');
        $this->assertEquals($b->version, '6.0');
    }

    public function testIe4()
    {
        $s = 'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Microsoft');
        $this->assertEquals($b->name, 'Internet Explorer');
        $this->assertEquals($b->version, '7.0');
    }

    public function testIe5()
    {
        $s = 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727)';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Microsoft');
        $this->assertEquals($b->name, 'Internet Explorer');
        $this->assertEquals($b->version, '8.0');
    }

    public function testIe6()
    {
        $s = '"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0)';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Microsoft');
        $this->assertEquals($b->name, 'Internet Explorer');
        $this->assertEquals($b->version, '9.0');
    }

    public function testIe7()
    {
        $s = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Microsoft');
        $this->assertEquals($b->name, 'Internet Explorer');
        $this->assertEquals($b->version, '10.0');
    }

    public function testIe8()
    {
        //latest stable as of 2014-04-24
        $s = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; Trident/7.0; rv:11.0) like Gecko';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Microsoft');
        $this->assertEquals($b->name, 'Internet Explorer');
        $this->assertEquals($b->version, '11.0');
    }

    public function testOpera1()
    {
        $s = 'Opera/9.00 (Windows NT 5.1; U; en)';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Opera Software');
        $this->assertEquals($b->name, 'Opera');
        $this->assertEquals($b->version, '9.00');
        $this->assertEquals($b->os, 'Windows NT 5.1');
    }

    public function testOpera2()
    {
        $s = 'Opera/9.50 (Macintosh; Intel Mac OS X; U; en)';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Opera Software');
        $this->assertEquals($b->name, 'Opera');
        $this->assertEquals($b->version, '9.50');
        $this->assertEquals($b->os, 'Macintosh');
    }

    public function testOpera3()
    {
        $s = 'Opera/9.80 (X11; Linux x86_64; U; en) Presto/2.2.15 Version/10.00';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Opera Software');
        $this->assertEquals($b->name, 'Opera');
        $this->assertEquals($b->version, '10.00');
        $this->assertEquals($b->os, 'X11');
    }

    public function testOpera4()
    {
        $s = 'Opera/9.80 (Windows NT 6.0; U; en) Presto/2.8.99 Version/11.10';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Opera Software');
        $this->assertEquals($b->name, 'Opera');
        $this->assertEquals($b->version, '11.10');
        $this->assertEquals($b->os, 'Windows NT 6.0');
    }

    public function testOpera5()
    {
        // latest stable as of 2012-02-08
        $s = 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.10.229 Version/11.61';
        $b = HttpUserAgent::getBrowser($s);
        $this->assertEquals($b->vendor, 'Opera Software');
        $this->assertEquals($b->name, 'Opera');
        $this->assertEquals($b->version, '11.61');
        $this->assertEquals($b->os, 'Windows NT 6.1');
    }
}
