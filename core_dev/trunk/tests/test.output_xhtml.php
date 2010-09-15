<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('output_xhtml.php');

if (xmlentities('jag < tror > ibland & att " det blir bra') != 'jag &amp;lt; tror &amp;gt; ibland &amp; att &amp;quot; det blir bra') echo "FAIL 1\n";

?>
