<?php
/**
 * $Id$
 *
 * Temporary all-in-one storage singleton that utilises memcached extension
 *
 * @author Martin Lindhe, 2011-2013 <martin@startwars.org>
 */

//STATUS: wip

// TODO later: expose server pool ability (again, was removed because it was not used)

//TODO: detect when no memcache server actually exist. currently it will just act as no cache result was found

//PLAN: stabilize impl, move current Cache users to here, deprecate Cache class

namespace cd;

require_once('ITempStore.php');

class TempStoreMemcached implements ITempStore
{
    protected $memcached;
    protected $enabled     = true;
    protected $debug       = true;
    protected $connected   = false;
    private   $maxlen      = 300;
    var       $host        = '127.0.0.1';
    var       $port        = 11211;

    public function disable() { $this->enabled = false; }
    public function debug($b = true) { $this->debug = $b; }

    /**
     * Sets one server as the valid one
     */
    public function setServer($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    private function connect()
    {
        if ($this->connected)
            return true;

        if (!extension_loaded('memcached'))
            throw new \Exception ('install php5-memcached');

        $this->memcached = new \Memcached();

        if (!$this->memcached->addServer($this->host, $this->port))
            throw new \Exception ('failed to add server');

        $this->connected = true;

        return true;
    }

    /** @return array of server statistics, one entry per server */
    public function getServerStats()
    {
        $this->connect();

        return $this->memcached->getStats();
    }

    public function get($key)
    {
        if (!$this->enabled)
            return false;

        if (strlen($key) > $this->maxlen)
            throw new \Exception ('Key length too long (len '.strlen($key).', max '.$this->maxlen.'): '.$key);

        $this->connect();

//        $key = str_replace(' ', '_', $key);

        $val = $this->memcached->get($key);

        if ($this->debug)
            echo 'TempStoreMemcached GET "'.$key.'"'.($val ? ' HIT' : ' MISS').ln();

        return $val;
    }

    /**
     * @param $expire_time expiration time, in seconds
     */
    public function set($key, $val = '', $expire_time = 3600)
    {
        if (strlen($key) > $this->maxlen)
            throw new \Exception ('Key length too long (len '.strlen($key).', max '.$this->maxlen.'): '.$key);

        if ($expire_time) {
            if (!is_duration($expire_time))
                throw new \Exception ('bad expire time');

            $expire_time = parse_duration($expire_time);
        }

        $this->connect();

//        $key = str_replace(' ', '_', $key);

        $ret = $this->memcached->set($key, $val, $expire_time);

        if ($this->debug)
            echo 'TempStoreMemcached SET "'.$key.'" = "'.substr($val, 0, 200).'"... ('.$expire_time.' sec)'.ln();
/*
        if (!$ret)
            throw new \Exception ('SET failed');
*/
        return $ret;
    }

}
