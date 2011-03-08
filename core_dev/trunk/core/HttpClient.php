<?php
//XXXXXXX IMPORTANT: renamed getHeader -> getResponseHeader, getHeaders->getResponseHeaders  ... update all users!!!


/**
 * $Id$
 *
 * HTTP Client class to GET/POST data using the HTTP protocol
 *
 * References
 * ----------
 * http://tools.ietf.org/html/rfc2616 - Hypertext Transfer Protocol -- HTTP/1.1
 *
 * NTLM HTTP Authentication
 * http://davenport.sourceforge.net/ntlm.html#ntlmHttpAuthentication
 *
 * @author Martin Lindhe, 2008-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: send gzip compression headers & auto-decompress response

//TODO: extend from Url ???

//FIXME header parsing should preserve all fields, ex:
/*
WWW-Authenticate: Negotiate
WWW-Authenticate: NTLM
*/

/**
 * TODO make NTLM work......... curl
 *
 * auth med user "sweweb":    Authorization: NTLM TlRMTVNTUAABAAAABoIIAAAAAAAAAAAAAAAAAAAAAAA=
 * server response:           WWW-Authenticate: NTLM TlRMTVNTUAACAAAADwAPADgAAAAGgooCD0s3WhzSkKMAAAAAAAAAAIwAjABHAAAABQCTCAAAAA9VTkktWEsxUTNZNkdCRTUCAB4AVQBOAEkALQBYAEsAMQBRADMAWQA2AEcAQgBFADUAAQAeAFUATgBJAC0AWABLADEAUQAzAFkANgBHAEIARQA1AAQAHgB1AG4AaQAtAHgAawAxAHEAMwB5ADYAZwBiAGUANQADAB4AdQBuAGkALQB4AGsAMQBxADMAeQA2AGcAYgBlADUAAAAAAA==
 */

require_once('core.php');
require_once('network.php');

require_once('Url.php');
require_once('TempStore.php');

class HttpClient extends CoreBase
{
    public  $Url;                          ///< Url property
    private $ch;                           ///< curl handle
    private $headers;
    private $body;
    private $status_code;                  ///< return code from http request, such as 404
    private $cache_time         = 0;       ///< in seconds
    private $user_agent         = 'core_dev HttpClient 1.0';
    private $referer            = '';      ///< if set, send Referer header
    private $cookies            = array(); ///< holds cookies to be sent to the server in the following request
    private $connection_timeout = 120;     ///< 2 minutes
    private $content_type       = '';      ///< request content by mime type

    private $username;
    private $password;

    private $auth_method        = '';

    function __construct($url = '')
    {
        if (!function_exists('curl_init')) {
            echo "HttpClient->ERROR: php5-curl missing".ln();
            return false;
        }

        $this->ch = curl_init();

        if (!$this->ch)
            throw new Exception ('curl error: '.curl_errstr($this->ch).' ('.curl_errno($this->ch) );

        $this->Url = new Url($url);
    }

    function __destruct()
    {
        curl_close($this->ch);
    }

    /**
     * Performs a HTTP HEAD request
     */
    function getHead()
    {
        return $this->get(array(), true);
    }

    function getBody()
    {
        return $this->get();
    }

    function post($params)
    {
        return $this->get($params);
    }

    function getResponseHeaders()
    {
        return $this->headers;
    }

    function getResponseHeader($name)
    {
        $name = strtolower($name);

        if (isset($this->headers[ $name ]))
            return $this->headers[$name];

        return false;
    }

    function setConnectionTimeout($n) { if (is_numeric($n)) $this->connection_timeout = $n; }
    function setContentType($s) { $this->content_type = $s; }
    function setReferer($s) { $this->referer = $s; }

    /**
     * Sets a cookie to send with the next HTTP request
     */
    function setCookie($name, $val)
    {
        $this->cookies[ $name ] = $val;
    }

    /**
     * Sets/updates an array (name->val) of cookies
     */
    function setCookies($arr)
    {
        foreach ($arr as $name => $val)
            $this->setCookie($name, $val);
    }

    /**
     * Returns the value of a cookie from last server response
     */
    function getCookie($name)
    {
        if (!isset($this->cookies[ $name ]))
            return false;

        return $this->cookies[ $name ];
    }

    /**
     * Returns all cookies from last server response
     */
    function getCookies() { return $this->cookies; }

    /**
     * Returns HTTP status code for the last request
     */
    function getStatus()
    {
        return $this->status_code;
    }

    function getUrl() { return $this->Url->get(); }

    /**
     * @param $s cache time in seconds; max 2592000 (30 days)
     */
    function setCacheTime($s) { $this->cache_time = $s; }

    function setUserAgent($ua) { $this->user_agent = $ua; }

    function setUrl($s) { $this->Url = new Url($s); }

    function setUsername($s) { $this->username = $s; }
    function setPassword($s) { $this->password = $s; }

    private function setAuthMethod($s)
    {
        $x = explode(' ', $s, 2);

        switch (strtolower($x[0])) {
        case 'basic': // $s = basic realm="name"
            //XXX care about realm?
            $this->auth_method = 'basic';
            break;

        case 'ntlm':  // $s = NTLM
            $this->auth_method = 'NTLM';
            break;

        default:
            d($this->headers);
            throw new Exception ('unhandled auth method: '.$x[0]);
        }

        // force a second request to complete the authentication procedure
        $this->getBody();
    }

    function saveBody($out_file)
    {
        $data = $this->getBody();
        file_put_contents($out_file, $data);
    }

    /**
     * Fetches the data of the web resource
     * uses HTTP AUTH if username is set
     *
     * @param $post_params array of key->val pairs of POST parameters to send
     */
    private function get($post_params = array(), $head_only = false)
    {
        if (!$this->Url->get())
            throw new Exception ('Must set url');

        $temp = TempStore::getInstance();

        if (!$this->username && empty($post_params) && $this->cache_time && !$head_only)
        {
            $key_head = 'HttpClient/head//'.sha1( $this->Url->get() );
            $key_full = 'HttpClient/full//'.sha1( $this->Url->get() );

            $full = $temp->get($key_full);
            if ($full) {
                $this->parseResponse($full);
                return $this->body;
            }
        }

        if ($this->getDebug())
            curl_setopt($this->ch, CURLOPT_VERBOSE, true);

        curl_setopt($this->ch, CURLOPT_URL, $this->Url->get() );

        $headers = array(
        ($this->content_type ? 'Content-Type: '.$this->content_type : ''),
        'Accept-Encoding: gzip'
        );

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        if ($this->Url->getScheme() == 'https') {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->connection_timeout);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->user_agent);

        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($this->ch, CURLOPT_MAXREDIRS, 0);

        curl_setopt($this->ch, CURLOPT_HEADER, 1);
        curl_setopt($this->ch, CURLOPT_NOBODY, $head_only);

        curl_setopt($this->ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);

        if ($this->referer)
            curl_setopt($this->ch, CURLOPT_REFERER, $this->referer);

        if ($this->auth_method) {
            switch ($this->auth_method) {
            case 'basic':
                $key = base64_encode($this->username.':'.$this->password);
                curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Authorization: basic '.$key) );
                break;
/*
            case 'NTLM':  //DONT WORK PROPERLY, 2010-10-20
                curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM); //for "Server: Microsoft-IIS/5.0"
                curl_setopt($this->ch, CURLOPT_USERPWD, $this->username.':'.$this->password);
                curl_setopt($this->ch, CURLOPT_MAXREDIRS, 3);
                break;
*/
            default:
                throw new Exception ('unhandled auth method '.$this->auth_method);
            }
        }

        if ($this->cookies) {
            if ($this->getDebug()) echo "http->get() sending cookies: ".encode_cookie_string($this->cookies).ln();
            curl_setopt($this->ch, CURLOPT_COOKIE, encode_cookie_string($this->cookies));
        }

        if (!empty($post_params)) {
            if ($this->getDebug()) echo "http->post() ".$this->Url->get()." ... ";

            if (is_array($post_params)) {
                $var = http_build_query($post_params);
            } else {
                $var = $post_params;
            }
            if ($this->getDebug()) echo 'BODY: '.$var.' ('.strlen($var).' bytes)'.ln();

            curl_setopt($this->ch, CURLOPT_POST, 1);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $var);
        } else {
            if ($this->getDebug()) echo "http->get() ".$this->Url->get()." ... ".ln();
        }

        $res = curl_exec($this->ch);

        if ($this->getDebug())
            echo "Got ".strlen($res)." bytes".ln(); //, showing first 2000:".ln(); d( substr($res,0,2000) );

        $this->parseResponse($res);

        if (!$this->username && empty($post_params) && $this->cache_time && !$head_only) {
            $temp->set($key_head, serialize($this->headers), $this->cache_time);
            $temp->set($key_full, $res, $this->cache_time);
        }

        return $this->body;
    }

    /**
     * Parse HTTP response data into object variables and sets status code
     */
    private function parseResponse($res)
    {
        $this->headers = array();

        if (!$res)
            return;

        $pos = strpos($res, "\r\n\r\n");
        if ($pos !== false) {
            $head = substr($res, 0, $pos);
            $body = substr($res, $pos + strlen("\r\n\r\n"));
            $headers = explode("\r\n", $head);
        } else {
            $body = '';
            $headers = explode("\r\n", $res);
        }

        $status = array_shift($headers);
        if ($this->getDebug()) echo "http->get() returned HTTP status ".$status.ln();

        switch (substr($status, 0, 9)) {
        case 'HTTP/1.0 ':
        case 'HTTP/1.1 ':
            $this->status_code = intval(substr($status, 9));
            break;
        default:
            throw new Exception ('unhandled HTTP response '.$status);
        }

        foreach ($headers as $h) {
            $col = explode(': ', $h, 2);
            $this->headers[ strtolower($col[0]) ] = $col[1];
        }

        $encoding = $this->getResponseHeader('content-encoding');
        switch ($encoding) {
        case 'gzip':
            // strip 10-byte gzip header  XXXX always 10 byte headers???
            $gzhead = substr($body, 0, 10); // 1f8b 0800 0000 0000 0003 ... gzip start with ED FD sequence (???)
            $body = substr($body, 10);
            $body = gzinflate($body);
            break;

        case '':
            break;

        default:
            d( $this->headers);
            throw new Exception ('unhandled content-encoding: '.$encoding);
        }

        $this->body = $body;

        // store cookies sent from the server in our cookie pool for possible manipulation
        $raw_cookies = $this->getResponseHeader('set-cookie');

        if ($raw_cookies)
            $this->setCookies( decode_cookie_string($raw_cookies) );

        $auth = $this->getResponseHeader('www-authenticate');
        if ($auth)
            $this->setAuthMethod($auth);
    }

}

?>
