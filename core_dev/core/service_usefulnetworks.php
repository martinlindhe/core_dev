<?php
/**
 * $Id$
 *
 * Functions to interact with useful-networks.com Poisitioning API
 *
 * Implemented using "LBS Location Services API Document v1.09"
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//TODO: what is expire time of a "active_token"?
//TODO: parse positioning responses
//TODO: implement async positioning

require_once('output_http.php');

class un_request
{
	var $app_id, $user, $pass, $base_url; ///< auth credentials

	var $active_token = '';  ///< set with active token if authenticated
	var $response_code = ''; ///< holds the response code of last server response

	function parse_response($data)
	{
		$parser = xml_parser_create('ISO-8859-1');
		xml_parse_into_struct($parser, $data, $data_vals, $data_idx);
		xml_parser_free($parser);

		print_r($data_vals); print_r($data_idx);

		foreach ($data_vals as $idx=>$val)
		{
			if ($val['tag'] == 'UNRESPONSECODE') $this->response_code = $val['value'];
			if ($val['tag'] == 'ACTIVETOKEN')    $this->active_token  = $val['value'];

			//if ($val['tag'] == 'CLIENTTRANSACTIONID') $this->xxx = $val['value'];
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

		$tid = 666;

		$x =
		'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
		'<unRequest>'.
			'<authenticationRequest>'.
				'<applicationId>'.$this->app_id.'</applicationId>'.
				'<applicationUser>'.$this->user.'</applicationUser>'.
				'<applicationPassword>'.$this->pass.'</applicationPassword>'.
				'<clientTransactionId>'.$tid.'</clientTransactionId>'.	//echoed back
			'</authenticationRequest>'.
		'</unRequest>';

		$res = http_post($uri, $x);

		echo "XXX result:\n"; print_r($res);

		$this->parse_response($res['body']);

		if ($this->response_code != "OK") {
			echo "FATAL: authentication failed!\n";
			return false;
		}
		return true;
	}

	/**
	 * Performs a syncronous (blocking) MSID position request
	 */
	function pos_sync($msid)
	{
		$uri = $this->base_url.'/location/get';

$tid = 999;

		$x =
		'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
		'<unRequest>'.
			'<locationRequest>'.
				'<requestHeader>'.
					'<activeToken>'.$this->active_token.'</activeToken>'.
					'<clientTransactionId>'.$tid.'</clientTransactionId>'.	//echoed back
				'</requestHeader>'.
				'<async>false</async>'.	//XXX rätt sätt att sätta "async"?

				'<locateQoP>'.
					'<requestQoP>'.
						'<requestedHorizontalAccuracy>100</requestedHorizontalAccuracy>'.	//in meters
						'<acceptableHorizontalAccuracy>100</acceptableHorizontalAccuracy>'.	//in meters
						'<maximumLocationAge>600</maximumLocationAge>'. //in seconds
					'</requestQoP>'.	//quality of position for all MSID's

					'<msids>'.
						'<value>'.$msid.'</value>'.
						'<type>MSISDN</type>'.		//XXX or "MDN", depends on carrier????
						'<carrier>Telia</carrier>'.	//XXX usch å blä
					'</msids>'.

				'</locateQoP>'.
			'</locationRequest>'.
		'</unRequest>';

		$res = http_post($uri, $x);

		echo "XXX result:\n"; print_r($res);
	}

}

?>
