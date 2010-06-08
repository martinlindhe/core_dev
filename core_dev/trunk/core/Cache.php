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

//STATUS: wip

//TODO: remove isActive(), add isConnected() ??
//TODO: rename setCacheTime -> setTimeout

require_once('core.php');
require_once('class.CoreBase.php');

class Cache extends CoreBase
{
    private $handle      = false;
    private $persistent  = true;   ///< use persistent connections?
    private $expire_time = 0;      ///< expiration time, in seconds
    private $driver      = 'memcache';
    private $server_pool = array();
    private $connected   = false;  ///< memcache server connection open?

    /**
     * @param $server_pool array of "host[:port]" addresses to memcache servers
     */
    function addServer($host, $port = 11211)
    {
        $this->server_pool[] = array('host' => $host, 'port' => intval($port) );
    }

    /**
     * @param $server_pool array of "host[:port]" addresses to memcache servers
     */
    function addServerPool($pool)
    {
        foreach ($pool as $server)
        {
            $ex = explode(':', $server);
            if (empty($ex[1])) $ex[1] = 11211;
            list($host, $port) = $ex;

            $this->addServer($host, $port);
        }
    }

    private function connect()
    {
        if (!$this->expire_time)
            return false;

        if ($this->connected)
            return true;

        if (extension_loaded('memcached')) {
            //php5-memcached for php 5.3 or newer
            $this->driver = 'memcached';
            $this->handle = new Memcached;
        } else if (extension_loaded('memcache')) {
            //php5-memcache for php 5.2 or older
            $this->driver = 'memcache';
            $this->handle = new Memcache;
        } else
            throw new Exception ("Cache FAIL: php5-memcache (php 5.2 or older), or php5-memcached (php 5.3+) not found");

        if (!$this->server_pool)
            $this->addServer('127.0.0.1');

        foreach ($this->server_pool as $server)
            $this->handle->addServer($server['host'], $server['port'], $this->persistent);

        $this->connected = true;

        return true;
    }

    /**
     * @return true if cache is active
     */
    function isActive() { return $this->expire_time ? true : false; }

    /**
     * @param $s cache time in seconds; max 2592000 (30 days)
     */
    function setTimeout($s) { $this->expire_time = $s; }
    function setCacheTime($s) { $this->setTimeout($s); } ///XXX DEPRECATE

    function getServerPool() { return $this->server_pool; }

    function get($key)
    {
        if (strlen($key) > 250)
            throw new Exception ('Key length too long '.$key);

        if (!$this->connect())
            return false;

        $val = $this->handle->get($key);

        if ($this->getDebug()) {
            echo "CACHE READ ".$key;
            echo ($val ? ' HIT' : ' MISS').ln();
        }
        return $val;
    }

    function set($key, $val = '')
    {
        if (strlen($key) > 250)
            throw new Exception ('Key length too long');

        if (!$val)
            return $this->delete($key);

        if (!$this->connect())
            return false;

        if ($this->driver == 'memcache') {
            //XXX HACK force quiet bogus warnings from memcache in 2009
            $ret = @$this->handle->set($key, $val, false, $this->expire_time);
        } else {
            $ret = $this->handle->set($key, $val, $this->expire_time);
        }

        if ($this->getDebug()) echo "CACHE WRITE ".$key." = ".substr($val, 0, 200)."... (".$this->expire_time." sec)".ln();
        return $ret;
    }

    function delete($key)
    {
        if (strlen($key) > 250)
            throw new Exception ('Key length too long');

        if (!$this->connect())
            return false;

        $ret = $this->handle->delete($key);
        if ($this->getDebug()) echo "CACHE DELETE ".$key.ln();

        return $ret;
    }
}

?>
