<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('core.php');
require('class.Sendmail.php');

$config['debug'] = true;

$mail = new Sendmail('mail.startwars.org', 'martintest@startwars.org', 'test111');	//postfix

$mail->setFrom('martin@startwars.org', "Måartin Lindhe");
$mail->setReplyTo("noreply@unicorn.se", "iåenget svar");

$mail->setSubject('message åäö subject');
$mail->addRecipient('martin@unicorn.se');

/*
$mail->embed('delicious.png', 'pic_name');
$mail->attach('delicious.png');
*/

//$mail->setHTML(true);

$msg ='abc åäö 123';

//$msg = '<h1>HEJ HEJ HEJ</h1><br><br><img src="cid:pic_name"><font color="red">lalala</font>';

$mail->send($msg);

?>

