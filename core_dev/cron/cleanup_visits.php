<?
/**
 * Cleans up old entries in tblVisits
 */

define('MAX_LIFETIME', 30 );	//delete entries older than 30 days

require_once('/home/ml/dev/pc/config.php');	//FIXME: no hardcoded path

$q = 'DELETE FROM tblVisits WHERE timeCreated <= DATE_SUB(NOW(),INTERVAL '.MAX_LIFETIME.' DAY)';
$db->delete($q);

?>
