<?php
/**
 * $Id$
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

//defaults, please override:
$config['smtp']['host'] = 'localhost';
$config['smtp']['username'] = '';
$config['smtp']['password'] = '';
$config['smtp']['sender'] = 'core_dev@somewhere';
$config['smtp']['sender_name'] = 'core_dev';

/**
 * Helper function: calls class.phpmailer.php functions
 *
 * \param $dst_adr single address or array of destination e-mail addresses
 * \param $subj subject of e-mail
 * \param $msg body of e-mail
 * \param $attach_name filename of attachment (optional)
 * \param $attach_data data of attachment (optional)
 */
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

	if (is_array($dst_adr)) {
		foreach ($mails as $adr) {
			$mail->AddAddress($adr);
		}
	} else {
		$mail->AddAddress($dst_adr);
	}

	$mail->Subject = $subj;
	$mail->Body = $msg;

	if (!$mail->Send()) return false;
	return true;
}

/**
 * Send email to multiple users
 */
function contact_users($message, $subject, $all, $presvid, $logged_in_days, $days, $res)
{
	global $db, $files;
	if (empty($message) || empty($subject)) return false;

	if ($all == 1) { // Ignore everything else, just get a list of all users.
		$users = Users::getUsers();

		foreach ($users as $row) {
			$email = loadUserdataEmail($row['userId']);
			echo 'All users.<br/>';
			smtp_mail($email, $subject, $message);
		}
	} else {
		foreach ($res as $row) {
			if (!empty($days)) {
				if (!is_numeric($days)) return false;
				$timestamp = strtotime('-'.$days.' day');
				$logintime = datetime_to_timestamp(Users::getLogintime($row['userId']));

				// user logged in before timestamp (so hasnt been logged in the latest $days days)
				if ($logged_in_days == 1 && $logintime < $timestamp) {
					// Then it's wrong, so dont send email
					continue;
				} else if ($logged_in_days == 0 && $logintime > $timestamp) {
					continue;
				}
			}

			//FIXME denna kod är m2w-specifik och har inget att göra i core_dev. urval borde göras efter USERDATA_TYPE_VIDEOPRES
			if (!empty($presvid)) {
				if ($presvid == 1) {
					$cId = loadSetting(SETTING_USERDATA, 0, $row['userId'], 'm2w_id');
					if (!$cId) continue;
					$vid_pres = $files->getFiles(FILETYPE_VIDEOPRES, $cId);
					if (!is_array($vid_pres)) continue;
				}
			}
			$email = loadUserdataEmail($row['userId']);
			echo $email.'<br/>';
			smtp_mail($email, $subject, $message);
		}
	}
}

?>
