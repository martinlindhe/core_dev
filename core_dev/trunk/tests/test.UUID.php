<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('UUID.php');


$v3 = UUID::v3('514d2ee9-58ed-49ef-a592-1a49c268e2a2', 'test crap 123');
if ($v3 != "19d179a2-9511-3595-a8e2-490b981e92c2") echo "FAIL 1\n";

$v5 = UUID::v5('514d2ee9-58ed-49ef-a592-1a49c268e2a2', 'test crap 123');
if ($v5 != "f1b18651-7633-5945-8527-853ddc5b6393") echo "FAIL 2\n";


$v4 = UUID::v4();


echo "UUID v3 (md5):  ".$v3."\n";
echo "UUID v5 (sha1): ".$v5."\n";
echo "UUID v4 (rand): ".$v4."\n";

?>
