<?php
/**
 * HTTP Client class to GET/POST data using the HTTP protocol
 *
 * References
 * ----------
 * http://tools.ietf.org/html/rfc2616 - Hypertext Transfer Protocol -- HTTP/1.1
 *
 * NTLM HTTP Authentication
 * http://davenport.sourceforge.net/ntlm.html#ntlmHttpAuthentication
 *
 * @author Martin Lindhe, 2008-2014 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

require_once('core.php');
require_once('network.php');
require_once('Url.php');
require_once('TempStore.php');

class HttpClient
{
    public    $Url;                          ///< Url property
    protected $ch;                           ///< curl handle
    protected $request_headers    = array();
    protected $response_headers   = array();
    protected $body;
    protected $status_code;                  ///< return code from http request, such as 404
    protected $cache_time         = 0;       ///< in seconds
    protected $user_agent         = 'core_dev HttpClient 1.0';
    protected $referer            = '';      ///< if set, send Referer header
    protected $cookies            = array(); ///< holds cookies to be sent to the server in the following request
    protected $connection_timeout = 120;     ///< 2 minutes
    protected $content_type       = '';      ///< request content by mime type

    protected $username;
    protected $password;

    protected $auth_method        = '';

    protected $debug              = false;

    function __construct($url = '')
    {
        if (!extension_loaded('curl'))
            throw new \Exception ('php5-curl missing');

        $this->ch = curl_init();

        if (!$this->ch)
            throw new \Exception ('curl error: '.curl_errstr($this->ch).' ('.curl_errno($this->ch) );

        $this->Url = new Url($url);
    }

    function __destruct()
    {
        curl_close($this->ch);
    }

    /**
     * Returns all cookies from last server response
     */
    function getCookies() { return $this->cookies; }

    /**
     * Returns HTTP status code for the last request
     */
    function getStatus() { return $this->status_code; }

    function getUrl() { return $this->Url->get(); }

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

    function getAllResponseHeaders() { return $this->response_headers; }

    /**
     * @return value of specified response header name. if more than one value is set, the first is returned
     */
    function getResponseHeader($name)
    {
        $name = strtolower($name);

        if (isset($this->response_headers[ $name ]))
            return $this->response_headers[$name][0];

        return false;
    }

    /**
     * @return all values of specified response header name
     */
    function getResponseHeaders($name)
    {
        $name = strtolower($name);

        if (isset($this->response_headers[ $name ]))
            return $this->response_headers[$name];
        if (isset($this->response_headers[ $name ]))
            return $this->response_headers[$name];

        return false;
    }

    function setDebug($b = true) { $this->debug = $b; }

    /**
     * @param $n duration in seconds, or represented as "30s", "2m"
     */
    function setConnectionTimeout($n) { $this->connection_timeout = parse_duration($n); }

    function setContentType($s) { $this->content_type = $s; }
    function setReferer($s) { $this->referer = $s; }

    function addRequestHeader($s) { $this->request_headers[] = $s; }

    /**
     * Sets a cookie to send with the next HTTP request
     */
    function setCookie($raw)
    {
        $this->cookies[] = $raw;
    }

    /**
     * Sets/updates an array (name->val) of cookies
     */
    /* function setCookies($arr)
    {
        foreach ($arr as $name => $val)
            $this->setCookie($name, $val);
    } */

    /**
     * Returns the value of a cookie from last server response
     */
    /* function getCookie($name)
    {
        if (!isset($this->cookies[ $name ]))
            return false;

        return $this->cookies[ $name ];
    } */

    /**
     * @param $s cache time in seconds or as string "4h", max 2592000 (30 days)
     */
    function setCacheTime($s) { $this->cache_time = parse_duration($s); }

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
            d($this->response_headers);
            throw new \Exception ('unhandled auth method: '.$x[0]);
        }

        // force a second request to complete the authentication procedure
        $this->getBody();
    }

    function saveBody($out_file)
    {
        $data = $this->getBody();
        file_put_contents($out_file, $data);
    }

    function encodeCookies()
    {
        return implode('; ', $this->cookies);
/*
        $res = '';
        foreach ($this->cookies as $key => $val) {
            $res .= $key.'='.$val.'; ';
        }

        //HACK: remove last "; "
        $res = trim($res);
        if (substr($res, -1, 1) == ';')
            $res = substr($res, 0, -1);

        return $res;
*/
    }

    /**
     * Parses a HTTP header "Set-cookie" string into array
     */
    static function decodeCookie($raw)
    {
        $out = array();

        $pairs = explode(';', $raw);

        foreach ($pairs as $key => $val) {
            $x = explode('=', $val, 2);

            if (isset($x[1]))
                $out[ $x[0] ] = $x[1];
            else
                $out[ $x[0] ] = true;
        }
        return $out;
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
            throw new \Exception ('Must set url');

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

        if ($this->debug)
            curl_setopt($this->ch, CURLOPT_VERBOSE, true);

        curl_setopt($this->ch, CURLOPT_URL, $this->Url->get() );

        if ($this->content_type)
            $this->addRequestHeader('Content-Type: '.$this->content_type);

        $this->addRequestHeader('Accept-Encoding: gzip,deflate');
        $this->addRequestHeader('Expect:');  // HACK to disable cURL default to send "100-continue"

        if ($this->Url->getScheme() == 'https') {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        curl_setopt($this->ch, CURLOPT_COOKIESESSION, 1); /// to disable curl:s internal cookie handling

        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->connection_timeout);
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
                $this->addRequestHeader('Authorization: basic '.$key);
                break;
/*
            case 'NTLM':  //DONT WORK PROPERLY, 2010-10-20
                curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM); //for "Server: Microsoft-IIS/5.0"
                curl_setopt($this->ch, CURLOPT_USERPWD, $this->username.':'.$this->password);
                curl_setopt($this->ch, CURLOPT_MAXREDIRS, 3);
                break;
*/
            default:
                throw new \Exception ('unhandled auth method '.$this->auth_method);
            }
        }

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->request_headers);

        if ($this->cookies) {
            if ($this->debug)
                echo 'http->get() sending cookies: '.$this->encodeCookies().ln();
            curl_setopt($this->ch, CURLOPT_COOKIE, $this->encodeCookies());
        }

        if (!empty($post_params)) {
            if ($this->debug)
                echo 'http->post() '.$this->Url->get().' ... ';

            if (is_array($post_params)) {
                $var = http_build_query($post_params);
            } else {
                $var = $post_params;
            }
            if ($this->debug)
                echo 'BODY: '.$var.' ('.strlen($var).' bytes)'.ln();

            curl_setopt($this->ch, CURLOPT_POST, 1);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $var);
        } else {
            if ($this->debug)
                echo 'http->get() '.$this->Url->get().' ... '.ln();
        }

        $res = curl_exec($this->ch);

        if ($this->debug)
            echo 'Got '.strlen($res).' bytes'.ln(); //, showing first 2000:".ln(); d( substr($res,0,2000) );

        $this->parseResponse($res);

        if (!$this->username && empty($post_params) && $this->cache_time && !$head_only) {
            $temp->set($key_head, serialize($this->response_headers), $this->cache_time);
            $temp->set($key_full, $res, $this->cache_time);
        }

        return $this->body;
    }

    /**
     * Parse HTTP response data into object variables and sets status code
     */
    private function parseResponse($res)
    {
        $this->response_headers = array();

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
        if ($this->debug)
            echo 'http->get( '.$this->Url->get().' ) returned HTTP status '.$status.ln();

        switch (substr($status, 0, 9)) {
        case 'HTTP/1.0 ':
        case 'HTTP/1.1 ':
            $this->status_code = intval(substr($status, 9));
            break;
        default:
            throw new \Exception ('unhandled HTTP response '.$status);
        }

        foreach ($headers as $h) {
            $col = explode(': ', $h, 2);

            $name = strtolower($col[0]);
            if (!isset( $this->response_headers[ $name ]))
                $this->response_headers[ $name ] = array();
            $this->response_headers[ $name ][] = $col[1];
            switch ($name) {
/*
            case 'cache-control':
                switch (strtolower($col[1])) {
                case 'no-cache="set-cookie, set-cookie2"':
                    echo "CLEARING COOKIES!!!!\n";
                    $this->cookies = array();
                    break;
                default:
                    echo "XXX UNKNOWN CACHE CONTROL: ".$col[1]."\n";
                    break;
                }
                break;
*/
            case 'set-cookie':
                // store cookies sent from the server in our cookie pool for possible manipulation
//d('SETTING COOKIE: '.$col[1]);
                $this->setCookie( $col[1] );
                break;
            }
        }

        $encoding = $this->getResponseHeader('Content-Encoding');
        switch ($encoding) {
        case 'gzip':
            // strip 10-byte gzip header  XXXX always 10 byte headers???
            $gzhead = substr($body, 0, 10); // 1f8b 0800 0000 0000 0003 ... gzip start with ED FD sequence (???)
            $body = substr($body, 10);
            $body = gzinflate($body);
            break;

        case 'identity':
        case '':
            break;

        default:
            d( $this->response_headers);
            throw new \Exception ('unhandled content-encoding: '.$encoding);
        }

        $this->body = $body;

        $auth = $this->getResponseHeader('WWW-Authenticate');
        if ($auth)
            $this->setAuthMethod($auth);
    }

    /**
     * Parses and returns a section's given value of a HTTP response header
     */
    public static function parseResponseHeader($section, $s)
    {
        $x = explode(';', $s);

        foreach ($x as $part) {
            $y = explode('=', $part);
            if (count($y) == 2) {
                if (strtolower(trim($y[0])) == strtolower($section))
                    return $y[1];
            }
        }

        return false;
    }

}
