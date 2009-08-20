<?php
die;

/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is WURFL PHP Libraries.
 *
 * The Initial Developer of the Original Code is
 * Andrea Trasatti.
 * Portions created by the Initial Developer are Copyright (C) 2004-2005
 * the Initial Developer. All Rights Reserved.
 *
 * ***** END LICENSE BLOCK ***** */

/*
 * $Id: update_cache.php,v 1.1 2005/04/16 16:04:21 atrasatti Exp $
 * $RCSfile: update_cache.php,v $ v2.1 beta2 (Apr, 16 2005)
 *
 * Author: Andrea Trasatti ( atrasatti AT users DOT sourceforge DOT net )
 * Multicache implementation: Herouth Maoz ( herouth AT spamcop DOT net )
 *
 */

/*
 *
 * This script should be called manually (CLI is suggested) to update the
 * multicache files when a new XML is availabled.
 *
 * KNOWN BUG: cache.php will be updated automatically, a race condition might
 * happen while generating the new files in the temporary directory and before
 * it's moved to the default path. A temporary cache file should be used along
 * contributions are welcome.
 *
 * More info can be found here in the PHP section:
 * http://wurfl.sourceforge.net/
 *
 * Questions or comments can be sent to
 * "Andrea Trasatti" <atrasatti AT users DOT sourceforge DOT net>
 *
 * Please, support this software, send any suggestion and improvement to me
 * or the mailing list and we will try to keep it updated and make it better
 * every day.
 *
 * If you like it and use it, please let me know or contact the wmlprogramming
 * mailing list: wmlprogramming@yahoogroups.com
 *
 */


/*
This code is modified by Martin Lindhe, based on code from wurfl_php_tools_21beta2.zip from the wurfl team,
so the orginal copyright above remains.
*/
set_time_limit(600);

$page_start = microtime(true); 

require_once('wurfl_config.php');
require_once('wurfl_parser.php');
define('FORCED_UPDATE', true);

$config['wurfl']['url'] = 'http://wurfl.sourceforge.net/wurfl.xml';
$config['wurfl']['xmlfile'] = WURFL_FILE;	//todo: get rid of WURFL_FILE


$download_start = microtime(true);
if (!file_exists($config['wurfl']['xmlfile']) || isset($_GET['forcedl'])) {
	echo 'Downloading '.$config['wurfl']['url'].' ...<br/>';
	$xml = file_get_contents($config['wurfl']['url']);

	file_put_contents($config['wurfl']['xmlfile'], $xml);
	echo 'Stored local copy to '.$config['wurfl']['xmlfile'].'<br/>';
}
$download_end = microtime(true);


$parse_start = microtime(true);
parse();
$parse_end = microtime(true);
echo 'Done updating cache<br/>';


$page_end = microtime(true);

$download_time = $download_end - $download_start;
$parse_time = $parse_end - $parse_start;
$page_time = $page_end - $page_start;

$other_time = $page_time - $download_time - $parse_time;

echo '<hr/>';
echo '<h1>Stats</h1>';
echo 'Download time: '.round($download_time, 3).' sec<br/>';

echo 'Parse time: '.round($parse_time, 3).' sec<br/>';
echo 'Other time: '.round($other_time, 3).' sec<br/>';
echo 'Total: '.round($page_time, 3).' sec<br/>';

echo '<br/>';
echo '<a href="?forcedl">Click here</a> to force a new download of '.$config['wurfl']['url'];
?>
