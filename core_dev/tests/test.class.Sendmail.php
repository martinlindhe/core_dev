<?
require('/var/www/core_dev/core/functions_core.php');
require('/var/www/core_dev/core/class.Sendmail.php');

$config['debug'] = true;

$mail = new Sendmail('mail.startwars.org', 'martintest@startwars.org', 'test111');	//postfix

$mail->from('martin@startwars.org', "Martin Lindhe");
$mail->to('martin@unicorn.se');
$mail->reply_to("noreply@unicorn.se", "inget svar");
$mail->embed('delicious.png', 'pic_name');
$mail->attach('delicious.png');

$mail->html = true;

$msg = '<h1>HEJ HEJ HEJ</h1><br><br><img src="cid:pic_name"><font color="red">lalala</font>';

$mail->send('message subject', $msg);

?>
