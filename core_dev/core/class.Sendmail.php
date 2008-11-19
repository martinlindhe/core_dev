<?php
/**
 * $Id$
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

require_once('output_smtp.php');

class Sendmail
{
	var $debug = false;
	var $smtp = false;
	var $to_adr = array();

	function __construct($server = '', $username = '', $password = '', $port = 25)
	{
		global $config;
		if (!empty($config['debug'])) $this->debug = true;
		$this->smtp = new smtp($server, $username, $password, $port);
	}

	function addRcpt($r)
	{
		if (!$this->smtp->login()) return false;
		$this->to_adr[] = $r;
	}

	/**
	 * Sends a text email
	 * FIXME: move out to different class
	 */
	function send($subject, $msg)
	{
		if (!$this->smtp->_MAIL_FROM($this->smtp->username)) return false;

		$header = '';
		foreach ($this->to_adr as $to) {
			if (!$this->smtp->_RCPT_TO($to)) continue;
			$header .= "To: ".$to."\r\n";
		}

		$header .=
		"From: ".$this->smtp->username."\r\n".		//XXX make from address configurable
		"Subject: ".$subject."\r\n".
		"Date: ".date('r')."\r\n".
		//"MIME-Version: 1.0\r\n".
		//"Content-Type: text/plain; charset=ISO-8859-1\r\n".
		//"Content-Transfer-Encoding: 7bit\r\n".
		"X-Mailer: core_dev\r\n\r\n";	//XXX version string

		return $this->smtp->_DATA($header.$msg);
	}

}

?>
