<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

//defaults, please override:
$config['smtp']['host'] = 'localhost';
$config['smtp']['username'] = '';
$config['smtp']['password'] = '';
$config['smtp']['sender'] = 'core_dev@somewhere';
$config['smtp']['sender_name'] = 'core_dev';

require_core('ext/class.phpmailer.php');

/**
 * Helper function: calls class.phpmailer.php functions
 *
 * @param $dst_adr single address or array of destination e-mail addresses
 * @param $subj subject of e-mail
 * @param $msg body of e-mail
 * @param $attach_name filename of attachment (optional)
 * @param $attach_data data of attachment (optional)
 */
function smtp_mail($dst_adr, $subj, $msg, $attach_name = '', $attach_data = '', $html = true)
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

	$mail->IsHTML($html); // send HTML mail?

	//Embed graphics
	if (!empty($config['smtp']['mail_footer'])) {
		$mail->AddEmbeddedImage($config['smtp']['mail_footer'], 'pic_name', '', 'base64', 'image/png');
	}

	if ($attach_name && $attach_data) {
		$mail->AddStringAttachment($attach_data, $attach_name, 'base64', 'application/pdf');
	}

	if (is_array($dst_adr)) {
		foreach ($dst_adr as $adr) {
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
	global $h, $db;
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
					$vid_pres = $h->files->getFiles(FILETYPE_VIDEOPRES, $cId);
					if (!is_array($vid_pres)) continue;
				}
			}
			$email = loadUserdataEmail($row['userId']);
			echo $email.'<br/>';
			smtp_mail($email, $subject, $message);
		}
	}
}
/*

	//SMTP out settings
	public $smtp_host = 'smtp.example.com';
	public $smtp_username = '';
	public $smtp_password = '';

	public $mail_from = 'noreply@example.com';
	public $mail_from_name = 'core_dev';

	public $mail_activate_msg =	//XXX move all smtp crap out of here
		"Hello. Someone (probably you) registered an account from IP __IP__

Username: __USERNAME__
Activation code: __CODE__

Follow this link to activate your account:
__URL__

The link will expire in __EXPIRETIME__";

	public $mail_password_msg =
		"Hello. Someone (probably you) asked for a password reset procedure from IP __IP__

Registered username: __USERNAME__

Follow this link to set a new password:
__URL__

The link will expire in __EXPIRETIME__";
*/
	/**
	 * FIXME remove smtp settings from Auth. use $config['email']
	 */
/*	function SmtpSend($dst_adr, $subj, $msg)
	{
		$mail = new PHPMailer();

		$mail->Mailer = 'smtp';
		$mail->Host = $this->smtp_host;
		$mail->Username = $this->smtp_username;
		$mail->Password = $this->smtp_password;

		$mail->CharSet  = 'utf-8';

		$mail->From = $this->mail_from;
		$mail->FromName = $this->mail_from_name;

		$mail->IsHTML(false); // send HTML mail?

		$mail->AddAddress($dst_adr);
		$mail->Subject = $subj;
		$mail->Body = $msg;

		if (!$mail->Send()) {
			$this->mail_error = true;
			return false;
		}
		return true;
	}
*/
	/**
	 * Sends a account activation mail to specified user
	 *
	 * @param $_id user id
	 */
/*	function sendActivationMail($_id)
	{
		global $config;
		if (!is_numeric($_id)) return false;

		$email = loadUserdataEmail($_id);
		if (!$email) return false;

		$code = generateActivationCode(ACTIVATE_EMAIL, 1000000, 9999999);
		createActivation(ACTIVATE_EMAIL, $code, $_id);

		$subj = t('Account activation');

		$pattern = array('/__USERNAME__/', '/__IP__/', '/__CODE__/', '/__URL__/', '/__EXPIRETIME__/');
		$replacement = array(
			Users::getName($_id),
			$_SERVER['REMOTE_ADDR'],
			$code,
			$config['app']['full_url']."activate.php?id=".$_id."&code=".$code,
			shortTimePeriod($config['activate']['expire_time_email'])
		);
		$msg = preg_replace($pattern, $replacement, $this->mail_activate_msg);

		if (!$this->SmtpSend($email, $subj, $msg)) return false;

		$this->activation_sent = true;
		return true;
	}
*/
	/**
	 * Verifies user activaction code
	 *
	 * @param $_id
	 * @param $_code
	 * @return true if success
	 */
/*	function verifyActivationMail($_id, $_code)
	{
		if (!is_numeric($_id) || !is_numeric($_code)) return false;

		if (!verifyActivation(ACTIVATE_EMAIL, $_code, $_id)) {
			echo t('Activation code is invalid or expired.');
			return false;
		}

		Users::activate($_id);

		removeActivation(ACTIVATE_EMAIL, $_code);

		echo t('Your account has been activated.').'<br/>';
		echo t('You can now proceed to').' <a href="login.php">'.t('log in').'</a>.';
		return true;
	}
*/

?>
