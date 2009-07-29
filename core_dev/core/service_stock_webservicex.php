<?php
/**
 * $Id$
 *
 * Get Stock quote for a company symbol
 * http://www.webservicex.net/WCF/ServiceDetails.aspx?SID=19
 */

require_once('functions_defaults.php');
require_once('input_xml.php');

define('WEBSERVICEX_STOCK_API', 'http://www.webservicex.net/stockquote.asmx?wsdl');

function webservicex_stock_quote($code)
{
	$client = new SoapClient(WEBSERVICEX_STOCK_API);

	try {
		$params['symbol'] = $code;
		$val = $client->GetQuote($params);
		$xml = $val->GetQuoteResult;

		$x = new xml_input();
		$p = $x->parse($xml);

		//FIXME adjust timezone of timestamp
		$timestamp = strtotime($p['StockQuotes|Stock|Date'].' '.$p['StockQuotes|Stock|Time']);

		$res = array(
		'Symbol'       =>$p['StockQuotes|Stock|Symbol'],
		'Name'         =>$p['StockQuotes|Stock|Name'],
		'Last'         =>$p['StockQuotes|Stock|Last'],
		'Time'         =>formatTime($timestamp),
		'Timestamp'    =>$timestamp,
		'Change'       =>$p['StockQuotes|Stock|Change'],
		'Open'         =>$p['StockQuotes|Stock|Open'],
		'High'         =>$p['StockQuotes|Stock|High'],
		'Low'          =>$p['StockQuotes|Stock|Low'],
		'Volume'       =>$p['StockQuotes|Stock|Volume'],
		'PreviousClose'=>$p['StockQuotes|Stock|PreviousClose'],
		'PercentChange'=>$p['StockQuotes|Stock|PercentageChange'],
		'Earns'        =>$p['StockQuotes|Stock|Earns'],
		'MktCap'       =>$p['StockQuotes|Stock|MktCap'], //XXX ???
		'AnnRange'     =>$p['StockQuotes|Stock|AnnRange'],//XXX ???
		'P-E'          =>$p['StockQuotes|Stock|P-E']//XXX???
		);
		return $res;

	} catch (Exception $e) {
		echo 'exception: '.$e, "\n";
		echo 'Request header:'.$client->__getLastRequestHeaders()."\n";
		echo 'Request: '.$client->__getLastRequest()."\n";
		echo 'Response: '. $client->__getLastResponse()."\n";
		return false;
	}
}

?>
