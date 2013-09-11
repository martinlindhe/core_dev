<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('CoreBase.php');
require_once('ImapReader.php');


// Google IMAP example
$mail = new ImapReader();
$mail->setServer('imap.gmail.com');
$mail->setPort(993); // SSL port
$mail->setUsername('xxx@gmail.com');
$mail->setPassword('pwd');
$mail->useSsl(true);


/**
 * @param $mails array of EMail objects
 * @param $caller reference to calling object
 */
function mailCallback($mails, $caller)
{
    foreach ($mails as $mail) {
        echo 'MAIL FROM SENDER '.$mail->from."\n";

        foreach ($mail->attachments as $a)
            d($a);
    }
}

$mail->getMail('mailCallback');
