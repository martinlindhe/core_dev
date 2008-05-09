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

	function smtp_mail($dst_adr, $subj, $msg, $attach_name = '', $attach_data = '')
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
		if (isset($config['smtp']['mail_footer'])) {
			$mail->AddEmbeddedImage($config['smtp']['mail_footer'], 'pic_name', '', 'base64', 'image/png');
		}

		if ($attach_name && $attach_data) {
			$mail->AddStringAttachment($attach_data, $attach_name, 'base64', 'application/pdf');
		}

		$mail->AddAddress($dst_adr);
		$mail->Subject = $subj;
		$mail->Body = $msg;

		if (!$mail->Send()) return false;
		return true;
	}
	
	function contact_users($message, $subject, $all, $presvid, $logged_in_days, $days, $res) {
		global $db, $files;
		
		if (empty($message) || empty($subject)) return false;
		
		if ($all == 1) { // Ignore everything else, just get a list of all users.
			$users = Users::getUsers();
			
			foreach ($users as $row) {
				$email = loadUserdataEmail($row['userId']);
				echo 'All users.<br/>';
				smtp_mail($email, $subject, $message);
			}
		}
		else {
			foreach ($res as $row) {
				if (!empty($days)) {
					if (!is_numeric($days)) return false;
					$timestamp = strtotime('-'.$days.' day');
					$logintime = datetime_to_timestamp(Users::getLogintime($row['userId']));

 					// user logged in before timestamp (so hasnt been logged in the latest $days days)
 					if ($logged_in_days == 1 && $logintime < $timestamp) {
 						// Then it's wrong, so dont send email
 						continue;
					}
					else if ($logged_in_days == 0 && $logintime > $timestamp) {
						continue;
					}
					
				}
				if (!empty($presvid)) {
					if ($presvid == 1) {
						$cId = loadSetting(SETTING_USERDATA, $row['userId'], 'm2w_id');
						if ($cId) {
							$vid_pres = $files->getFiles(FILETYPE_VIDEOPRES, $cId);
							if (!is_array($vid_pres)) {
								continue;
							}
						}
						else {
							continue;
						}
					}
				}
				$email = loadUserdataEmail($row['userId']);
				echo $email.'<br/>';
				smtp_mail($email, $subject, $message);
			}
		}

	}

?>
