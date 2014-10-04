<?php

namespace cd;

// TODO add coredev_testserver files to core_dev git repo

//$http->addRequestHeader('Accept-Language: sv'); // TODO test that request header is built properly


class HttpClientTest extends \PHPUnit_Framework_TestCase
{
    function testParseResponseHeader()
    {
        $content_type = 'text/html; charset=utf-8';
        $this->assertEquals(
            HttpClient::parseResponseHeader('charset', $content_type),
            'utf-8'
        );
    }

    /*
    function testGetResponseHeader()
    {
        $http = new HttpClient('https://www.google.com/');
        $body = $http->getBody();

        // d( $http->getAllResponseHeaders() );

        // NOTE: worked 2014-04-25
        $this->assertSame( $http->getResponseHeader('content-type'), 'text/html; charset=UTF-8' );
        $this->assertSame( $http->getResponseHeader('server'), 'GFE/2.0' );
        $this->assertSame( $http->getStatus(), 302 );
    }
    */

    function testPostForm1()
    {
        $http = new HttpClient('http://localhost/coredev_testserver/post_form.php');
        $res = $http->post( array('str' => 'abc 123') );
        $this->assertEquals($res, 'str=abc 123');
    }

    function testPostForm2()
    {
        $http = new HttpClient('http://localhost/coredev_testserver/post_form.php');
        $res = $http->post( array('str' => 'åäö 123') );
        $this->assertEquals($res, 'str=åäö 123');
    }

    function testJsonPost()
    {
        $http = new HttpClient('http://localhost/coredev_testserver/post_json.php');

        $http->setContentType('application/json');
        $http->setDebug(true);
        $res = $http->post( array('str' => 'abc 123') );

        $this->assertEquals($res, 'str=abc+123');
    }

}
