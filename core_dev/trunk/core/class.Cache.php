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

require_once('class.CoreBase.php');

class Cache extends CoreBase
{
    private $handle      = false;
    private $persistent  = true;  ///< use persistent connections?
    private $expire_time = 60;    ///< expiration time, in seconds
    private $driver      = 'memcache';

    /**
     * @param $server_pool array of "host[:port]" addresses to memcache servers
     */
    function __construct($server_pool = false)
    {
        if (class_exists('Memcached')) {
            //php5-memcached for php 5.3 or newer
            $this->driver = 'memcached';
            $this->handle = new Memcached;
        } else if (class_exists('Memcache')) {
            //php5-memcache for php 5.2 or older
            $this->driver = 'memcache';
            $this->handle = new Memcache;
        } else {
            dp("Cache FAIL: php5-memcache (php 5.2 or older), or php5-memcached (php 5.3+) not found");
            return false;
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

        if ($this->getDebug()) {
            echo "CACHE READ ".$key;
            echo ($val ? ' HIT' : ' MISS').ln();
        }
        return $val;
    }

    function set($key, $val)
    {
        if (!$this->handle || !$this->expire_time) return false;

        if ($this->driver == 'memcache') {
            //XXX HACK force quiet bogus warnings from memcache in 2009
            $ret = @$this->handle->set($key, $val, false, $this->expire_time);
        } else {
            $ret = $this->handle->set($key, $val, $this->expire_time);
        }

        if ($this->getDebug()) echo "CACHE WRITE ".$key." = ".substr($val, 0, 200)."... (".$this->expire_time." sec)".ln();
        return $ret;
    }
}

?>
