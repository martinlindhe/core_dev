<?php
/**
 * $Id$
 *
 * Functions to interact with useful-networks.com Poisitioning API
 *
 * Coordinates is in WGS84 format
 *
 * Implemented using "LBS Location Services API Document v1.12"
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//QUESTION: what is expire time of a "active_token"?
//QUESTION: docs lists carrier names in upper case but they are not accepted in upper case?
//QUESTION: why is MDN & Sprint in the response xml's rather than correct info?

//TODO: implement function to handle /location/audit


require_once('output_http.php');

class un_request
{
	var $app_id, $user, $pass, $base_url; ///< auth credentials

	var $active_token = '';  ///< set with active token if authenticated
	var $response_code = ''; ///< holds the response code of last server response

	/**
	 * Parses XML response data from authentication request
	 */
	function parse_auth_response($data)
	{
		$parser = xml_parser_create('ISO-8859-1');
		xml_parse_into_struct($parser, $data, $data_vals, $data_idx);
		xml_parser_free($parser);

		foreach ($data_vals as $idx=>$val)
		{
			if ($val['tag'] == 'UNRESPONSECODE') $this->response_code = $val['value'];
			if ($val['tag'] == 'ACTIVETOKEN')    $this->active_token  = $val['value'];
		}
	}

	/**
	 * Requests a session token that is valid for a limited time
	 * subsequent requests need to use this token, or request a new
	 * one if it is expired
	 *
	 * @return a valid UN session token
	 */
	function auth($url, $app_id, $user, $pass)
	{
		$this->base_url = $url;

		$uri = $this->base_url.'/application/authenticate';

		$this->app_id = $app_id;
		$this->user = $user;
		$this->pass = $pass;

		$tid = 666; //echoed back

		$x =
		'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
		'<unRequest>'.
			'<authenticationRequest>'.
				'<applicationId>'.$this->app_id.'</applicationId>'.
				'<applicationUser>'.$this->user.'</applicationUser>'.
				'<applicationPassword>'.$this->pass.'</applicationPassword>'.
				'<clientTransactionId>'.$tid.'</clientTransactionId>'.
			'</authenticationRequest>'.
		'</unRequest>';

		$res = http_post($uri, $x);

		$this->parse_auth_response($res['body']);

		if ($this->response_code != 'OK') {
			echo "FATAL: authentication failed!\n";
			return false;
		}
		return true;
	}

	/**
	 * Performs a syncronous (blocking) MSID position request
	 *
	 * @return array with coordinates & other details
	 */
	function pos_sync($msid, $carrier)
	{
		return $this->pos($msid, $carrier, true);
	}

	/**
	 * Performs a asyncronous (non-blocking) MSID position request
	 *
	 * @return transaction id for the position request
	 */
	function pos_async($msid, $carrier)
	{
		$data = $this->pos($msid, $carrier, false);
		return $data['transaction_id'];
	}

	/**
	 * Polls for update of status for async positioning request
	 *
	 * @param $transaction_id transaction id from previous $un->pos_async() request
	 * @return false if not ready, else it returns the data
	 */
	function pos_async_poll($transaction_id)
	{
		$uri = $this->base_url.'/location/poll';

		$tid = 1234;

		$x =
		'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
		'<unRequest>'.
			'<pollRequest>'.
				'<requestHeader>'.
					'<activeToken>'.$this->active_token.'</activeToken>'.
					'<clientTransactionId>'.$tid.'</clientTransactionId>'.
				'</requestHeader>'.
				'<transactionId>'.$transaction_id.'</transactionId>'.
			'</pollRequest>'.
		'</unRequest>';

		$res = http_post($uri, $x);

		$parsed = $this->parse_loc_response($res['body']);
		if ($parsed['status_code'] == 'PENDING_LOCATION_RESPONSE') return false;
		return $parsed;
	}

	/**
	 * Performs a MSID position request
	 *
	 * @param $msid
	 * @param $carrier
	 * @param $sync false for async positioning
	 */
	function pos($msid, $carrier, $sync = 'true')
	{
		if ($sync) {
			$uri = $this->base_url.'/location/get';
		} else {
			$uri = $this->base_url.'/location/submit';
		}

		switch ($carrier) {
			case 'Tre':
			case 'Telia':
			case 'Telenor'://XXXX untested!!
			case 'Tele2': //XXX untested!
				$type = 'MSISDN';
				break;

			case 'Sprint':
				$type = 'MDN';
				break;

			default:
				die('FATAL: unknown carrier "'.$carrier.'"');
		}

		$tid          = 999; //echoed back
		$max_age      = 600; //maximumLocationAge, in seconds
		$req_accuracy = 100; //requestedHorizontalAccuracy, in meters
		$acc_accuracy = 100; //acceptableHorizontalAccuracy, in meters

		$x =
		'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
		'<unRequest>'.
			'<locationRequest>'.
				'<requestHeader>'.
					'<activeToken>'.$this->active_token.'</activeToken>'.
					'<clientTransactionId>'.$tid.'</clientTransactionId>'.
				'</requestHeader>'.
				'<locateQoP>'.
					'<requestQoP>'.
						'<requestedHorizontalAccuracy>'.$req_accuracy.'</requestedHorizontalAccuracy>'.
						'<acceptableHorizontalAccuracy>'.$acc_accuracy.'</acceptableHorizontalAccuracy>'.
						'<maximumLocationAge>'.$max_age.'</maximumLocationAge>'.
					'</requestQoP>'.	//quality of position for all MSID's

					'<msids>'.
						'<value>'.$msid.'</value>'.
						'<type>'.$type.'</type>'.
						'<carrier>'.$carrier.'</carrier>'.
					'</msids>'.

				'</locateQoP>'.
			'</locationRequest>'.
		'</unRequest>';

		$res = http_post($uri, $x);

		return $this->parse_loc_response($res['body']);
	}

	function parse_loc_response($data)
	{
		$parser = xml_parser_create('ISO-8859-1');
		xml_parse_into_struct($parser, $data, $data_vals, $data_idx);
		xml_parser_free($parser);

		$res = array();

		foreach ($data_vals as $idx=>$val)
		{
			if ($val['tag'] == 'LATITUDE')           $res['latitude']  = $val['value'];
			if ($val['tag'] == 'LONGITUDE')          $res['longitude'] = $val['value'];
			if ($val['tag'] == 'ACCURACY')           $res['accuracy']  = $val['value'];
			if ($val['tag'] == 'LOCATIONDATE')       $res['timestamp'] = intval($val['value'] / 1000);//unix time * 1000 (milliseconds)

			if ($val['tag'] == 'LOCATIONSTATUSCODE') $res['status_code']     = $val['value']; //"OK"
			if ($val['tag'] == 'STATUSMESSAGE')      $res['status_message']  = $val['value']; //"OK"

			if ($val['tag'] == 'TRANSACTIONID')      $res['transaction_id']  = $val['value']; //identifier for this request
		}

		return $res;
	}

}

?>
