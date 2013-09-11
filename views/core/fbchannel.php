<?php
// required for facebook login, see http://developers.facebook.com/docs/reference/javascript/FB.init/ as to why

namespace cd;

$page->disableDesign(); //remove XhtmlHeader, designHead & designFoot for this request
$page->setMimeType('text/html');
// If your application is https, your channelUrl must also be https
echo '<script src="'.$page->getScheme().'://connect.facebook.net/en_US/all.js"></script>';

?>
