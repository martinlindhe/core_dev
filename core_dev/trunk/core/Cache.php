<?php
/**
 * $Id$
 *
 * Implements a cache using memcached with automatic expire time
 *
 * Requirements: memcached php5-memcache (php 5.2 or older), or php5-memcached (php 5.3 or newer)
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip
//PLAN. migrate users over to TempStore, eventually drop Cache class

//TODO: remove isActive(), add isConnected() ??
//TODO: rename setCacheTime -> setTimeout
//TODO later: drop 'memcache' extension support

require_once('core.php');
require_once('class.CoreBase.php');

class Cache extends CoreBase
{
    protected $handle      = false;
    protected $persistent  = true;   ///< use persistent connections?
    protected $expire_time;          ///< expiration time, in seconds
    protected $driver_name;
    protected $connected   = false;  ///< memcache server connection open?

    private function connect()
    {
        if (!$this->expire_time)
            return false;

        if ($this->connected)
            return true;

        if (extension_loaded('memcached')) {
            //php5-memcached for php 5.3 or newer
            $this->driver_name = 'memcached';
            $this->handle = new Memcached;
        } else if (extension_loaded('memcache')) {
            //php5-memcache for php 5.2 or older
            $this->driver_name = 'memcache';
            $this->handle = new Memcache;
        } else
            throw new Exception ("Cache FAIL: php5-memcache (php 5.2 or older), or php5-memcached (php 5.3+) not found");

        $this->handle->addServer('127.0.0.1', 11211);

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
    function setTimeout($s) { $this->expire_time = parse_duration($s); }
    function setCacheTime($s) { $this->setTimeout($s); }

    function get($key)
    {
        if (strlen($key) > 250)
            throw new Exception ('Key length too long '.$key);

        if (!$this->connect())
            return false;

        $key = str_replace(' ', '_', $key);

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

        if (!$this->connect())
            return false;

        $key = str_replace(' ', '_', $key);

        if (!$val)
            return $this->delete($key);

        if ($this->driver_name == 'memcache') {
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

        $key = str_replace(' ', '_', $key);

        $ret = $this->handle->delete($key);
        if ($this->getDebug()) echo "CACHE DELETE ".$key.ln();

        return $ret;
    }

}

?>
