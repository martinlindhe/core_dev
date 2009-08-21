<?php
/**
 * $Id$
 *
 * Skeleton for session classes
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

abstract class session_base
{
	abstract function start($_id, $_username, $_usermode);
	abstract function end();

	//XXX move these somewhere else:
	abstract function startPage();
	abstract function loggedOutStartPage();
	abstract function errorPage();
	abstract function requireLoggedOut();
	abstract function requireLoggedIn();
	abstract function requireWebmaster();
	abstract function requireAdmin();
	abstract function requireSuperAdmin();
	abstract function requireLocalhost();
}

?>
