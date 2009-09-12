<?php
/**
 * $Id$
 *
 * Returns current stock quotes
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('client_stock_webservicex.php'); //NASDAQ only (?)

require_once('class.Cache.php');

class Stock
{
	var $cache_expire = 300; ///< expire time in seconds for local cache

	function getNasdaq($code)
	{
		$code = strtolower($code);

		$cache = new cache();
		$cache->setCacheTime($this->cache_expire);
		//$cache->debug = true;

		$data = $cache->get('stock_nasdaq_'.$code);
		if ($data) return unserialize($data);

		$client = new Stock_webservicex();

		$res = $client->getQuote($code);

		$cache->set('stock_nasdaq_'.$code, serialize($res));
		return $res;
	}
}

?>
