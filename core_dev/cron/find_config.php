<?php
/**
 * $Id: find_config.php 3925 2008-08-27 09:41:44Z ml $
 */

$project = '../../';

require_once($project.'config.php');

set_include_path($config['core']['fs_root'].'core/');
require_once('functions_forum.php');
restore_include_path();
?>
