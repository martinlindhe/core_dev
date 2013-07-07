<?php
/**
 * $Id$
 */

namespace cd;

require_once('ITempStore.php');

class TempStoreRedis implements ITempStore
{
    protected $redis;
    protected $connected = false;
    protected $enabled = true;
    protected $maxlen = 300; ///< do redis have a limit?
    var       $debug = true;

    var       $host = '127.0.0.1';
    var       $port = 6379;

    public function setServer($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    private function connect()
    {
        if ($this->connected)
            return true;

        if (!extension_loaded('redis'))
            throw new \Exception ('apt-get install php5-redis');

        $this->redis = new \Redis();

        $this->connected = $this->redis->connect($this->host, $this->port);
        if (!$this->connected)
            throw new \Exception ('connection failed');

        return $this->connected;
    }


    /** @return array of server statistics, one entry per server */
    public function getServerStats()
    {
        $this->connect();

        return $this->redis->info();
    }

    public function get($key)
    {
        if (!$this->enabled)
            return false;

        if (strlen($key) > $this->maxlen)
            throw new \Exception ('Key length too long (len '.strlen($key).', max '.$this->maxlen.'): '.$key);

        $this->connect();

 //       $key = str_replace(' ', '_', $key);

        $val = $this->redis->get($key);

        if ($this->debug)
            echo 'TempStoreRedis GET "'.$key.'"'.($val ? ' HIT' : ' MISS').ln();

        return $val;
    }

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

 //       $key = str_replace(' ', '_', $key);

        $ret = $this->redis->setex($key, $expire_time, $val);

        if ($this->debug)
            echo 'TempStoreRedis SET "'.$key.'" = "'.substr($val, 0, 200).'"... ('.$expire_time.' sec)'.ln();
/*
        if (!$ret)
            throw new \Exception ('SET failed');
*/
        return $ret;
    }
}
