<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('output_js.php');

// test escaping of non-numeric keys
if (jsArray1D(array('5-1' => 'a b c')) != '{"5-1":"a b c"}') echo "FAIL 1\n";

if (jsArray1D(array('012345' => 'abc')) != '{"012345":"abc"}') echo "FAIL 2\n";

?>
