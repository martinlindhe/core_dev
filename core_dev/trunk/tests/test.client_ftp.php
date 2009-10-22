<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('client_ftp.php');

//normal anon ftp:
$url = 'ftp://ftp.sunet.se/pub/os/Linux/distributions/slackware/slackware-10.2/';

/*
//normal ftp med konto:
$url = 'ftp://username:password@ftp.server.com/';



//FTPES: explicit SSL/TLS
//XXX "kan" fungera. problem att prova mot localhost
//XXX2: blir error 35: Unknown SSL protocol error in connection to host:port
$url = 'ftpes://user:pwd@host:port/';

//sftp: ftp over ssh
//XXX requires curl built with "--with-libssh2", wont work with ubuntu packages (9.04 or 9.10)
$url = 'sftp://user:pass@host:port/path/file.xx';
*/


$f = new ftp($url);
//$f->setDebug(true);
//$x = $f->get($url); echo $x;die;

/*
$str = 'kalle anka blabla hahaha';
$f->putData('/home/ml/KALLE.ANKA', $str);
*/

$dir = $f->getDir();

foreach ($dir as $file) {
	if ($file['is_file']) {
		//echo $file['filename']."\n";
		$get = $file['filename'];

		$data = $f->getData($get);
		d($data);
	}
}


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
