<?php
/**
 * Shows detailed information of all connected memcached servers, which is used by the TempStore class
 */

namespace cd;

$tempstore_div = 'tss_'.mt_rand();

echo ' | '.ahref_js('cache', "return toggle_el('".$tempstore_div."')").' ';

$temp = TempStore::getInstance();

$css =
'display:none;'.
'overflow:auto;'.
'padding:4px;'.
'border:#000 1px solid;';

echo '<div id="'.$tempstore_div.'" style="'.$css.'">';

require('redis.php');
require('memcached.php');

echo '</div>';
