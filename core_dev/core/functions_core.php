<?
/**
 * $Id$
 *
 * Functions assumed to always be available
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

	/**
	 * Debug function. Prints out variable $v
	 *
	 * \param $v variable of any type to display
	 * \return nothing
   */
	function d($v)
	{
		if (is_string($v)) echo htmlentities($v);
		else {
			if (extension_loaded('xdebug')) var_dump($v);	//xdebug's var_dump is awesome
			else {
				echo '<pre>';
				print_r($v);
				echo '</pre>';
			}
		}
	}

	/**
	 * Helper function to include core function files
	 *
	 * \param $file filename to include
	 */
	function require_core($file)
	{
		global $config;
		require_once($config['core_root'].'core/'.$file);
	}

	/* loads all active plugins */
	function loadPlugins()
	{
		global $config;

		if (empty($config['plugins'])) return;

		foreach ($config['plugins'] as $plugin) {
			require_once($config['core_root'].'plugins/'.$plugin.'/plugin.php');
		}
	}

?>