<?php
/**
 * $Id$
 *
 * Returns current stock quotes
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('service_stock_webservicex.php');
require_once('class.Cache.php');

class StockQuote
{
	var $cache_expire = 300; ///< expire time in seconds for local cache

	function nasdaq($code)
	{
		$code = strtolower_utf8($code);

		$cache = new cache();
		//$cache->debug = true;
		$data = $cache->get('stock_nasdaq_'.$code);
		if ($data) return unserialize($data);

		$res = webservicex_stock_quote($code);
		$cache->set('stock_nasdaq_'.$code, serialize($res), $this->cache_expire);
		return $res;
	}
}

?>
