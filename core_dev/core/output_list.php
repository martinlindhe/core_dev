<?php
/**
 * Used by output_feed.php, output_playlist.php
 */

//TODO: use with table output code (and call it output_table.php)

class coredev_output_list
{
	/**
	 * Adds a array of entries to the feed list
	 */
	function addList($list)
	{
		foreach ($list as $entry)
			$this->entries[] = $entry;
	}

	/**
	 * Adds a entry to the feed list
	 */
	function addEntry($entry)
	{
		$this->entries[] = $entry;
	}

	function clearList()
	{
		//XXX implement
	}
}

?>
