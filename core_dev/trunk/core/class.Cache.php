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
	var $debug  = false;
	private $handle = false;

	/**
	 * @param $server_pool array of "host[:port]" addresses to memcache servers
	 */
	function __construct($server_pool = false)
	{
		if (!class_exists('Memcache')) {
			echo "cache FAIL: php5-xcache or php5-memcache not found\n";
			return false;
		}

		$this->handle = new Memcache;

		if (!$server_pool) $server_pool = array('127.0.0.1:11211');

		foreach ($server_pool as $server) {

			$ex = explode(':', $server);
			if (empty($ex[1])) $ex[1] = 11211;
			list($host, $port) = $ex;

			$this->handle->addServer($host, $port, true);
		}

		return true;
	}

	function get($key)
	{
		if (!$this->handle) return false;

		$val = $this->handle->get($key);

		if ($this->debug) echo "CACHE READ ".$key." = ".$val."\n";
		return $val;
	}

	function set($key, $val, $expire = 60)
	{
		if (!$this->handle) return false;

		$ret = $this->handle->set($key, $val, false, $expire);

		if ($this->debug) echo "CACHE WRITE ".$key." = ".$val." (".$expire." sec)\n";
		return $ret;
	}
}

?>
