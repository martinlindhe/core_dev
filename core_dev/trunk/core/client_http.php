<?php
/**
 * $Id$
 *
 * Collection of utilities to deal with HTTP/HTTPS servers
 *
 * http://tools.ietf.org/html/rfc2616 - Hypertext Transfer Protocol -- HTTP/1.1
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

require_once('class.Cache.php');

/**
 * Utility class for url manipulation and cached reads
 */
class http
{
	var $debug = false;
	private $scheme, $host, $port, $path, $param;
	private $username, $password; ///< for HTTP AUTH

	private $user_agent = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.0.13) Gecko/2009080315 Ubuntu/9.04 (jaunty) Firefox/3.0.13';

	private $headers, $body;
	private $cache_time = 300; //5min

	function __construct($url = '')
	{
		if (!function_exists('curl_init')) {
			echo "ERROR: php5-curl missing\n";
			return false;
		}

		if ($url) $this->parse_url($url);
	}

	function getBody() { return $this->body; }
	function getHeaders() { return $this->headers; }

	function parse_url($url)
	{
		$parsed = parse_url($url);
		switch ($parsed['scheme']) {
		case 'http':
		case 'https':
		case 'rtsp':
		case 'rtmp':
			break;
		default:
			echo "unhandled url scheme ".$parsed['scheme'].dln();
			return false;
		}
		$this->scheme = $parsed['scheme'];
		$this->host = $parsed['host'];
		$this->path = $parsed['path'];

		if (!empty($parsed['port']))
			$this->port = $parsed['port'];

		if (!empty($parsed['query']))
			parse_str($parsed['query'], $this->param);
	}

	/**
	 * Adds/sets a parameter to the url
	 */
	function set($name, $val = false)
	{
		unset($this->param[$name]);
		$this->param[$name] = $val;
	}

	function remove($name)
	{
		foreach ($this->param as $n=>$val)
			if ($name == $n)
				unset($this->param[$n]);
	}

	function post($post_params)
	{
		return $this->get(false, $post_params);
	}

	/**
	 * Fetches the data of the web resource
	 * uses HTTP AUTH if username is set
	 */
	function get($head_only = false, $post_params = array())
	{
		$url = $this->compact();
		if (!is_url($url)) {
			echo $url." is not a valid URL".dln();
			return false;
		}

		$ch = curl_init($url);
		if (!$ch) {
			echo "curl error: ".curl_errstr($ch)." (".curl_errno($ch).")".dln();
			return false;
		}

		$u = parse_url($url);
		if ($u['scheme'] == 'https') {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		}

		if ($this->username) {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $this->username.':'.$this->password);
		}

		if (!$this->username && empty($post_params)) {
			$cache = new cache();
			if ($this->debug) $cache->debug = true;
			$key_head = 'url_head//'.htmlspecialchars($url);
			$key_body = 'url//'.htmlspecialchars($url);

			if ($head_only) {
				$headers = $cache->get($key_head);
				if ($headers)
					return unserialize($headers);
			} else {
				$body = $cache->get($key_body);
				if ($body)
					return $body;
			}
		}

		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, $head_only ? 1 : 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		if (!empty($post_params)) {
			if ($this->debug) echo "HTTP POST ".$this->render()." ... ";

			if (is_array($post_params)) {
				$var = htmlspecialchars(http_build_query($post_params));
			} else {
				$var = $post_params;
			}
			if ($this->debug) echo 'BODY: '.$var.' ('.strlen($var).' bytes)'.dln();

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $var);
		} else {
			if ($this->debug) echo "HTTP GET ".$this->render()." ...";
		}
		$res = curl_exec($ch);
		curl_close($ch);
		if ($this->debug) {
			echo "Got ".strlen($res)." bytes:".dln();
			echo '<pre>'.htmlspecialchars($res).'</pre>';
		}

		if (!$head_only) {
			$pos  = strpos($res, "\r\n\r\n");
			$head = substr($res, 0, $pos);
			$this->body    = substr($res, $pos + strlen("\r\n\r\n"));
			$this->headers = explode("\r\n", $head);
		} else {
			$this->body = '';
			$this->headers = explode("\r\n", $res);
		}

		if (!$this->username && empty($post_params)) {
			$cache->set($key_head, serialize($this->headers), $this->cache_time);
			if (!$head_only) $cache->set($key_body, $this->body, $this->cache_time);
		}

		if ($head_only)
			return $this->headers;

		return $this->body;
	}

	function head()
	{
		return $this->get(true);
	}

	/**
	 * Outputs URL in a safe format (& => &amp;)
	 */
	function render()
	{
		$res = $this->scheme.'://'.$this->host.($this->port ? ':'.$this->port : '').$this->path;

		if (!empty($this->param))
			$res .= '?'.htmlspecialchars(http_build_query($this->param));

		return $res;
	}

	/**
	 * Outputs URL in a compact format (& => &)
	 */
	function compact()
	{
		$res = $this->scheme.'://'.$this->host.($this->port ? ':'.$this->port : '').$this->path;

		if (!empty($this->param))
			$res .= '?'.http_build_query($this->param);

		return $res;
	}

}


/**
 * Checks if input string is a valid http or https URL
 *
 * @param $url string
 * @return true if input is a url
 */
function is_url($url)
{
	if (strpos($url, ' ')) return false; //FIXME: the regexp allows spaces in domain name
	$pattern = "(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)";

	if (preg_match($pattern, $url)) return true;
	return false;
}

/**
 * HTTP/HTTPS GET function, using curl
 *
 * @param $head if true, return HTTP HEADer, else the BODY
 */
function http_get($url, $cache_time = 60)
{
	$h = new http($url);
	$h->cache_time = $cache_time;
	return $h->get();
}

/**
 * Performs a HTTP POST request to the given url
 */
function http_post($url, $data)
{
	$x = new http($url);
	$x->post($data);

	$res['body'] = $x->getBody();
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
	$h = new http($url);
	$h->cache_time = $cache_time;
	return $h->head();
}

/**
 * Fetch HTTP status code of given URL
 *
 * @param $p is a URL or an array from http_head()
 * @return HTTP status code, or false if none was found
  */
function http_status($p)
{
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

?>