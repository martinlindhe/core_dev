<?php

header('HTTP/1.1 404 Not Found');

$header->setTitle( $_SERVER['REDIRECT_URL'].' not found' );

echo '<h1>core_dev/ The page '.$_SERVER['REDIRECT_URL'].' does not exist</h1>';

if (!empty($_SERVER['REDIRECT_QUERY_STRING']))
    echo 'Additionaly, these parameters was sent to the request: '.$_SERVER['REDIRECT_QUERY_STRING'].'<br/>';

?>
