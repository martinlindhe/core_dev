<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('core.php');
require('client_pop3.php');

//$config['debug'] = true;

$mail = new pop3('mail.startwars.org', 'martintest@startwars.org', 'test111');

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
