<?php
/**
 * $Id$
 */

$project = '../../';

$project = '/var/www/m2w/operator/';

require_once($project.'config.php');

set_include_path($config['core']['fs_root'].'core/');
require_once('functions_forum.php');
require_once('functions_statistics.php');
restore_include_path();
?>
