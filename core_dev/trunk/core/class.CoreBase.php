<?php
/**
 * $Id$
 *
 * Base class, all objects should extend from this class
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

class CoreBase
{
	private $debug = false;
	private $error = '';    ///< error string

	function getDebug() { return $this->debug; }
	function setDebug($bool = true) { $this->debug = $bool; }

	function getError() { return $this->error; }
	function setError($s) { $this->error = $s; }
}

?>
