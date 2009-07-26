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

//TODO: if Memcache is not available, use ini-file style in /tmp/coredev_cache

class cache
{
	var $handle;
	function __construct($server = '127.0.0.1', $port = 11211)
	{
		$this->handle = new Memcache;
		$this->handle->connect($server, $port);
	}

	function get($key)
	{
		if ($this->handle->get($key.'_expiretime') < time()) return false;
		echo "return cache\n";
		return $this->handle->get($key);
	}

	function set($key, $val, $expire = 60)
	{
		echo "store\n";
		$this->handle->set($key, $val);
		$this->handle->set($key.'_expiretime', time()+$expire);
	}
}

?>
