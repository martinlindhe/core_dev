<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('files.php');

$x = expand_arg_files('/media/media-server/downloads/dump', array('.mkv', '.avi') );
d($x);



$x = expand_arg_files('/etc/hosts');
d( $x );



?>
