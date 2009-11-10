<?php
/**
 * $Id$
 *
 * Location object, contains a URI resource
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//STATUS: ok, need testing
//TODO: dont render port 80 for http scheme etc, hide default ports

require_once('network.php');

class Url extends CoreDevBase
{
	private $scheme = 'http';
	private $host;
	private $port;
	private $path;
	private $param  = array();
	private $username, $password; ///< for HTTP AUTH

	function setUsername($username) { $this->username = $username; }
	function setPassword($password) { $this->password = $password; }
	function setPath($path) { $this->path = $path; }

	function getScheme()   { return $this->scheme; }
	function getUsername() { return $this->username; }
	function getPassword() { return $this->password; }

	function __construct($url = '')
	{
		$this->init($url);
	}

	/**
	 * Convert object representation to a string
	 */
	function __toString()
	{
		return $this->get();
	}

	private function init($url = '')
	{
		if (!is_url($url))
			return false;

		$this->set($url);
	}

	/**
	 * Returns a string url of current client location
	 *
	 * @param $safe Outputs URL in a safe format (& => &amp;)
	 */
	function get($safe = false)
	{
		$res = $this->scheme.'://'.$this->host.($this->port ? ':'.$this->port : '').$this->path;

		if (!empty($this->param)) {
			if ($safe)
				$res .= '?'.htmlspecialchars(http_build_query($this->param));
			else
				$res .= '?'.http_build_query($this->param);
		}

		return $res;
	}

	/**
	 * @return default port for protocol $scheme
	 */
	function getDefaultPort($scheme)
	{
		$schemes = array('http'=>80, 'https'=>443, 'rtsp'=>554, 'rtmp'=>1935, 'rtmpe'=>1935, 'mms'=>1755);

		if (empty($schemes[$scheme]))
			return false;

		return $schemes[$scheme];
	}

	/**
	 * Parses url and initializes the object for this url
	 */
	function set($url)
	{
		if (!$url)
			return false;

		$parsed = parse_url($url);

		if (!$parsed['scheme'])
			return false;

		if (empty($parsed['path'])) $parsed['path'] = '/';

		$this->scheme = $parsed['scheme'];
		$this->host   = $parsed['host'];
		$this->path   = $parsed['path'];

		if (!empty($parsed['port']))
			$this->port = $parsed['port'];

		if (!empty($parsed['query']))
			parse_str($parsed['query'], $this->param);

		return true;
	}

	function setScheme($scheme)
	{
		$this->scheme = $scheme;
		return true;
	}

	/**
	 * Adds/sets a parameter to the url
	 */
	function setParam($name, $val = false)
	{
		unset($this->param[$name]);
		$this->param[$name] = $val;
	}

	/**
	 * Removes a parameter from the url
	 */
	function removeParam($name)
	{
		foreach ($this->param as $n=>$val)
			if ($name == $n)
				unset($this->param[$n]);
	}

}

?>
