<?php
/**
 * $Id$
 *
 * Implements a cache using memcached with automatic expire time
 *
 * sudo aptitude install memcached php5-memcache
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

class cache
{
	var $handle = false;
	var $debug  = false;

	function __construct($server = '127.0.0.1', $port = 11211)
	{
		if (!class_exists('Memcache')) {
			echo "ERROR: php5-memcache missing\n";
			return false;
		}

		$this->handle = new Memcache;
		$this->handle->connect($server, $port);
	}

	function get($key)
	{
		if (!$this->handle) return false;

		$val = $this->handle->get($key);

		if ($this->debug) echo "MEMCACHE READ ".$key." = ".$val."\n";

		return $val;
	}

	function set($key, $val, $expire = 60)
	{
		if (!$this->handle) return false;

		if ($this->debug) echo "MEMCACHE WRITE ".$key." = ".$val." (".$expire." sec)\n";

		$ret = $this->handle->set($key, $val, false, $expire);

		return $ret;
	}
}

?>
