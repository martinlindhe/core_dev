<?
/**
 * $Id$
 *
 * Code to implement activation procedures
 *
 * \todo cleanup script that deletes all > 30 day old entries from tblActivation
 * \todo cleanup script that deletes un-activated users entirely
 * \todo finish api/human_test.php implementation
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

	define('ACTIVATE_CAPTCHA',		1);
	define('ACTIVATE_EMAIL',		2);
	define('ACTIVATE_SMS',			3);
	define('ACTIVATE_CHANGE_PWD',	4);		//used to allow the user to set a new password from a email link when he forgot password
	define('ACTIVATE_ACCOUNT',		5);		//used to activate a pre-generated account

	$config['activate']['expire_time_captcha']		= 60*5;			///< 5 minutes
	$config['activate']['expire_time_email']		= (12*60*60); 	///< 12 hours
	$config['activate']['expire_time_sms']			= (12*60*60);	///< 12 hours
	$config['activate']['expire_time_change_pwd']	= (6*60*60); 	///< 6 hours

	/**
	 * Returns an unused numeric activation code
	 *
	 * \param $lo lower limit of code
	 * \param $hi upper limit of code
	 * \return unused numeric activation code
	 */
	function generateActivationCode($lo, $hi)
	{
		global $db;

		do {
			$code = mt_rand($lo, $hi);
			$q = 'SELECT COUNT(*) FROM tblActivation WHERE rnd="'.$code.'"';
		} while ($db->getOneItem($q));

		return $code;
	}

	function getActivationExpireTime($_type)
	{
		global $config;
		switch ($_type)
		{
			case ACTIVATE_CAPTCHA:		return $config['activate']['expire_time_captcha'];
			case ACTIVATE_EMAIL:		return $config['activate']['expire_time_email'];
			case ACTIVATE_SMS:			return $config['activate']['expire_time_sms'];
			case ACTIVATE_CHANGE_PWD:	return $config['activate']['expire_time_change_pwd'];
		}
	}

	/**
	 * Verify if activation code is valid
	 *
	 * \param $_type type
	 * \param $_code activation code
	 * \param $_answer is string for CAPTCHA's, userId for email/sms activation
	 */
	function verifyActivation($_type, $_code, $_answer = '')
	{
		global $db, $config;
		if (!is_numeric($_type)) return false;

		$expired = getActivationExpireTime($_type);

		$q = 'SELECT COUNT(entryId) FROM tblActivation WHERE type='.$_type.' AND rnd="'.$db->escape($_code).'"';
		$q .= ' AND timeCreated >= DATE_SUB(NOW(), INTERVAL '.$expired.' SECOND)';

		switch ($_type)
		{
			case ACTIVATE_CAPTCHA:
				$q .= ' AND answer="'.$db->escape($_answer).'"';
				break;

			case ACTIVATE_EMAIL:
			case ACTIVATE_SMS:
			case ACTIVATE_CHANGE_PWD:
				if (!is_numeric($_answer)) return false;
				$q .= ' AND userId='.$_answer;
				break;
		}
		return $db->getOneItem($q);
	}

	/**
	 * Checks if the activation code is valid, returns associated user id
	 *
	 * \param $_type type
	 * \param $_code activation code
	 * \return user id
	 */
	function getActivationUserId($_type, $_code)
	{
		global $db, $config;
		if (!is_numeric($_type)) return false;

		$expired = getActivationExpireTime($_type);

		$q = 'SELECT userId FROM tblActivation WHERE type='.$_type.' AND rnd="'.$db->escape($_code).'"';
		$q .= ' AND timeCreated >= DATE_SUB(NOW(), INTERVAL '.$expired.' SECOND)';
		return $db->getOneItem($q);
	}

	/**
	 * Creates a new activation code
	 *
	 * \param $_type type
	 * \param $_code activation code
	 * \param $_answer is correct answer to captcha-implementation, or userId for email/sms activation
	 * \return activationId
	 */
	function createActivation($_type, $_code, $_answer = '')
	{
		global $db;
		if (!is_numeric($_type)) return false;

		$q = 'INSERT INTO tblActivation SET type='.$_type.',rnd="'.$db->escape($_code).'",timeCreated=NOW()';
		switch ($_type) {
			case ACTIVATE_CAPTCHA:
				$q .= ', answer="'.$db->escape($_answer).'"';
				break;

			case ACTIVATE_EMAIL:
			case ACTIVATE_SMS:
			case ACTIVATE_CHANGE_PWD:
			case ACTIVATE_ACCOUNT:
				if (!is_numeric($_answer)) return false;
				removeActivations($_type, $_answer); 
				$q .= ',userId='.$_answer;
				break;
		}
		return $db->insert($q);
	}

	/**
	 * Removes all activation codes of same type for same user. Used when generating a new activation
	 * code of specified type
	 *
	 * \param $_type type
	 * \param $_id user id
	 */
	function removeActivations($_type, $_id)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($_id)) return false;

		$q = 'DELETE FROM tblActivation WHERE type='.$_type.' AND userId='.$_id;
		$db->delete($q);
	}

	/**
	 * Removes a single activation code. Call this when activation process has succeeded
	 *
	 * \param $_type type
	 * \param $_code activation code
	 */
	function removeActivation($_type, $_code)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($_code)) return false;

		$q = 'DELETE FROM tblActivation WHERE type='.$_type.' AND rnd="'.$db->escape($_code).'"';
		$db->delete($q);
	}
?>
