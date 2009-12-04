<?

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('functions_phones.php');

if (!ValidMobilNr('46737308872')) echo "FAIL 1\n";
if (!ValidMobilNr('0737308872')) echo "FAIL 2\n";
?>
