<?php
/**
 * $Id$
 *
 * MIME attachments:                 http://tools.ietf.org/html/rfc2231
 * MIME message bodies:              http://tools.ietf.org/html/rfc2045
 * MIME header Content-Disposition:  http://tools.ietf.org/html/rfc2183
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

/**
 * TODO: ability to add file attachments to the mail
 * TODO: ability to embed graphics in the mail (html)
 * TODO: ability to add "Reply-To:" header
 * TODO: use regexp to catch invalid mail address input
 */

require_once('output_smtp.php');

class Sendmail
{
	var $smtp, $debug = false;

	var $from_adr;
	var $to_adr = array(), $cc_adr = array(), $bcc_adr = array();
	var $html = false;
	var $attachments = array();

	function __construct($server = '', $username = '', $password = '', $port = 25)
	{
		global $config;
		if (!empty($config['debug'])) $this->debug = true;
		$this->smtp = new smtp($server, $username, $password, $port);
		$this->from_adr = $username;
	}

	function from($s)
	{
		$this->from_adr = $s;
	}

	function to($s)
	{
		$this->to_adr[] = $s;
	}

	function cc($s)
	{
		$this->cc_adr[] = $s;
	}

	function bcc($s)
	{
		$this->bcc_adr[] = $s;
	}

	function attach($file)
	{
		if (!file_exists($file)) {
			echo "Error: File ".$file." not found\n";
			return false;
		}
		$this->attachments[] = $file;
		return true;
	}

	/**
	 * Sends a email
	 */
	function send($subject, $msg)
	{
		if (!$this->smtp->login()) return false;
		if (!$this->smtp->_MAIL_FROM($this->from_adr)) return false;

		$header =
			"Date: ".date('r')."\r\n".
			"From: Martin Lindhe <".$this->from_adr.">\r\n".	//XXX testing!!!!!!!!!!!!!1111111111111111
			"User-Agent: core_dev\r\n".	//XXX version string
			"MIME-Version: 1.0\r\n";

		foreach ($this->to_adr as $to) {
			if (!$this->smtp->_RCPT_TO($to)) continue;
			$header .= "To: ".$to."\r\n";
		}
		foreach ($this->cc_adr as $cc) {
			if (!$this->smtp->_RCPT_TO($cc)) continue;
			$header .= "Cc: ".$cc."\r\n";
		}
		foreach ($this->bcc_adr as $bcc) {
			if (!$this->smtp->_RCPT_TO($bcc)) continue;
			$header .= "Bcc: ".$bcc."\r\n";
		}

		$header .= "Subject: ".$subject."\r\n";

		//$boundary = '------------060501040008'.mt_rand(0, 9999999999999);	//XXX: create a better boundary (Typically this is done by inserting a large random string)
		$boundary = '------------060501040008060800050807';

		if (count($this->attachments)) {
			$header .=
				"Content-Type: multipart/mixed;\r\n".
				" boundary=\"".$boundary."\"\r\n".
				"\r\n".
				"This is a multi-part message in MIME format.\r\n".
				$boundary."\r\n";
		}
		$header .=
			//"Content-Type: ".($this->html ? 'text/html' : 'text/plain')."; charset=utf-8; format=flowed\r\n".	//XXX what is "format=flowed" ???
			"Content-Type: ".($this->html ? 'text/html' : 'text/plain')."; charset=ISO-8859-1; format=flowed\r\n".	//XXX what is "format=flowed" ???
			"Content-Transfer-Encoding: 7bit\r\n".
			"\r\n".
			$msg."\r\n";	//XXX: ska \r\n paddas här för icke-attachment mails??

		$attachment_data = '';
		foreach ($this->attachments as $a) {
			$data = file_get_contents($a);
			$attachment_data .=
				"\r\n".$boundary."\r\n".
				"Content-Type: image/png;\r\n".
				" name=\"".basename($a)."\"\r\n".	//XXX: get mimetype
				"Content-Transfer-Encoding: base64\r\n".
				//"Content-Disposition: attachment\r\n".	//XXX use "inline" for embedded gfx
				"Content-Disposition: inline;\r\n".	//XXX use "inline" for embedded gfx
				" filename=\"".basename($a)."\"\r\n".
				"\r\n".
				chunk_split(base64_encode($data), 72, "\r\n");	//XXX: unneeeded, 72 = thunderbird length
		}
		if ($attachment_data) $attachment_data .= $boundary."--";

		return $this->smtp->_DATA($header.$attachment_data);
	}

}

?>
