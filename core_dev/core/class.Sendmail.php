<?php
/**
 * $Id$
 *
 * MIME attachments:                 http://tools.ietf.org/html/rfc2231
 * MIME message bodies:              http://tools.ietf.org/html/rfc2045
 * MIME media types & multipart:     http://tools.ietf.org/html/rfc2046
 * MIME header Content-Disposition:  http://tools.ietf.org/html/rfc2183
 * MIME validator:                   http://www.apps.ietf.org/msglint.html
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

/**
 * TODO: use regexp to catch invalid mail address input
 * TODO: figure out the proper "multipart" content-type to set on
 *    attachments/embedded files. the current approach works for Thunderbird,
 *    but message with attachment is shown without attachments before they
 *    are fully loaded (no gem next to mail)
 */

require_once('output_smtp.php');

class Sendmail
{
	var $smtp, $debug = false;

	var $from_adr, $from_name;
	var $rply_adr, $rply_name;
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

	function from($s, $n = '')
	{
		$this->from_adr = $s;
		$this->from_name = $n;
	}

	function reply_to($s, $n = '')
	{
		$this->rply_adr = $s;
		$this->rply_name = $n;
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
		return $this->embed($file, '');
	}

	//<img src="cid:pic_name">
	function embed($file, $cid)
	{
		if (!file_exists($file)) {
			echo "Error: File ".$file." not found\n";
			return false;
		}
		$this->attachments[] = array($file, $cid);
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
			"From: ".($this->from_name ? $this->from_name." <".$this->from_adr.">" : $this->from_adr)."\r\n".
			"Subject: ".$subject."\r\n".
			"User-Agent: core_dev\r\n".	//XXX version string
			"MIME-Version: 1.0\r\n";

		if ($this->rply_adr) {
			$header .= "Reply-To: ".($this->rply_name ? $this->rply_name." <".$this->rply_adr.">" : $this->rply_adr)."\r\n";
		}

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

		if (count($this->attachments)) {
			$boundary = '------------0'.time().md5(mt_rand(0, 9999999999999).microtime());
			$header .=
				//"Content-Type: ".(count($this->embedded) ? "multipart/related" : "multipart/mixed").";".
				"Content-Type: multipart/related;".	//XXX what is "multipart/alternative"?
					" type=\"".($this->html ? "text/html" : "text/plain")."\";\r\n".
					" boundary=\"".$boundary."\"\r\n".
				"\r\n".
				"This is a multi-part message in MIME format.\r\n\r\n".
				"--".$boundary."\r\n";
		}

		$header .=
			"Content-Type: ".($this->html ? "text/html" : "text/plain")."; charset=utf-8\r\n".
			"Content-Transfer-Encoding: 7bit\r\n".
			"\r\n".
			$msg."\r\n";

		$attachment_data = '';
		foreach ($this->attachments as $a) {
			$data = file_get_contents($a[0]);
			$attachment_data .=
				"\r\n".
				"--".$boundary."\r\n".
				"Content-Type: image/png;\r\n".	//FIXME: get mimetype
				" name=\"".basename($a[0])."\"\r\n".
				"Content-Transfer-Encoding: base64\r\n".
				($a[1] ? "Content-ID: <".$a[1].">\r\n" : "").
				"Content-Disposition: ".($a[1] ? "inline" : "attachment").";\r\n".
				" filename=\"".basename($a[0])."\"\r\n".
				"\r\n".
				chunk_split(base64_encode($data));
		}

		if ($attachment_data) $attachment_data .= "--".$boundary."--";

		return $this->smtp->_DATA($header.$attachment_data);
	}

}

?>
