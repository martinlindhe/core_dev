<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('SendMail.php');


// external MTA example

/*
$sendmail = SendMail::getInstance();
$sendmail->useMta(true);

$sendmail->addRecipient('martin@ubique.se');
$sendmail->setSubject('subj åöl');
$sendmail->send('helo world');
*/



// Gmail example
$mail = SendMail::getInstance();
//$mail->setDebug(true);
$mail->setServer('smtp.gmail.com');
$mail->setUsername('gmail-username');
$mail->setPassword('password');
$mail->setPort(587); // TLS/STARTTLS

$mail->setFrom('martin@unicorn.se', 'martin testar');
$mail->setReplyTo('noreply@unicorn.se');

$mail->addRecipients(array('martin@unicorn.se','martin@startwars.org'));

if (count($mail->getRecipients()) != 2) echo "FAIL 1\n";



$mail->setSubject('message åäö subject');
//$mail->attachFile('/home/ml/Desktop/bilder/167968_193900863954437_140855739258950_730662_4411055_n.jpg');

$msg ='abc åäö 123';
$mail->send($msg);
