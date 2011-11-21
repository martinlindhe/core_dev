<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('core.php');
require('SendMail.php');




$mail = SendMail::getInstance();
$mail->setServer('mail.unicorn.se');
$mail->setFrom('test@unicorn.se', 'TEST');
$mail->setReplyTo('noreply@unicorn.se');




$msg ='abc åäö 123';


//$arr = array('martin@unicorn.se', 'ml@unicorn.se');
$arr = 'martin@unicorn.se, ml@unicorn.se kalle@ba.com';
$mail->addRecipients($arr);

if (count($mail->getRecipients()) != 3) echo "FAIL 1\n";



$mail->setSubject('message åäö subject');
$mail->attachFile('/home/ml/Desktop/bilder/167968_193900863954437_140855739258950_730662_4411055_n.jpg');
$mail->send($msg);



//---


$mail = SendMail::getInstance();
$mail->addRecipients('ml@unicorn.se');


$mail->attachFile('/home/ml/Desktop/Screenshot.png');
$mail->send($msg);


?>
