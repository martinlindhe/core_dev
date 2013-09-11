<?php

namespace cd;

if (!$session->isAdmin)
    return;

echo '<h1>Time zones</h1>';

echo 'Server time: '.date('r').'<br/>';
echo 'Server timezone: '.date_default_timezone_get().' ('.date('T').')<br/>';
echo '<br/>';

//XXX ability to show some common timezones

echo 'Browser time: <span id="js_time"></span><br/>';
echo 'Browser timezone offset: <span id="js_timezone"></span><br/>';

$header->embedJs(
'function get_js_time() {'.
    'var d = new Date();'.
    'e = document.getElementById("js_time");'.
    'e.innerHTML = d.toUTCString();'.
    'e = document.getElementById("js_timezone");'.
    'e.innerHTML = d.getTimezoneOffset();'.
'}');
$header->embedJsOnload('get_js_time();');

?>
