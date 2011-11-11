<?php

//STATUS: wip

//TODO: ability to attach project-specific admin pages here

require_once('UserList.php');
require_once('UserGroupList.php');
require_once('IconWriter.php');
require_once('File.php');
require_once('ImageResizer.php');

switch ($this->view) {
case 'error':
    $header->setTitle( t('Error message') );
    echo $error->render(true);
    echo ahref('iview/login', 'Log in').'<br/>';
    echo ahref('./', 'Go to start page').'<br/>';
    break;

case 'selftest':
    // returns a error code if anything is wrong
    // the idea is to have a external script fetch http://server/coredev/selftest
    // and warn if result != "STATUS:OK"
    $status = 'OK';

    $dir = $page->getUploadRoot();
    if ($dir) {
        if (!is_dir($dir))
            $status = 'ERROR: upload dir dont exist';
        else if (!is_writable($dir))
            $status = 'ERROR: upload dir not writable';

        // low disk space in upload directories?
        $df = disk_free_space($dir);
        if ($df < 1024 * 1024 * 32) // 32mb
            $status = 'ERROR: not enough space in upload dir';
    }

    die('STATUS:'.$status);

case 'file':
    // passes thru a file
    echo File::passthru($this->owner);
    return;

case 'image':
    // passes thru a image (with optional width & height specified)

    $name = File::getUploadPath($this->owner);

    if (!empty($_GET['w']) && !empty($_GET['h'])) {
        $im = new ImageResizer($name);
        $im->resizeAspect($_GET['w'], $_GET['h']);
    } else {
        $im = new Image($name);
    }
    $im->render();

    return;

case 'reset_password':
    // allows users who lost their password to reset it by following a email-link to this view
    $view = new ViewModel('views/session_reset_pwd.php');
    $view->registerVar('token', $this->owner);
    echo $view->render();
    break;

case 'admin':
    $session->requireSuperAdmin();

    echo '<h1>core_dev admin</h1>';
    echo ahref('iview/moderation', 'Moderation queue').'<br/>';
    echo '<br/>';
    echo ahref('iview/manage_users', 'Manage users').'<br/>';
    echo ahref('iview/manage_usergroups', 'Manage user groups').'<br/>';
    echo ahref('iview/manage_files', 'Manage files').'<br/>';
    echo ahref('iview/reserved_words', 'Reserved words').'<br/>';
    echo '<br/>';
    echo ahref('iview/phpinfo', 'phpinfo()').'<br/>';
    echo ahref('iview/compatiblity', 'Compatibility check').'<br/>';
    echo ahref('iview/timezones', 'Time zones').'<br/>';
    echo ahref('iview/currency', 'Currencies').'<br/>';
    echo '<br/>';
    echo ahref('iview/mysql_config', 'MySQL information').'<br/>';
    echo '<br/>';
    echo ahref('iview/chatrooms/list', 'Chatrooms').'<br/>';
    echo '<br/>';
    echo ahref('iview/userdata/list', 'Userdata types').'<br/>';
    break;

case 'robots':
    $page->disableDesign(); //remove XhtmlHeader, designHead & designFoot for this request
    $page->setMimeType('text/plain');
    echo "User-agent: *\n";
    echo "Disallow: /\n";
    break;

case 'favicon':
    //XXX TODO only force Microsoft Icon render for IE browsers

    $page->disableDesign(); //remove XhtmlHeader, designHead & designFoot for this request
    $page->setMimeType('image/vnd.microsoft.icon');

    $f = $page->getApplicationRoot().$header->getFavicon();
    if (!file_exists($f))
        throw new Exception ('favicon.ico generation failed, file not found '.$f);

    $temp = TempStore::getInstance();
    $key = 'favicon//'.$f;

    $data = $temp->get($key);
    if ($data) {
        echo $data;
        break;
    }

    $im = new IconWriter();
    $im->addImage($f);
    echo $im->create();

    $temp->set($key, $data, '24h');
    break;

case 'fbchannel':
    // required for facebook login, see http://developers.facebook.com/docs/reference/javascript/FB.init/ as to why
    $page->disableDesign(); //remove XhtmlHeader, designHead & designFoot for this request
    $page->setMimeType('text/html');
    // If your application is https, your channelUrl must also be https
    echo '<script src="'.$page->getScheme().'://connect.facebook.net/en_US/all.js"></script>';
    return;

default:
    throw new Exception ('no such view: '.$this->view);
}

?>
