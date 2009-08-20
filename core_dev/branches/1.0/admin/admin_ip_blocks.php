<?php
/**
 * $Id$
 *
 * Lists all blocked ips
 */

require_once('find_config.php');
$h->session->requireSuperAdmin();

if (!empty($_GET['remove'])) {
	//remove ip block
	removeBlock(BLOCK_IP, $_GET['remove']);
}

require('design_admin_head.php');

$list = getBlocks(BLOCK_IP);
echo '<h1>IP blocks</h1>';

echo '<table>';
echo '<tr>';
echo '<th width="200">IP</th>';
echo '<th width="90">Time</th>';
echo '<th width="50">Admin</th>';
echo '<th width="50">&nbsp;</th>';
echo '</tr>';
foreach ($list as $row) {
	echo '<tr>';
	$ip = GeoIP_to_IPv4($row['rule']);
	echo '<td>'.$ip.'</td>';
	echo '<td>'.ago($row['timeCreated']).'</td>';
	echo '<td>'.Users::getName($row['createdBy']).'</td>';
	echo '<td><a href="'.$_SERVER['PHP_SELF'].'?remove='.urlencode($row['rule']).'""><img src="'.$config['core']['web_root'].'gfx/icon_error.png" alt="Remove block" title="Remove block"></a></td>';
	echo '</tr>';
}
echo '</table>';

require('design_admin_foot.php');

?>
