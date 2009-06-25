<?php
/**
 * $Id$
 *
 * Skeleton for auth classes
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

require_once('atom_activation.php');	//for mail activation
require_once('class.Sendmail.php');		//for sending mail

abstract class auth_base
{
	abstract function login($username, $password);
	abstract function logout($userId);

	abstract function validLogin($username, $password);

	public $mail_activate_msg =
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

	/**
	 * Sends a account activation mail to specified user
	 *
	 * @param $_id user id
	 */
	function sendActivationMail($_id)
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

	/**
	 * Looks up user supplied email address / alias and generates a mail for them if needed
	 *
	 * @param $email email address
	 */
	function handleForgotPassword($email)
	{
		global $config;

		$email = trim($email);
		if (strpos($email, '@')) {
			if (!ValidEmail($email)) return false;
			$_id = findUserByEmail($email);
		} else {
			//find user by alias
			$_id = Users::getId($email);
		}
		if (!$_id) {
			$session->error = t('Invalid email address or username');
			return false;
		}

		$email = loadUserdataEmail($_id);

		$code = generateActivationCode(ACTIVATE_CHANGE_PWD, 10000000, 99999999);
		createActivation(ACTIVATE_CHANGE_PWD, $code, $_id);

		$subj  = t('Forgot password');

		$pattern = array('/__USERNAME__/', '/__IP__/', '/__URL__/', '/__EXPIRETIME__/');
		$replacement = array(
			Users::getName($_id),
			$_SERVER['REMOTE_ADDR'],
			$config['app']['full_url']."reset_password.php?id=".$_id."&code=".$code,
			shortTimePeriod($config['activate']['expire_time_email'])
		);
		$msg = preg_replace($pattern, $replacement, $this->mail_password_msg);

		if (!$this->SmtpSend($email, $subj, $msg)) {
			removeActivation(ACTIVATE_CHANGE_PWD, $code);
			$session->error = t('Problems sending mail');
			return false;
		}

		$this->resetpwd_sent = true;
		return true;
	}

	var $sendmail, $mail_server, $mail_username, $mail_password, $mail_fromadr, $mail_fromname;

	function SmtpConfig($server, $username, $password, $from_adr, $from_name = '') {
		$this->mail_server   = $server;
		$this->mail_username = $username;
		$this->mail_password = $password;
		$this->mail_fromadr  = $from_adr;
		$this->mail_fromname = $from_name;
	}

	function SmtpSend($dst_mail, $subject, $body)
	{
		if (!$this->sendmail) {
			$this->sendmail = new Sendmail($this->mail_server, $this->mail_username, $this->mail_password);
			$this->sendmail->from($this->mail_fromadr, $this->mail_fromname);
		}

		$this->sendmail->to($dst_mail);
		return $this->sendmail->send($subject, $body);
	}

}

?>
