<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('atom_guid.php');

$hex = GUIDtoHEX('3F2504E0-4F89-11D3-9A0C-0305E82C3301');
if ($hex != 'E004253F894FD3119A0C0305E82C3301') echo "FAIL 1\n";

?>
