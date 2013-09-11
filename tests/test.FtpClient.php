<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('FtpClient.php');

$x = new FtpClient();
$x->setAddress('sftp://username:passwd@hostname.com:12346/path/to/file');

if ($x->getUrl() != 'sftp://username:passwd@hostname.com:12346/path/to/file') echo "FAIL 1\n";

?>
