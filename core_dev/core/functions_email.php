<?


	/**
	 * Helper function: calls class.phpmailer.php functions. $mails is a array of recipients
	 *
	 * \param $mails array of destination e-mail addresses
	 * \param $subject subject of e-mail
	 * \param $body body of e-mail
	 */
	function smtp_mass_mail($mails, $subject, $body)
	{
		global $config;
		
		$mail = new PHPMailer();
		
		$mail->IsSMTP();                                // send via SMTP
		$mail->Host     = $config['smtp']['host']; 			// SMTP servers
		$mail->SMTPAuth = true;    											// turn on SMTP authentication
		$mail->Username = $config['smtp']['username'];	// SMTP username
		$mail->Password = $config['smtp']['password'];	// SMTP password
		$mail->CharSet  = 'utf-8';

		$mail->From     = $config['smtp']['sender'];
		$mail->FromName = $config['smtp']['sender_name'];

		foreach ($mails as $adr) {
			$mail->AddAddress($adr);
		}

		$mail->IsHTML(true);                   					// send as HTML

		//Embed graphics
		$mail->AddEmbeddedImage($config['smtp']['mail_footer'], 'pic_name', '', 'base64', 'image/png');

		$mail->Subject  = $subject;
		$mail->Body     = $body;

		if (!$mail->Send()) {
			echo 'Failed to send mail, error:'.$mail->ErrorInfo;
			return false;
		}

		return true;
	}

	function smtp_mail($dst_adr, $subj, $msg)
	{
		global $config;

		$mail = new PHPMailer();

		$mail->Mailer = 'smtp';
		$mail->Host = $config['smtp']['host'];
		$mail->Username = $config['smtp']['username'];
		$mail->Password = $config['smtp']['password'];

		$mail->CharSet  = 'utf-8';

		$mail->From = $config['smtp']['sender'];
		$mail->FromName = $config['smtp']['sender_name'];

		$mail->IsHTML(true); // send HTML mail?

		//Embed graphics
		$mail->AddEmbeddedImage($config['smtp']['mail_footer'], 'pic_name', '', 'base64', 'image/png');

		$mail->AddAddress($dst_adr);
		$mail->Subject = $subj;
		$mail->Body = $msg;

		if (!$mail->Send()) return false;
		return true;
	}

?>
