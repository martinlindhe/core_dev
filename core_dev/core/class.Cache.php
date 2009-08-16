<?php
/**
 * $Id$
 *
 * Implements a cache using memcached with automatic expire time
 *
 * for memcached: "memcached php5-memcache"
 * for xcache: "php5-xcache"
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//XXX: use xcache's cache instead if its available
//XXX: add "inc/dec" to increase/Decrease numeric values

class cache
{
	var $handle = false;
	var $debug  = false;
	var $mode   = '';

	function __construct($server = '127.0.0.1', $port = 11211)
	{
		/*if (function_exists('xcache_get')) {
			$this->mode = 'xcache';
			return true;
		} else */if (class_exists('Memcache')) {
			$this->mode = 'memcache';
			$this->handle = new Memcache;
			$this->handle->connect($server, $port);
			return true;
		}
		echo "cache FAIL: php5-xcache or php5-memcache not found\n";
		return false;
	}

	function get($key)
	{
		if ($this->mode == 'xcache') {
			$val = xcache_get($key); //XXX i never get data returned when testing 2009.08.15
		} else {
			if (!$this->handle) return false;

			$val = $this->handle->get($key);
		}
		if ($this->debug) echo "CACHE READ ".$key." = ".$val."\n";

		return $val;
	}

	function set($key, $val, $expire = 60)
	{
		if ($this->mode == 'xcache') {
			$ret = xcache_set($key, $val, $expire);
		} else {
			if (!$this->handle) return false;

			$ret = $this->handle->set($key, $val, false, $expire);
		}
		if ($this->debug) echo "CACHE WRITE ".$key." = ".$val." (".$expire." sec)\n";
		return $ret;
	}
}

?>
