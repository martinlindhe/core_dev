<?php
/**
 * $Id$
 *
 * Skeleton for user classes
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

abstract class user_base
{
	abstract function reserve();

	abstract function register($username, $password1, $password2, $_mode = USERLEVEL_NORMAL, $newUserId = 0);

	abstract function unregister();

	abstract function remove();
}

?>
