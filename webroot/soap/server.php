<?

class QuoteService
{
	private $quotes = array('ibm' => 98.42);  

	function getQuote($symbol)
	{
		if (isset($this->quotes[$symbol])) {
			return $this->quotes[$symbol];
		}
		throw new SoapFault('Server', 'Unknown Symbol '.$symbol);
	}
}

$server = new SoapServer('calls.wsdl');
$server->setClass('QuoteService');
$server->handle();

?>