<?php
/**
 *
 */

//STATUS: experimenting

class Duration
{
	private $duration; ///< internal representation of a duration, in seconds

	/**
	 * @param $n initialize object to a duration, in seconds
	 */
	function __construct($n = 0)
	{
		$this->duration = $n;
	}

	function set($s)
	{
		$this->duration = decodeDuration($s);
	}

	function get() { return $this->duration; }

	function asSeconds() { return $this->get(); }

	function asMilliseconds() { return $this->duration * 1000; }

	/**
	 * @return "4:37:11" h:m:s...
	 */
	function render()
	{
		return formatDuration( $this->duration );
	}
}

?>
