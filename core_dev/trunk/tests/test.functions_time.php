<?

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('functions_time.php');

if (num_days('2010-03-04', '2010-03-04') !=  1) echo "FAIL 1\n";
if (num_days('2010-03-04', '2010-03-06') !=  3) echo "FAIL 2\n";
if (num_days('2010-01-01', '2010-02-24') != 55) echo "FAIL 3\n";
if (num_days('2010-03-01', '2010-03-31') != 31) echo "FAIL 4\n";

?>
