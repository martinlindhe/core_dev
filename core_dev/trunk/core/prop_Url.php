<?php
/**
 * $Id$
 *
 * Location object, contains a URI resource
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//STATUS: good

require_once('class.CoreProperty.php');
require_once('network.php'); //for is_url(), scheme_default_port()

class Url extends CoreProperty
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

	/**
	 * @param $safe outputs URL in a safe format (& => &amp;)
	 * @return the url as a string
	 */
	function get($safe = false)
	{
		$port = '';
		if ($this->port && scheme_default_port($this->scheme) != $this->port)
			$port = ':'.$this->port;

		$res = $this->scheme.'://'.$this->host.$port.$this->path;

		if (!empty($this->param)) {
			if ($safe)
				$res .= '?'.htmlspecialchars(http_build_query($this->param));
			else
				$res .= '?'.http_build_query($this->param);
		}

		return $res;
	}

	/**
	 * @param $text string to substitute location with
	 * @return the url as a html tag
	 */
	function asHtml($text = '')
	{
		if (!$text)
			$text = $this->get();

		return '<a href="'.$this->get().'">'.$text.'</a>';
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
