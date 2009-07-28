<?php
/**
 * $Id$
 *
 * Get Stock quote for a company symbol
 * http://www.webservicex.net/WCF/ServiceDetails.aspx?SID=19
 */

require_once('input_xml.php');
require_once('class.Cache.php');

define('WEBSERVICEX_STOCK_API', 'http://www.webservicex.net/stockquote.asmx?wsdl');

function webservicex_stock_quote($code)
{
	$client = new SoapClient(WEBSERVICEX_STOCK_API);

	try {
		$cache = new cache();
		$data = $cache->get('stock_'.$code);
		if ($data) return unserialize($data);

		$params['symbol'] = $code;
		$val = $client->GetQuote($params);
		$xml = $val->GetQuoteResult;

		$x = new xml_input();
		$p = $x->parse($xml);

		$res = array(
		'Symbol'       =>$p['StockQuotes|Stock|Symbol'],
		'Name'         =>$p['StockQuotes|Stock|Name'],
		'Last'         =>$p['StockQuotes|Stock|Last'],
		'Time'         =>$p['StockQuotes|Stock|Date'].' '.$p['StockQuotes|Stock|Time'],//FIXME format time & adjust timezone
		'Change'       =>$p['StockQuotes|Stock|Change'],
		'Open'         =>$p['StockQuotes|Stock|Open'],
		'High'         =>$p['StockQuotes|Stock|High'],
		'Low'          =>$p['StockQuotes|Stock|Low'],
		'Volume'       =>$p['StockQuotes|Stock|Volume'],
		'MktCap'       =>$p['StockQuotes|Stock|MktCap'], //XXX ???
		'PreviousClose'=>$p['StockQuotes|Stock|PreviousClose'],
		'PercentChange'=>$p['StockQuotes|Stock|PercentageChange'],
		'AnnRange'     =>$p['StockQuotes|Stock|AnnRange'],//XXX ???
		'Earns'        =>$p['StockQuotes|Stock|Earns'],
		'P-E'          =>$p['StockQuotes|Stock|P-E']//XXX???
		);
		$cache->set('stock_'.$code, serialize($res), 5*60);
		return $res;

	} catch (Exception $e) {
		echo 'exception: '.$e, "\n";
		echo 'Request header:'.$client->__getLastRequestHeaders()."\n";
		echo 'Request: '.$client->__getLastRequest()."\n";
		echo 'Response: '. $client->__getLastResponse()."\n";
		return false;
	}
}

$x = webservicex_stock_quote('AAPL');
print_r($x);



?>
