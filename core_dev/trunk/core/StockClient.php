<?php
/**
 * $Id$
 *
 * Returns current stock quotes
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('StockClientWebservicex.php'); //NASDAQ only (?)

require_once('Cache.php');

class StockClient
{
    private $cache_expire = 300; ///< expire time in seconds for local cache
    private $cache; ///< Cache object

    function __construct()
    {
        $this->cache = new Cache();
        $this->setCacheTime($this->cache_expire);
    }

    function setCacheTime($s) { $this->cache->setCacheTime($s); }

    function getNasdaq($code)
    {
        $code = strtolower($code);

        $data = $this->cache->get('stock_nasdaq//'.$code);
        if ($data) return unserialize($data);

        $client = new StockClientWebservicex();

        $res = $client->getQuote($code);

        if ($res)
            $this->cache->set('stock_nasdaq//'.$code, serialize($res));

        return $res;
    }
}

?>
