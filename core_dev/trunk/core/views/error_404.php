<?php
header('HTTP/1.0 404 Not Found');

$header->setTitle( $_SERVER['REDIRECT_URL'].' not found' );

echo '<h1>The page '.$_SERVER['REDIRECT_URL'].' does not exist</h1>';

if (!empty($_SERVER['REDIRECT_QUERY_STRING']))
    echo 'Additionaly, these parameters was sent to the request: '.$_SERVER['REDIRECT_QUERY_STRING'].'<br/>';

if (!empty($_SERVER['REDIRECT_ERROR_NOTES'])) //XXX is never set, due to how we handle redirect???
    throw new Exception ('REDIRECT_ERROR_NOTES set to '. $_SERVER['REDIRECT_ERROR_NOTES'] );

?>
