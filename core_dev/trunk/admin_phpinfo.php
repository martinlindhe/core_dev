<?php
/**
 * $Id$
 */

require_once('find_config.php');
$h->session->requireSuperAdmin();

require('design_admin_head.php');

ob_start();
phpinfo();
$info = ob_get_contents();
ob_end_clean();

//hack to remove phpinfo()'s own CSS rules
$info = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $info);
echo $info;

require('design_admin_foot.php');
?>
