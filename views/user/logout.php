<?php

namespace cd;

if (!$session->id)
    return;

$session->logout();

?>
