<?php
/**
 * $Id$
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

	/**
	 * __set() is run when writing data to inaccessible properties.
	 */
	public function __set($name, $value)
	{
		if (!isset($this->$name))
			throw new Exception ($name." property does not exist");
	}
}

?>
