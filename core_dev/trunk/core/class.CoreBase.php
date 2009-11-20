<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

/**
 * core_dev base class, all objects should extend this class
 */
class CoreBase
{
	protected $debug = false;
	protected $error = '';    ///< error string

	function setDebug($bool = true) { $this->debug = $bool; }

	function setError($s) { $this->error = $s; }
	function getError() { return $this->error; }
}

?>
