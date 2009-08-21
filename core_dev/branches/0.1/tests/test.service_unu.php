<?php

require_once('/var/www/core_dev/core/service_unu.php');

$x = 'http://tech.slashdot.org/story/09/06/02/134224/Internet-Explorer-6-Will-Not-Die';

if (unuShortURL($x) != 'http://u.nu/9ay8') echo 'FAIL 1';

?>
