<?php
/**
 * $Id$
 *
 * Implements a cache using memcached with automatic expire time
 *
 * Requirements: memcached php5-memcache
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//XXX: add "inc/dec" to increase/Decrease numeric values

class cache
{
	var $debug           = false;
	private $handle      = false;
	private $persistent  = true; ///< use persistent connections?
	private $expire_time = 60; ///< expiration time, in seconds

	/**
	 * @param $server_pool array of "host[:port]" addresses to memcache servers
	 */
	function __construct($server_pool = false)
	{
		if (!class_exists('Memcache')) {
			echo "cache FAIL: php5-memcache not found\n";
			return false;
		}

		$this->handle = new Memcache;

		if (!$server_pool) $server_pool = array('127.0.0.1:11211');

		foreach ($server_pool as $server) {

			$ex = explode(':', $server);
			if (empty($ex[1])) $ex[1] = 11211;
			list($host, $port) = $ex;

			$this->handle->addServer($host, $port, $this->persistent);
		}

		return true;
	}

	function setCacheTime($s) { $this->expire_time = $s; }

	function get($key)
	{
		if (!$this->handle) return false;

		$val = $this->handle->get($key);

		if ($this->debug) echo "CACHE READ ".$key." = ".substr($val, 0, 200)."...\n";
		return $val;
	}

	function set($key, $val, $expire = 'REMOVEME') //XXX remove expire parameter
	{
		if (!$this->handle) return false;

		if (is_numeric($expire)) die('FIXME USE $cache->setCacheTime() instead!');

		$ret = $this->handle->set($key, $val, false, $this->expire_time);

		if ($this->debug) echo "CACHE WRITE ".$key." = ".substr($val, 0, 200)."... (".$this->expire_time." sec)\n";
		return $ret;
	}
}

?>
