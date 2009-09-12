<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('client_captcha_recaptcha.php');

class Captcha
{
	const RECAPTCHA   = 1;

	private $client;

	function __construct($service = captcha::RECAPTCHA)
	{
		switch ($service) {
		case captcha::RECAPTCHA:
			$this->client = new captcha_recaptcha();
			break;
		}
	}

	function setPubKey($k) { $this->client->setPubKey($k); }
	function setPrivKey($k) { $this->client->setPrivKey($k); }

	function render() { return $this->client->render(); }
	function verify() { return $this->client->verify(); }
}

?>
