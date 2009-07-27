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

	function __construct($server = '127.0.0.1', $port = 11211)
	{
		if (!class_exists('Memcache')) return false;

		$this->handle = new Memcache;
		$this->handle->connect($server, $port);
	}

	function get($key)
	{
		if (!$this->handle) return false;
		return $this->handle->get($key);
	}

	function set($key, $val, $expire = 60)
	{
		if (!$this->handle) return false;
		return $this->handle->set($key, $val, false, $expire);
	}
}

?>
