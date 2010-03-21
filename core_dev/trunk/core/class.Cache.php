<?php
/**
 * $Id$
 *
 * Implements a cache using memcached with automatic expire time
 *
 * Requirements: memcached php5-memcache (php 5.2 or older), or php5-memcached (php 5.3 or newer)
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: good
//TODO: add "inc/dec" to increase/Decrease numeric values

require_once('class.CoreBase.php');

class Cache extends CoreBase
{
	private $handle      = false;
	private $persistent  = true;  ///< use persistent connections?
	private $expire_time = 60;    ///< expiration time, in seconds

	/**
	 * @param $server_pool array of "host[:port]" addresses to memcache servers
	 */
	function __construct($server_pool = false)
	{
	    //php5-memcache for php 5.2 or older
        if (PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION < 3 && !class_exists('Memcache')) {
			dp("Cache FAIL: php5-memcache not found");
			return false;
		}

        //php5-memcached for php 5.3 or newer
        if (PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION >= 3 && !class_exists('Memcached')) {
			dp("Cache FAIL: php5-memcached not found");
			return false;
		}

        if (class_exists('Memcached')) {
    		$this->handle = new Memcached;
    	} else {
            $this->handle = new Memcache;
    	}

		if (!$server_pool) $server_pool = array('127.0.0.1:11211');

		foreach ($server_pool as $server) {

			$ex = explode(':', $server);
			if (empty($ex[1])) $ex[1] = 11211;
			list($host, $port) = $ex;

			$this->handle->addServer($host, $port, $this->persistent);
		}

		return true;
	}

    /**
     * @return true if cache is active
     */
	function isActive() { return ($this->handle && $this->expire_time) ? true : false; }

	/**
	 * @param $s cache time in seconds; max 2592000 (30 days)
	 */
	function setCacheTime($s) { $this->expire_time = $s; }

	function get($key)
	{
		if (!$this->handle || !$this->expire_time) return false;

		$val = $this->handle->get($key);

		if ($this->getDebug() && $val) echo "CACHE READ ".$key.ln();
		return $val;
	}

	function set($key, $val)
	{
		if (!$this->handle || !$this->expire_time) return false;

		//XXX HACK force quiet bogus warnings from memcache in 2009
		$ret = @$this->handle->set($key, $val, false, $this->expire_time);

		if ($this->getDebug()) echo "CACHE WRITE ".$key." = ".substr($val, 0, 200)."... (".$this->expire_time." sec)".ln();
		return $ret;
	}
}

?>
