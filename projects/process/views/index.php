<?php

if (!$session->id) {
    echo $session->renderLoginForm();
    return;
}

//FIXME re-implement SOAP interface
if (!defined('SOAP_1_2')) {
    echo '<div class="critical">php_soap extension is not loaded! This application will not function properly</div>';
}

?>
