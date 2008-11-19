<?php
/**
 * $Id$
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

		$header = '';
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

		$attachment_data = '';
		foreach ($this->attachments as $a) {
			$data = file_get_contents($a);
			$chunk = chunk_split(base64_encode($data));
			$attachment_data .=
			"--MIME_BOUNDARY\r\n".
			"Content-Type: image/png; name=\"".basename($a)."\"\r\n".	//XXX: get mimetype
			"Content-disposition: attachment\r\n".
			"Content-Transfer-Encoding: base64\r\n\r\n".
			$chunk."\r\n\r\n";
		}

		$header .=
		"From: ".$this->from_adr."\r\n".
		"Subject: ".$subject."\r\n".
		"Date: ".date('r')."\r\n".
		"X-Mailer: core_dev\r\n".	//XXX version string
		"MIME-Version: 1.0\r\n";
		if (count($this->attachments)) {
			$header .= "Content-Type: multipart/mixed; boundary=\"MIME_BOUNDARY\"";
		} else {
			$header .= "Content-Type: ".($this->html ? 'text/html' : 'text/plain')."; charset=utf-8\r\n";
		}
		$header .= "\r\n";

		return $this->smtp->_DATA($header.$msg.$attachment_data);
	}

}

?>
