<?php
/**
 * $Id$
 *
 * Temporary all-in-one storage singleton that utilises memcached extension
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip
//PLAN: stabilise impl, move current Cache users to here, eventually deprecate Cache class

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

    protected $handle;
    protected $persistent  = true;   ///< use persistent connections?
    protected $server_pool = array();
    protected $enabled     = true;
    protected $debug       = false;

    private function __construct()
    {
        $this->handle = new Memcached();
    }

    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        if (!extension_loaded('memcached'))
            throw new Exception ('sudo apt-get install php5-memcached');

        return self::$_instance;
    }

    function disable() { $this->enabled = false; }

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
        if (!$this->server_pool)
            $this->addServer('127.0.0.1', 11211);

        return true;
    }

    function setDebug($b) { $this->debug = $b; }

    function getServerPool()
    {
        $this->connect();

        return $this->server_pool;
    }

    function getServerStats()
    {
        $this->connect();
        return $this->handle->getStats();
    }

    function get($key)
    {
        if (!$this->enabled)
            return false;

        if (strlen($key) > 250)
            throw new Exception ('Key length too long '.$key);

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
        if (strlen($key) > 250)
            throw new Exception ('Key length too long');

        if (!is_duration($expire_time))
            throw new Exception ('bad expire time');

        $expire_time = parse_duration($expire_time);

        $this->connect();

        $key = str_replace(' ', '_', $key);

        if (!$val)
            return $this->delete($key);

        $ret = $this->handle->set($key, $val, $expire_time);

        if ($this->debug)
            echo 'CACHE WRITE "'.$key.'" = "'.substr($val, 0, 200).'"... ('.$expire_time.' sec)'.ln();

        return $ret;
    }

    /**
     * Shows the cache status view
     */
    public function renderStatus()
    {
        $view = new ViewModel('views/TempStore_status.php');
        return $view->render();
    }

}

?>
