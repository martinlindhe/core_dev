<?

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('functions_phones.php');


if (formatMSID('0707308763', '46') != '46707308763') echo "FAIL 1\n";

?>
