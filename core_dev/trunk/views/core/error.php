<?php

$header->setTitle( t('Error message') );
echo $error->render(true);
echo ahref('u/login', 'Log in').'<br/>';
echo ahref('./', 'Go to start page').'<br/>';

?>
