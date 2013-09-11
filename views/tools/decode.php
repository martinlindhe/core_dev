<?php
/**
 * Utility to decode base64 strings
 */

namespace cd;

$session->requireSuperAdmin();

if (!$this->owner)
    $this->owner = 'default';

switch ($this->owner) {
case 'default':

    function onSubmit($p)
    {
        $error = ErrorHandler::getInstance();

        $res = base64_decode($p['data'], true);
        if ($res === false) {
            $error->add('Input is not base64 encoded');
            return false;
        }

        echo dh($res);
    }

    $form = new XhtmlForm();
    $form->addTextarea('data');
    $form->setFocus('data');
    $form->addSubmit('Analyze');
    $form->setHandler('onSubmit');
    echo $form->render();

    break;


default:
    throw new \Exception ('no such view: '.$this->owner);
}

?>
