<?php
//IMPORTANT: the current directory must contain a symlink to core_dev/trunk base directory
$coredev_inc = readlink(dirname(__FILE__).'/core_dev').'/core/';
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/inc'. PATH_SEPARATOR . $coredev_inc);

$config['debug'] = true;


//core_dev includes:
require_once('core.php');
require_once('XmlDocumentHandler.php');
require_once('SqlFactory.php');
require_once('ErrorHandler.php');
require_once('SessionHandler.php');
require_once('XhtmlMenu.php');

//project includes:
require_once('functions_process.php');



$page = XmlDocumentHandler::getInstance();
$page->designHead( dirname(__FILE__).'/design_head.php');
$page->designFoot( dirname(__FILE__).'/design_foot.php');
$page->setUrl('http://processtest.x/');
$page->setCoreDevInclude($coredev_inc); ///XXX peka på "/path/to/core_dev/core/" katalogen, hör egentligen inte till page handlern men den hör inte till något bra objekt... separat core-dev handler????
$page->setApplicationRoot();

$db = SqlFactory::factory('mysql', true); // enable profiler
SqlHandler::addInstance($db); //registers the created database connection as the one to use by SqlHandler

//$db->setConfig( array('host' => 'process1.x:44000', 'database' => 'dbProcess', 'username' => 'ml', 'password' => 'nutana') );
$db->setConfig( array('host' => 'localhost:44308', 'database' => 'dbProcess2', 'username' => 'root', 'password' => 'pl0gRupp4') );

$page->enableProfiler();

$locale = LocaleHandler::getInstance();
$locale->set('swe');

$session = SessionHandler::getInstance();
$session->setName('savakID');
$session->setTimeout( 60*60*24*2 );        //keep logged in for 2 days!
$session->setEncryptKey('sdcu7cw897cwhwihwiuh#zaixx7wsxh3hdzsddFDF4ex1g');
$session->allowLogins(true);
$session->allowRegistrations(false);


$page->setUploadRoot('/devel/projects/process/uploads/'); //XXX ska denna verkligen vara i page objektet... ?

$header = XhtmlHeader::getInstance();
$header->setCoreDevRoot('core_dev/');
//$header->setFavicon('favicon.png');
$header->setTitle('process server');
$header->includeCss('css/site.css');
//$header->includeCss('core_dev/css/core.css');

?>
