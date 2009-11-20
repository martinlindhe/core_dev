<?php
/**
 * $Id$
 *
 * Provides a service-agnostic interface for captcha-based
 * web services
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//STATUS: good

require_once('class.CoreBase.php');
require_once('client_captcha_recaptcha.php');

class Captcha extends CoreBase
{
	const RECAPTCHA = 1;      ///< recaptcha.net service provider

	private $Client;          ///< Captcha provider object
	private $checked = false; ///< was the captcha checked?
	private $result  = false; ///< was the captcha accepted?

	function __construct($service = Captcha::RECAPTCHA)
	{
		switch ($service) {
		case Captcha::RECAPTCHA:
			$this->Client = new CaptchaRecaptcha();
			break;
		}
	}

	function setPubKey($k) { $this->Client->setPubKey($k); }
	function setPrivKey($k) { $this->Client->setPrivKey($k); }

	function render() { return $this->Client->render(); }

	function verify()
	{
		if (!$this->checked) {
			$this->result  = $this->Client->verify();
			$this->checked = true;

			if (!$this->result)
				$this->setError( $this->Client->getError() );
		}

		return $this->result;
	}
}

?>
