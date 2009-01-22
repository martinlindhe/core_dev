<?php
/**
 * $Id$
 *
 * Locale functions for multi-language support
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

require_once('locale_se.php');

/**
 * Translates strings into other languages
 */
function t($s)
{
	global $config;
	if (empty($config['language'])) return $s;

	switch ($config['language']) {
		case 'en': return $s;		//English (System default)
		case 'se': return t_se($s);	//Swedish
		default: die('Unhandled language: '.$config['language']);
	}
}

?>
