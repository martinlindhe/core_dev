<?php
/**
 * $Id$
 *
 * Utility class for HTTP GET/POST requests with cache and url manipulation
 *
 * http://tools.ietf.org/html/rfc2616 - Hypertext Transfer Protocol -- HTTP/1.1
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

//STATUS: ok, need testing
//TODO: expose info from curl_getinfo

require_once('core.php');
require_once('network.php');

require_once('prop_Location.php');
require_once('class.Cache.php');

class HttpClient
{
	public  $url;              ///< Location property
	private $debug = false;

	private $headers, $body;

	private $error_code;       ///< return code from http request, such as 404

	private $cache_time = 300; ///< 5 min
	private $user_agent = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.1.4) Gecko/20091028 Ubuntu/9.10 (karmic) Firefox/3.5.4';

	function __construct($url = '')
	{
		if (!function_exists('curl_init')) {
			echo "HttpClient->ERROR: php5-curl missing".dln();
			return false;
		}

		$this->url = new Location($url);
	}

	function getBody()
	{
		$this->get(false);
		return $this->body;
	}

	function getHeaders()
	{
		$this->get(true);
		return $this->headers;
	}

	/**
	 * Returns HTTP status code for the last request
	 */
	function getStatus()
	{
		return $this->status_code;
	}

	/**
	 * @param $s cache time in seconds; max 2592000 (30 days)
	 */
	function setCacheTime($s) { $this->cache_time = $s; }

	function setUserAgent($ua) { $this->user_agent = $ua; }

	function setLocation($s) { $this->url->set($s); }

	function setDebug($bool = true) { $this->debug = $bool; }

	function post($post_params)
	{
		return $this->get(false, $post_params);
	}

	/**
	 * Fetches the data of the web resource
	 * uses HTTP AUTH if username is set
	 */
	private function get($head_only = false, $post_params = array())
	{
		$ch = curl_init( $this->url->get() );
		if (!$ch) {
			echo "curl error: ".curl_errstr($ch)." (".curl_errno($ch).")".dln();
			return false;
		}

		if ($this->url->getScheme() == 'https') {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		}

		if ($this->url->getUsername()) {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $this->url->getUsername().':'.$this->url->getPassword());
		}

		if (!$this->url->getUsername() && empty($post_params)) {
			$cache = new Cache();
			if ($this->debug) $cache->setDebug();
			$key_head = 'url_head//'.htmlspecialchars( $this->url->get() );
			$key_full = 'url//'.     htmlspecialchars( $this->url->get() );

			if ($head_only) {
				$this->headers = unserialize( $cache->get($key_head) );
				if ($this->headers)
					return true;
			} else {
				$full = $cache->get($key_full);
				if ($full) {
					$this->parseResponse($full);
					return true;
				}
			}
		}

		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, $head_only ? 1 : 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		if (!empty($post_params)) {
			if ($this->debug) echo "http->post() ".$this->render()." ... ";

			if (is_array($post_params)) {
				$var = htmlspecialchars(http_build_query($post_params));
			} else {
				$var = $post_params;
			}
			if ($this->debug) echo 'BODY: '.$var.' ('.strlen($var).' bytes)'.dln();

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $var);
		} else {
			if ($this->debug) echo "http->get() ".$this->render()." ... ";
		}

		$res = curl_exec($ch);

		$this->status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($this->debug) echo "http->get() returned HTTP status ".$this->status_code.dln();

		curl_close($ch);

		if ($this->debug) {
			echo "Got ".strlen($res)." bytes, showing first 2000:".dln();
			echo '<pre>'.htmlspecialchars(substr($res,0,2000)).'</pre>';
		}

		$this->parseResponse($res);

		if (!$this->url->getUsername() && empty($post_params)) {
			$cache->setCacheTime($this->cache_time);
			$cache->set($key_head, serialize($this->headers));
			if (!$head_only)
				$cache->set($key_full, $res);
		}

		if ($head_only)
			return $this->headers;

		return $this->body;
	}

	/**
	 * Parse HTTP response data into object variables
	 */
	private function parseResponse($res)
	{
		$pos = strpos($res, "\r\n\r\n");
		if ($pos !== false) {
			$head = substr($res, 0, $pos);
			$this->body    = substr($res, $pos + strlen("\r\n\r\n"));
			$this->headers = explode("\r\n", $head);
		} else {
			$this->body = '';
			$this->headers = explode("\r\n", $res);
		}
	}

}

/**
 * HTTP/HTTPS GET function, using curl
 *
 * @param $head if true, return HTTP HEADer, else the BODY
 */
function http_get($url, $cache_time = 60)
{
	$h = new HttpClient($url);
	$h->setCacheTime($cache_time);
	return $h->getBody();
}

/**
 * Performs a HTTP POST request to the given url
 */
function http_post($url, $data)
{
	$x = new HttpClient($url);
	$x->post($data);

	$res['body']   = $x->getBody();
	$res['header'] = $x->getHeaders();

	return $res;
}

/**
 * Fetches header for given URL and returns it in a parsed array
 *
 * @param $url URL to request HTTP headers for
 * @oaram $cache_time seconds to cache result, 0 to disable
 * @return array of parsed headers or false on error
 */
function http_head($url, $cache_time = 60)
{
	$h = new HttpClient($url);
	$h->setCacheTime($cache_time);
	return $h->head();
}

/**
 * Fetch HTTP status code of given URL
 *
 * @param $p is a URL or an array from http_head()
 * @return HTTP status code, or false if none was found
  */
function http_status($p)
{//XXX update to use the new HttpClient->getStatus() to return HTTP status code
	if (!is_array($p)) $p = http_head($p);

	foreach ($p as $h) {
		switch (substr($h, 0, 9)) {
		case 'HTTP/1.0 ':
		case 'HTTP/1.1 ':
			return intval(substr($h, 9));
		}
	}
	return false;
}

/**
 * Returns the Last-Modified field from given URL
 *
 * @param $p is a URL or an array from http_head()
 * @return a unix timestamp, or false if none was found
 */
function http_last_modified($p)
{
	if (!is_array($p)) $p = http_head($p);

	foreach ($p as $h) {
		$col = explode(': ', $h);
		switch (strtolower($col[0])) {
		case 'last-modified':
			return strtotime($col[1]);
		}
	}
	return false;
}

/**
 * Returns the Content-Length field from given URL
 *
 * @param $p is a URL or an array from http_head()
 * @return content length
 */
function http_content_length($p)
{
	if (!is_array($p)) $p = http_head($p);

	foreach ($p as $h) {
		$col = explode(': ', $h);
		switch (strtolower($col[0])) {
		case 'content-length':
			return intval($col[1]);
		}
	}
	return 0;
}

/**
 * Returns the Content-Type field from given URL
 *
 * @param $p is a URL or an array from http_head()
 * @return content length
 */
function http_content_type($p)
{
	if (!is_array($p)) $p = http_head($p);

	foreach ($p as $h) {
		$col = explode(': ', $h);
		switch (strtolower($col[0])) {
		case 'content-type':
			//text/xml;charset=UTF-8
			$param = explode(';', $col[1]);
			return $param[0];
		}
	}
	return false;
}

/**
 * @enable enable or disable cache headers?
 */
function http_cached_headers($enable = true)
{
	if ($enable) {
		//Tell browser to cache the output for 30 days. Works with MSIE6 and Firefox 1.5
		header('Expires: ' . date("D, j M Y H:i:s", time() + (86400 * 30)) . ' UTC');
		header('Cache-Control: Public');
		header('Pragma: Public');
	} else {
		//Force browser to not cache content
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	}
}

?>
