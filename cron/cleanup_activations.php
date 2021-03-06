<?php
/**
 * Cleans up old entries in tblActivation
 */

define('MAX_LIFETIME', 30);    //delete entries older than 30 days

require_once('find_config.php');

$q = 'DELETE FROM tblActivation WHERE timeCreated <= DATE_SUB(NOW(),INTERVAL '.MAX_LIFETIME.' DAY)';
$db->delete($q);

?>
