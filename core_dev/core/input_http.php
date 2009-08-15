<?php
/**
 * $Id$
 *
 * Collection of utilities to deal with HTTP requests
 *
 * http://tools.ietf.org/html/rfc2616 - Hypertext Transfer Protocol -- HTTP/1.1
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

$config['http']['user_agent'] = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; sv-SE; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';

require_once('class.Cache.php');

/**
 * Utility class for url manipulation and cached reads
 */
class url_handler
{
	var $scheme, $host, $port, $path, $param;
	var $headers;
	var $cache;
	var $cache_time = 300; //5min
	var $debug = false;

	function __construct($url = '')
	{
		echo phpversion('curl');
		if (!function_exists('curl_exec')) {
			echo "ERROR: php5-curl missing\n";
			//return false;
		}

		if ($url) $this->parse($url);

		$this->cache = new cache();
	}

	function parse($url)
	{
		$parsed = parse_url($url);
		switch ($parsed['scheme']) {
		case 'http':
		case 'https':
		case 'rtmp':
			break;
		default:
			echo "unhandled url scheme ".$parsed['scheme']."\n";
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

	/**
	 * Fetches the data of the web resource
	 */
	function get($cache_time = false)
	{
		$url = $this->compact();

		$data = http_get($url, false, $cache_time);
		$this->headers = http_head($url, $cache_time);

		return $data;
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
function http_get($url, $head_only = false, $cache_time = 60)
{
	global $config;

	if (!is_url($url)) {
		echo $url." is not a valid URL\n";
		return false;
	}

	$cache = new cache();
	$key_head = 'url_head//'.htmlspecialchars($url);
	$key_body = 'url//'.htmlspecialchars($url);

	$u = parse_url($url);

	$ch = curl_init($url);
	if (!$ch) {
		echo "curl error: ".curl_errstr($ch)." (".curl_errno($ch).")\n";
		return false;
	}

	if ($u['scheme'] == 'https') {
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	}

	curl_setopt($ch, CURLOPT_USERAGENT, $config['http']['user_agent']);
	
	if ($head_only) {
		$headers = $cache->get($key_head);
		if ($headers)
			return unserialize($headers);
	} else {
		$body = $cache->get($key_body);
		if ($body)
			return $body;
	}

	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_NOBODY, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

	$result = curl_exec($ch);
	curl_close($ch);

	//Cut off header
	$pos = strpos($result, "\r\n\r\n");
	$result_header = substr($result, 0, $pos);
	$result_body   = substr($result, $pos + strlen("\r\n\r\n"));

	$headers = explode("\r\n", $result_header);
	$cache->set($key_head, serialize($headers), $cache_time);
	$cache->set($key_body, $result_body, $cache_time);

	if ($head_only)
		return $headers;

	return $result_body;
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
	return http_get($url, true, $cache_time);
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
