<?php
/**
 * $Id$
 *
 * Http Client class to GET/POST data using the http protocol
 *
 * http://tools.ietf.org/html/rfc2616 - Hypertext Transfer Protocol -- HTTP/1.1
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

//STATUS: good

require_once('core.php');
require_once('network.php');

require_once('prop_Url.php');
require_once('class.Cache.php');

class HttpClient extends CoreBase
{
	public  $Url;              ///< Url property
	private $headers, $body;
	private $status_code;      ///< return code from http request, such as 404
	private $cache_time = 0;   ///< in seconds
	private $user_agent = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.1.4) Gecko/20091028 Ubuntu/9.10 (karmic) Firefox/3.5.4';

	function __construct($url = '')
	{
		if (!function_exists('curl_init')) {
			echo "HttpClient->ERROR: php5-curl missing".ln();
			return false;
		}

		$this->Url = new Url($url);
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

	function getHeader($name)
	{
		$name = strtolower($name);

		if (isset($this->headers[ $name ]))
			return $this->headers[$name];

		return false;
	}

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

	function setUrl($s) { $this->Url->set($s); }

	function setUsername($s) { $this->Url->setUsername($s); }
	function setPassword($s) { $this->Url->setPassword($s); }

	function post($params)
	{
		return $this->get(false, $params);
	}

	/**
	 * Fetches the data of the web resource
	 * uses HTTP AUTH if username is set
	 */
	private function get($head_only = false, $post_params = array())
	{
		$ch = curl_init( $this->Url->get() );
		if (!$ch) {
			echo "curl error: ".curl_errstr($ch)." (".curl_errno($ch).")".ln();
			return false;
		}

		if ($this->Url->getScheme() == 'https') {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		}

		if ($this->Url->getUsername()) {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $this->Url->getUsername().':'.$this->Url->getPassword());
		}

		if (!$this->Url->getUsername() && empty($post_params)) {
			$cache = new Cache();
			$cache->setCacheTime($this->cache_time);
			if ($this->getDebug()) $cache->setDebug();
			$key_head = 'url_head//'.htmlspecialchars( $this->Url->get() );
			$key_full = 'url//'.     htmlspecialchars( $this->Url->get() );

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
//			if ($this->getDebug()) echo "http->post() ".$this->render()." ... ";

			if (is_array($post_params)) {
				$var = htmlspecialchars(http_build_query($post_params));
			} else {
				$var = $post_params;
			}
			if ($this->getDebug()) echo 'BODY: '.$var.' ('.strlen($var).' bytes)'.ln();

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $var);
		} else {
			if ($this->getDebug()) echo "http->get() ".$this->Url->get()." ... ".ln();
		}

		$res = curl_exec($ch);

		curl_close($ch);

		if ($this->getDebug()) {
			echo "Got ".strlen($res)." bytes, showing first 2000:".ln();
			echo '<pre>'.htmlspecialchars(substr($res,0,2000)).'</pre>';
		}

		$this->parseResponse($res);

		if (!$this->Url->getUsername() && empty($post_params)) {
			$cache->set($key_head, serialize($this->headers));
			if (!$head_only)
				$cache->set($key_full, $res);
		}

		if ($head_only)
			return $this->headers;

		return $this->body;
	}

	/**
	 * Parse HTTP response data into object variables and sets status code
	 */

	private function parseResponse($res)
	{
		$pos = strpos($res, "\r\n\r\n");
		if ($pos !== false) {
			$head = substr($res, 0, $pos);
			$this->body = substr($res, $pos + strlen("\r\n\r\n"));
			$headers = explode("\r\n", $head);
		} else {
			$this->body = '';
			$headers = explode("\r\n", $res);
		}

		$status = array_shift($headers);
		if ($this->getDebug()) echo "http->get() returned HTTP status ".$status.ln();

		switch (substr($status, 0, 9)) {
		case 'HTTP/1.0 ':
		case 'HTTP/1.1 ':
			$this->status_code = intval(substr($status, 9));
			break;
		}

		$this->headers = array();
		foreach ($headers as $h) {
			$col = explode(': ', $h, 2);
			$this->headers[ strtolower($col[0]) ] = $col[1];
		}
	}

}

?>
