<?php

require_once('/var/www/core_dev/core/core.php');
require_once('/var/www/core_dev/core/atom_guid.php');

$hex = GUIDtoHEX('3F2504E0-4F89-11D3-9A0C-0305E82C3301');
if ($hex != 'E004253F894FD3119A0C0305E82C3301') echo "FAIL1\n";

?>
