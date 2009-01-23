<?php
/**
 * $Id$
 *
 * Skeleton for auth classes
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

abstract class auth_base
{
	abstract function login($username, $password);
	abstract function logout($userId);

	abstract function validLogin($username, $password);

	abstract function handleForgotPassword($email);
}

?>
