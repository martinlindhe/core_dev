<?
	set_time_limit(900*3);

	require('../config.php');

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
		//$mail->FromName = 'CitySurf';
		$mail->FromName = 'Förortish Filmfestival';

		$mail->IsHTML(true);   // send HTML mail

		//Embed graphics
		$mail->AddEmbeddedImage('utskick4_forortish/forortish.jpg', 'bild', '', 'base64', 'image/jpeg');
		//$mail->AddEmbeddedImage('utskick1/utskick_1.jpg', 'pic_1', '', 'base64', 'image/jpeg');

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
	$to = array();


	//välj alla som inte är verified
/*
	$q = 'select t1.u_email,t1.id_id,t2.verified from s_user as t1 left join tblVerifyUsers as t2 on (t1.id_id=t2.user_id) where t2.verified!=1 or t2.verified is null';
	$list = $db->getArray($q);
*/

	//välj alla användare på sajten
	$q = 'SELECT DISTINCT(u_email), id_id,lastonl_date FROM s_user ORDER BY lastonl_date DESC';
	$list = $db->getArray($q);
	
	echo 'Beginning sending mail to '.count($list).' ...<br/>';

	$bad = 0;
	foreach($list as $row) {
		
		if (!ValidEmail(trim($row['u_email']))) {
			echo 'Invalid email for <a href="/user_view.php?id='.$row['id_id'].'">'.$row['id_id'].'</a>, last online '.$row['lastonl_date'].' - '.$row['u_email'].'<br/>';
			$bad++;

			$delete_date = mktime(0, 0, 0, 1, 1, 2007);
			if (strtotime($row['lastonl_date']) < $delete_date) {
				echo 'Deleting inactive and invalid user!<br/>';
				$q = 'DELETE FROM s_user WHERE id_id='.$row['id_id'];
				$db->delete($q);
			}

		} else {
			$to[] = strtolower(trim($row['u_email']));
		}
	}

	echo 'Found '.$bad.' invalid emails<br/>';

//	$to[] = 'martin@unicorn.tv';
//	$to[] = 'kiano@unicorn.tv';

//	d($to); die;
	die;
	
	$subject = 'Förortish Filmfestival';
	
	$body = file_get_contents('utskick4_forortish/body.html');
	
	//smtp_mass_mail($to, $subject, $body);
?>
