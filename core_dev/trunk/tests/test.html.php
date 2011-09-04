<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('html.php');

if (htmlchars_decode('ja&nbsp;ha') != 'ja ha')  echo "FAIL 1\n"; // space char is a special NBSP character
if (htmlchars_decode('reg&reg;me') != 'reg®me') echo "FAIL 2\n";


$html =
'hello '.
'<style>.box h1 {text-align:left;}</style>'.
'<style type="text/css">.xxx</style>'.
'<script>strip me</script>'.
'<script language="text/javascript">strip me</script>'.
'world';

if (strip_html($html) != 'hello world') echo "FAIL 3\n";

if (relurl('/') != '/')       echo "FAIL 4\n";
if (relurl('?val') != '?val') echo "FAIL 5\n";

?>
