<?php
/**
 * $Id$
 *
 * Code to implement activation procedures
 *
 * Utility script: cron\cleanup_activations.php
 * 		This script deletes all > 30 day old entries from tblActivation
 *
 * \todo cleanup script that deletes un-activated users entirely
 * \todo finish api/human_test.php implementation
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

//FIXME: tblActivation.answer was only used for CAPTCHA's. safe to drop?

define('ACTIVATE_EMAIL',		1);
define('ACTIVATE_SMS',			2);
define('ACTIVATE_CHANGE_PWD',	3);		//used to allow the user to set a new password from a email link when he forgot password
define('ACTIVATE_ACCOUNT',		4);		//used to activate a pre-generated account

$config['activate']['expire_time_email']		= (24*60*60)*30; 	///< 30 days
$config['activate']['expire_time_sms']			= (24*60*60)*30;	///< 30 days
$config['activate']['expire_time_change_pwd']	= (24*60*60)*30; 	///< 30 days
$config['activate']['expire_time_account']		= (24*60*60)*30;	///< 30 days

/**
 * Returns an unused numeric activation code
 *
 * \param $lo lower limit of code
 * \param $hi upper limit of code
 * \return unused numeric activation code
 */
function generateActivationCode($_type, $lo, $hi)
{
	global $db;

	$expiry = getActivationExpireTime($_type);

	do {
		$code = mt_rand($lo, $hi);
		$q = 'SELECT COUNT(*) FROM tblActivation WHERE rnd="'.$code.'" AND timeActivated IS NULL AND timeCreated < NOW() + INTERVAL '.$expiry.' SECOND';
	} while ($db->getOneItem($q));
	return $code;
}

/**
 * Returns the expire time of specified type of activation code
 */
function getActivationExpireTime($_type)
{
	global $config;
	switch ($_type)
	{
		case ACTIVATE_EMAIL:		return $config['activate']['expire_time_email'];
		case ACTIVATE_SMS:			return $config['activate']['expire_time_sms'];
		case ACTIVATE_CHANGE_PWD:	return $config['activate']['expire_time_change_pwd'];
		case ACTIVATE_ACCOUNT:		return $config['activate']['expire_time_account'];
		default: die('UNKNOWN TYPE '.$_type);
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
	$q .= ' AND timeActivated IS NULL AND timeCreated >= DATE_SUB(NOW(), INTERVAL '.$expired.' SECOND)';

	switch ($_type) {
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
	$q .= ' AND timeActivated IS NULL AND timeCreated >= DATE_SUB(NOW(), INTERVAL '.$expired.' SECOND)';
	return $db->getOneItem($q);
}

/**
 * Get users activation date
 *
 * \param $_type type
 * \param $_id userId
 * \return date
 */
function getActivationDate($_type, $_id)
{
	global $db, $config;
	if (!is_numeric($_type) || !is_numeric($_id)) return false;

	$q = 'SELECT timeActivated FROM tblActivation WHERE timeActivated IS NOT NULL AND type='.$_type.' AND userId='.$_id;
	$q .= ' LIMIT 1';
	return $db->getOneItem($q);
}


/**
 * Get users activation code
 *
 * \param $_type type
 * \param $_id userId
 * \return user id
 */
function getActivationCode($_type, $_id)
{
	global $db, $config;
	if (!is_numeric($_type) || !is_numeric($_id)) return false;

	$q = 'SELECT rnd FROM tblActivation WHERE timeActivated IS NULL AND type='.$_type.' AND userId='.$_id;
	$q .= ' LIMIT 1';
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

	$q = 'UPDATE tblActivation SET timeActivated = NOW() WHERE type='.$_type.' AND userId='.$_id;
	$db->update($q);
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

	$q = 'UPDATE tblActivation SET timeActivated = NOW() WHERE type='.$_type.' AND rnd="'.$db->escape($_code).'"';
	$db->update($q);
}
?>
