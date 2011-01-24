<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('files.php');




if (arg_match( 'file123.jpg', array('file*.jpg') ) !== true)         echo "FAIL 1\n";
if (arg_match( 'test-filename.jpg', array('test-*') ) !== true)      echo "FAIL 2\n";
if (arg_match( 'test-filename.jpg', array('test-*.jpg') ) !== true)  echo "FAIL 3\n";

if (arg_match( 'filname ending with.php', array('*.php' )) !== true) echo "FAIL 4\n";
if (arg_match( 'file.jpg', array('*.gif', '*.jpg') ) !== true)       echo "FAIL 5\n";
if (arg_match( 'file.bmp', array('*.gif', '*.jpg') ) === true)       echo "FAIL 6\n";




$x = expand_arg_files('/var/log/boot.log', array('*.log') );
if (count($x) != 1 || $x[0] != '/var/log/boot.log') echo "FAIL x\n";

$x = expand_arg_files('/home/ml/dev/core_dev/tests/test.files.php', array('*.php') );
if (count($x) != 1 || $x[0] != '/home/ml/dev/core_dev/tests/test.files.php') echo "FAIL x\n";



$tests = dir_get_matches('.', array('test*.php') );




/*
$x = expand_arg_files('/media/downloads/dump/V.2009.S02E0*.avi');
d($x);
*/


/*
$x = expand_arg_files('/var/log', array('*.log', '*.err') );
d( $x );
*/

?>
