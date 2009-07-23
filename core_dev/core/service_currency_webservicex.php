<?php
/**
 * $Id$
 *
 * Converts currencies using Currency Convertor web api from
 * http://www.webservicex.net/WS/WSDetails.aspx?CATID=2&WSID=10
 */

define('WEBSERVICEX_CURRENCY_API', 'http://www.webservicex.net/CurrencyConvertor.asmx?WSDL');

function webservicex_currency_conversion_rate($from, $to)
{
	$client = new SoapClient(WEBSERVICEX_CURRENCY_API);

	try {
		$val = $client->ConversionRate( array('FromCurrency'=>strtoupper($from), 'ToCurrency'=>strtoupper($to)) );
		return $val->ConversionRateResult;
	} catch (Exception $e) {
		echo 'exception: '.$e, "\n";
		echo 'Request header:'.$client->__getLastRequestHeaders()."\n";
		echo 'Request: '.$client->__getLastRequest()."\n";
		echo 'Response: '. $client->__getLastResponse()."\n";
		return false;
	}
}

?>
