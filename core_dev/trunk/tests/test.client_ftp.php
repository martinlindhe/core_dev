<?php
require_once('/var/www/core_dev/trunk/core/core.php');
require_once('/var/www/core_dev/trunk/core/client_ftp.php');

//normal anon ftp:
$url = 'ftp://ftp.sunet.se/';

//normal ftp med konto:
$url = 'ftp://username:password@ftp.server.com/';



//FTPES: explicit SSL/TLS
//XXX "kan" fungera. problem att prova mot localhost
//XXX2: blir error 35: Unknown SSL protocol error in connection to host:port
$url = 'ftpes://user:pwd@host:port/';



//sftp: ftp over ssh
//XXX requires curl built with "--with-libssh2", wont work with ubuntu packages (9.04 or 9.10)
$host = 'sftp://user:pass@host:port/path/file.xx';

$f = new ftp($host);
$f->setDebug(true);
//$x = $f->get($url); echo $x;die;

/*
$str = 'kalle anka blabla hahaha';
$f->putData('/home/ml/KALLE.ANKA', $str);
*/

$f->rename('/home/ml/KALLE.ANKA', '/home/ml/test.RENAMED');

//$dir = $f->dir(); print_r($dir);

die;

//$f->dir('/home/ml');
/*
if ($f->is_dir('/home/ml')) echo "i found home\n";
else echo "no found home\n";

*/





//$f->put('/home/ml/kent-pa-drift-320-kbit.mp3', '/home/ml/storfil-b');


//file_put_contents('x2', $f->get($url) );


//$data = $f->get($url);
//echo $data."\n";
//file_put_contents('test.7z', $data);

?>
