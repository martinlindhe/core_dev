<?php

//STATUS: wip

//TODO: ability to attach project-specific admin pages here

require_once('UserList.php');
require_once('UserGroupList.php');
require_once('IconWriter.php');
require_once('FileInfo.php');

switch ($this->view) {
case 'error':
    $header->setTitle( t('Error message') );
    echo $error->render(true);
    echo ahref('./', t('Continue') );
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
    // XXX FIXME: handle w & h parameters inside FileInfo to resize images
    echo FileInfo::passthru($this->owner);
    die;


case 'reset_password':
    // allows users who lost their password to reset it by following a email-link to this view
    $view = new ViewModel('views/session_reset_pwd.php');
    $view->registerVar('token', $this->owner);
    echo $view->render();
    break;

case 'admin':
    $session->requireSuperAdmin();

    echo '<h1>core_dev admin</h1>';
    echo ahref('coredev/view/manage_users', 'Manage users').'<br/>';
    echo ahref('coredev/view/manage_usergroups', 'Manage user groups').'<br/>';
    echo '<br/>';
    echo ahref('coredev/view/phpinfo', 'phpinfo()').'<br/>';
    echo ahref('coredev/view/compatiblity', 'Compatibility check').'<br/>';
    echo ahref('coredev/view/timezones', 'Time zones').'<br/>';
    echo ahref('coredev/view/currency', 'Currencies').'<br/>';
    break;

case 'view':
    // view built in view. owner = name of view in core/views/

    //XXX FIXME: make sure $this->owner only contains a-z and underscore

    $file = $page->getCoreDevInclude().'views/'.$this->owner.'.php';
    if (!file_exists($file))
        throw new Exception ('DEBUG: view not found '.$file);

    $view = new ViewModel($file);
    $view->registerVar('owner', $this->child);
    echo $view->render();
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

default:
    throw new Exception ('no such view: '.$this->view);
}

?>
