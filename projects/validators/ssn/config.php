<?php
//IMPORTANT: the current directory must contain a symlink to core_dev/trunk base directory
set_include_path(get_include_path() . PATH_SEPARATOR . readlink(dirname(__FILE__).'/core_dev').'/core/');

error_reporting(E_ALL);

require_once('functions_general.php');
require_once('validate_ssn.php');
require_once('functions_textformat.php');
require_once('xhtml_header.php');

$config['debug'] = true;
?>
