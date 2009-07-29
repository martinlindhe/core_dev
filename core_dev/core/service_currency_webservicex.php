<?php
/**
 * $Id$
 *
 * Converts currencies using Currency Convertor web api from
 * http://www.webservicex.net/WS/WSDetails.aspx?CATID=2&WSID=10
 */

require_once('class.Cache.php');

define('WEBSERVICEX_CURRENCY_API', 'http://www.webservicex.net/CurrencyConvertor.asmx?WSDL');

function webservicex_currency_conversion_rate($from, $to)
{
	$client = new SoapClient(WEBSERVICEX_CURRENCY_API);

	try {
		$params['FromCurrency'] = strtoupper($from);
		$params['ToCurrency']   = strtoupper($to);

		$rate = $client->ConversionRate($params);
		return $rate->ConversionRateResult;

	} catch (Exception $e) {
		echo 'exception: '.$e, "\n";
		echo 'Request header:'.$client->__getLastRequestHeaders()."\n";
		echo 'Request: '.$client->__getLastRequest()."\n";
		echo 'Response: '. $client->__getLastResponse()."\n";
		return false;
	}
}

?>
