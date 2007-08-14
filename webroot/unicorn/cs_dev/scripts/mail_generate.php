<?
die;
	set_time_limit(900*3);

	include("_config/online.include.php");
	require("_config/class.phpmailer.php");

//Helper function: calls class.phpmailer.php functions. $mails is a array of recipients
	function smtp_mass_mail($mails, $subject, $body)
	{
		$mail = new PHPMailer();
		
		$mail->IsSMTP();                                // send via SMTP
		$mail->Host     = 'mail.citysurf.tv'; 			// SMTP servers
		$mail->SMTPAuth = true;    											// turn on SMTP authentication
		$mail->Username = 'info';	// SMTP username
		$mail->Password = 'ucinfo477';	// SMTP password
		//$mail->CharSet  = 'utf-8';

		$mail->From     = 'noreply@citysurf.tv';
		$mail->FromName = 'CitySurf';

		$mail->IsHTML(true);   // send HTML mail

		//Embed graphics
		$mail->AddEmbeddedImage('/home/martin/www/utskick1/utskick_head.jpg', 'head', '', 'base64', 'image/jpeg');
		$mail->AddEmbeddedImage('/home/martin/www/utskick1/utskick_1.jpg', 'pic_1', '', 'base64', 'image/jpeg');

		$mail->Subject  = $subject;
		$mail->Body     = $body;

		//addressera alla mottagare som BCC
		//can adress each mail to max XXX ppl using BCC, so need to generate multiple copies of the mail
		foreach ($mails as $adr) {
			if (!$adr) continue;
			$mail->AddBCC($adr);
			echo 'Sending mail to '.$adr.' ... ';
				
			if (!$mail->Send()) echo 'Failed with error: '.$mail->ErrorInfo.'<br/>';
			else echo 'Success.<br/>';
			$mail->ClearAllRecipients();
		}
		
		return true;
	}

	$q = 'select t1.u_email,t1.id_id,t2.verified from s_user as t1 left join tblVerifyUsers as t2 on (t1.id_id=t2.user_id) where t2.verified!=1 or t2.verified is null';
	$list = $sql->query($q);
	
	$to = array();
	
	$i = 0;

	foreach($list as $row) {
		if ($row[2] === '0') continue;
		
		$to[] = strtolower($row[0]);
	}

	$to[] = 'martin_lindhe@yahoo.se';

	//echo '<pre>'; print_r($to); die;
	//$to = array('martin@unicorn.tv', 'martin_lindhe@yahoo.se');
	
	$subject = 'Välkommen till nya CitySurf!';
	
	$body = file_get_contents('/home/martin/www/utskick1/body.html');
	
	smtp_mass_mail($to, $subject, $body);

?>
