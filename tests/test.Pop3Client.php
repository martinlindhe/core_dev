<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('core.php');
require('Pop3Client.php');

die('XXX: cant test pop3 client easily. the script worked last time tested');

$mail = new Pop3Client('mail.startwars.org', 'martintest@startwars.org', 'test111');

/**
 * If this function returns true, the mail is deleted from the server
 */
function mailCallback($attachments)
{
    foreach ($attachments as $a) {
        echo $a['mimetype'].": ";
        if (!empty($a['filename'])) {
            echo $a['filename']."\n";
            file_put_contents('pop3-'.$a['filename'], $a['body']);
        } else {
            echo $a['body']."\n";
        }
    }
    return false;
}

$mail->getMail('mailCallback');

?>
