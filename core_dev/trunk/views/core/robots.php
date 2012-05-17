<?php

$page->disableDesign(); //remove XhtmlHeader, designHead & designFoot for this request
$page->setMimeType('text/plain');
echo "User-agent: *\n";
echo "Disallow: /\n";

?>
