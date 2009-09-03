<?php
/**
 * $Id$
 *
 * HTTP API implementation for Ericsson IPX (Internet Payment Exchange)
 *
 * This currently only implements SMS sending & recieving capabilities
 * (premium SMS and non-premium)
 *
 * References:
 * "Implementation Guide SMS 5.1" from 2007.07.09 (supplied by Ericsson)
 * "Implementation Guide MMS" from 2007.07.09 (supplied by Ericsson)
 * "Implementation Guide, Web & WAP" from 2005.12.09 (supplied by Ericsson)
 *
 * Required PHP extensions: php_soap & php_openssl
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

/**
 * TODO: move all database code out of here!
 * TODO: ability to send mms
 */

require_once('functions_phones.php');

$config['sms']['bulk_username'] = '';
$config['sms']['bulk_password'] = '';

$config['sms']['premium_username'] = '';
$config['sms']['premium_password'] = '';

define('IPX_SOAP_API', 'https://europe.ipx.com/api/services2/SmsApi51?wsdl');	//Updated 2008.05.15

function sendSmsLoggedIn($username, $password, $from_number, $dst_number, $msg, $tariff = '', $reference = '')
{
	global $db;
	$customerId = getCustomerId($username, $password);
	if (!$customerId) return false;

	return sendSMS($customerId, $from_number, $dst_number, $msg, $tariff, $reference);
}

/**
 * Send out a premium or non-premium SMS (MT) SMS.
 *
 * For non-premium SMS, dont set $tariff or $reference
 *
 * @param $creatorId userId of the customer creating the outgoing SMS. used for internal billing
 * @param $dst_number the destination A-Number to send to. please provide country code
 * @param $msg the message to send. max 160 characters
 * @param $tariff currency to charge the a-number, 'SEK2000' = 20 SEK, required for premium MT SMS
 * @param $reference referenceId of a previously incoming SMS, required for premium MT SMS
 * @return returns true upon success, else the status code returned by IPX
 */
function sendSMS($customerId, $from_number, $dst_number, $msg, $tariff = '', $reference = '')
{
	global $db, $config;
	if (!is_numeric($customerId)) return false;

	if (!$tariff) $tariff = 'SEK0';
	if (!$reference) {
		$reference = '#NULL#';
		$ipx_username = $config['sms']['bulk_username'];
		$ipx_password = $config['sms']['bulk_password'];
	} else {
		$ipx_username = $config['sms']['premium_username'];
		$ipx_password = $config['sms']['premium_password'];
	}

	//$msg must be a UTF-8 encoded string for non latin1-characters to work
	$msg = mb_convert_encoding($msg, 'UTF-8', 'auto');

	$dst_number = formatMSID($dst_number);
	if (!$dst_number) return false;

	$client = new SoapClient(IPX_SOAP_API);

	try {
		$q = 'INSERT INTO tblSmsOut SET customerId='.$customerId.',dst="'.$db->escape($dst_number).'",msg="'.$db->escape($msg).'",timeSent=NOW()';
		$corrId = $db->insert($q);
		if (!$corrId) die('FAILED TO INSERT tblSmsOut');

		$params = array(
			//element					value			data type
			'correlationId'			=>	$corrId,		//string	- id som klienten sätter för att hålla reda på requesten, returneras tillsammans med soap-response från IPX
			'originatingAddress'	=>	$from_number,	//string	- orginating number for SMS sent by us
			'destinationAddress'	=>	$dst_number,	//string	- mottagare till sms:et, med landskod, format: 46707308763
			'originatorTON'			=>	(is_numeric($from_number)?'0':'1'),	//0=originatingAddress is number, 1=text, 2=MSISDN
			'userData'				=>	$msg,			//string	- meddelandetexten
			'userDataHeader'		=>	'#NULL#',		//string	- ?
			'DCS'					=>	'-1',			//int		- data coding scheme, how the userData text are encoded
			'PID'					=>	'-1',			//int		- reserved
			'relativeValidityTime'	=>	'-1',			//int		- relative validity time in seconds, from the time of submiussion to IPX
			'deliveryTime'			=>	'#NULL#',		//string	- used for delayed delivery of sms
			'statusReportFlags'		=>	'0',			//int		- 0 = no delivery report, 1 = delivery report requested
			'accountName'			=>	'#NULL#',		//string	- ?
			'tariffClass'			=>	$tariff,		//string	- price of the premium message in the format "SEK0"
			'VAT'					=>	'-1',			//int		- ?
			'referenceId'			=>	$reference,		//string	- reference order of premium message
			'contentCategory'		=>	'#NULL#',		//string
			'contentMetaData'		=>	'#NULL#',		//string
			'username'				=>	$ipx_username,	//string
			'password'				=>	$ipx_password	//string
		);

		$response = $client->send($params);

		$tempErr = ($response->temporaryError ? 1 : 0);	//returned as boolean

		$q = 'UPDATE tblSmsOut SET messageId="'.$db->escape($response->messageId).'",'.
				'responseCode='.$response->responseCode.',responseMessage="'.$db->escape($response->responseMessage).'",'.
				'temporaryError='.$tempErr.',referenceId="'.$db->escape($reference).'",'.
				'debugparams="'.$db->escape(serialize($params)).'", debugresponse="'.$db->escape(serialize($response)).'" '.
				'WHERE correlationId='.$corrId;
		$db->insert($q);

		if ($response->responseCode == 0) {
			if ($reference) return $response->billingStatus;
			return true;
		}
		//return $response->responseCode.' ('.$response->responseMessage.')';
		return false;

	} catch (Exception $e) {
		echo 'Exception! details have been logged<br/>';
		dp('sendSMS() exception: '.$e, LOGLEVEL_ERROR);
		dp('Request header:'.htmlspecialchars($client->__getLastRequestHeaders()) );
		dp('Request: '.htmlspecialchars($client->__getLastRequest()) );
		dp('Response: '. htmlspecialchars($client->__getLastResponse()) );
		return false;
	}
}

/**
 * Sends SMS to multiple reciepents
 *
 * @return number of successfully sent messages
 */
function sendSmsBatch($customerId, $from_number, $dst, $msg)
{
	$dst = normalizeString($dst, array("\n", "\t", ",", ";"));

	$nums = explode(' ', $dst);

	$success = 0;
	foreach ($nums as $num) {
		if (sendSMS($customerId, $from_number, $num, $msg) != false) $success++;
	}

	return $success;
}

//Status codes for incoming SMS in tblSmsIn
define('SMS_IS_OK',	1);	//SMS was handled correctly
define('SMS_INVALID_PARAMS',	2);	//incoming request didnt have all required url parameters set
define('SMS_INVALID_MESSAGE',	3);	//SMS didnt contain a prefix (empty message)
define('SMS_UNKNOWN_PREFIX',	4);	//SMS has a unknown/unhandled prefix and/or SMS with known prefix was sent to wrong ANR

/**
 * Retrieves and handles incoming SMS from the IPX platform
 */
function handleIncomingIPX()
{
	global $db, $config;

	//Acknowledgment - Tell IPX that the SMS was received so they can drop the connection
	header('HTTP/1.1 200 OK');
	header('Content-Type: text/plain');
	echo '<DeliveryResponse ack="true"/>';

	//All incoming data is set as either GET or POST parameters
	$get_params = ''; $get_insert = '';
	$post_params = ''; $post_insert = '';
	if (count($_GET)) { $get_params = $_GET; $get_insert = $db->escape(serialize($get_params)); }
	if (count($_POST)) { $post_params = $_POST; $post_insert = $db->escape(serialize($post_params)); }
	if (!$get_params && !$post_params) die;

	//Log the incoming SMS
	$q = 'INSERT INTO tblSmsIn SET getParams="'.$get_insert.'", postParams="'.$post_insert.'", IP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).', timeReceived=NOW()';
	$entryId = $db->insert($q);

	$params = '';
	if (!empty($post_params)) $params = $post_params;
	if (!empty($get_params)) $params = $get_params;

	if (empty($params['DestinationAddress']) || empty($params['OriginatorAddress']) || empty($params['Message']) ||
		empty($params['MessageId']) || empty($params['TimeStamp']) || empty($params['Operator'])) {
		$q = 'UPDATE tblSmsIn SET status='.SMS_INVALID_PARAMS.' WHERE entryId='.$entryId;
		$db->update($q);
		return false;
	}

	//Identify the incoming SMS prefix
	$arr = explode(' ', $params['Message']);
	if (!$arr) {
		$q = 'UPDATE tblSmsIn SET status='.SMS_INVALID_MESSAGE.' WHERE entryId='.$entryId;
		$db->update($q);
		return false;
	}

	$service = getSmsService($arr[0], $params['DestinationAddress']);
	if (!$service) {
		$q = 'UPDATE tblSmsIn SET status='.SMS_UNKNOWN_PREFIX.' WHERE entryId='.$entryId;
		$db->update($q);
		return false;
	}

	$q = 'UPDATE tblSmsIn SET status='.SMS_IS_OK.',ANR="'.$db->escape($params['OriginatorAddress']).'", serviceId='.$service['serviceId'].',messageId="'.$db->escape($params['MessageId']).'" WHERE entryId='.$entryId;
	$db->update($q);

	$url = $service['URI'].
		'?DestinationAddress='.urlencode($params['DestinationAddress']).
		'&OriginatorAddress='.urlencode($params['OriginatorAddress']).
		'&Message='.urlencode($params['Message']).
		'&MessageId='.urlencode($params['MessageId']).
		'&TimeStamp='.urlencode($params['TimeStamp']).
		'&Operator='.urlencode($params['Operator']);

	//Calls remote URL for identified service and store the result
	$data = file_get_contents($url);
	$q = 'UPDATE tblSmsIn SET result="'.$db->escape($data).'" WHERE entryId='.$entryId;
	$db->update($q);
}
?>
