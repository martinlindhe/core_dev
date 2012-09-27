<?php
/**
 * $Id$
 *
 * Temporary all-in-one storage singleton that utilises memcached extension
 *
 * @author Martin Lindhe, 2011-2012 <martin@startwars.org>
 */

//STATUS: wip
//PLAN: stabilise impl, move current Cache users to here, eventually deprecate Cache class

namespace cd;

class MemcacheServer
{
    var $host;
    var $port;

    function render()
    {
        return $this->host.':'.$this->port;
    }
}

class TempStore
{
    static    $_instance;            ///< singleton

    protected $handle;               ///< Memcached object
    protected $persistent  = true;   ///< use persistent connections?
    protected $server_pool = array();
    protected $enabled     = true;
    protected $debug       = false;
    protected $connected   = false;
    private   $maxlen      = 300;

    private function __construct()
    {
    }

    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        if (!extension_loaded('memcached'))
            throw new \Exception ('sudo apt-get install php5-memcached');

        return self::$_instance;
    }

    function disable() { $this->enabled = false; }
    function debug($b = true) { $this->debug = $b; }

    /**
     * Registers a server to the internal server pool
     */
    function addServer($host, $port = 11211)
    {
        $serv = new MemcacheServer();
        $serv->host = $host;
        $serv->port = intval($port);
        $this->server_pool[] = $serv;

        $this->handle->addServer($host, $port, $this->persistent);
    }

    /**
     * @param $server_pool array of "host[:port]" addresses to memcache servers
     */
    function addServers($pool)
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
        if ($this->connected)
            return;

        if (!extension_loaded('memcached'))
            throw new \Exception ("Cache FAIL: php5-memcache (php 5.2 or older), or php5-memcached (php 5.3+) not found");

        $this->handle = new \Memcached;

        if (!$this->server_pool)
            $this->addServer('127.0.0.1', 11211);

        $this->connected = true;

        return true;
    }

    function getServerPool()
    {
        $this->connect();

        return $this->server_pool;
    }

    /** @return array of server statistics, one entry per server */
    function getServerStats()
    {
        $this->connect();

        return $this->handle->getStats();
    }

    function get($key)
    {
        if (!$this->enabled)
            return false;

        if (strlen($key) > $this->maxlen)
            throw new \Exception ('Key length too long (len '.strlen($key).', max '.$this->maxlen.'): '.$key);

        $this->connect();

        $key = str_replace(' ', '_', $key);

        $val = $this->handle->get($key);

        if ($this->debug)
            echo 'CACHE READ "'.$key.'"'.($val ? ' HIT' : ' MISS').ln();

        return $val;
    }

    /**
     * @param $expire_time expiration time, in seconds
     */
    function set($key, $val = '', $expire_time = '1h')
    {
        if (strlen($key) > $this->maxlen)
            throw new \Exception ('Key length too long (len '.strlen($key).', max '.$this->maxlen.'): '.$key);

        if (!is_duration($expire_time))
            throw new \Exception ('bad expire time');

        $expire_time = parse_duration($expire_time);

        $this->connect();

        $key = str_replace(' ', '_', $key);

        $ret = $this->handle->set($key, $val, $expire_time);

        if ($this->debug)
            echo 'CACHE WRITE "'.$key.'" = "'.substr($val, 0, 200).'"... ('.$expire_time.' sec)'.ln();

        return $ret;
    }

}

?>
