<?php
/**
 * $Id$
 */

$project = '../../';

require_once($project.'config.php');

set_include_path($config['core']['fs_root'].'core/');
require_once('functions_forum.php');
restore_include_path();
?>
