<?php

if (!$session->id)
    return;

$session->logout();

?>
