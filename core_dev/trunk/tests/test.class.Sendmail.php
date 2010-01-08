<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

die('XXX: sendmail is working so test is disabled /martin');

require('core.php');
require('class.Sendmail.php');

$config['debug'] = true;


$msg ='abc åäö 123';

$mail = new Sendmail('mail.unicorn.se');
$mail->setFrom('test@unicorn.se', 'TEST');
$mail->setReplyTo('noreply@unicorn.se');

$arr = array('martin@unicorn.se', 'ml@unicorn.se');
$mail->addRecipients($arr);

$mail->setSubject('message åäö subject');
$mail->send($msg);
$mail->close();

?>
