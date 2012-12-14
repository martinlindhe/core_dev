<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('Password.php');

if (Password::encrypt('salt1', 'salt2', 'test', 'sha512') != 'sha512:84955637ddfc24c7f70b390c52a2a5fec0a02c9e3f34811772563547db18fbaf529f977af3fa59d4a818bfade14a9c04cadda1b3d53a3a0d9790794ef18f1e4d')    echo "FAIL 1\n";

if (Password::isForbidden('imnotforbidden'))       echo "FAIL 10\n";
if (!Password::isForbidden('password'))            echo "FAIL 11\n";
if (!Password::isForbidden(' password '))          echo "FAIL 12\n";

?>
