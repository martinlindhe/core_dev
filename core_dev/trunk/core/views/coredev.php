<?php

//STATUS: wip

//TODO: ability to attach project-specific admin pages here

require_once('UserList.php');
require_once('UserEditor.php');
require_once('UserGroupList.php');
require_once('FtpClient.php'); // for curl_check_protocol_support()
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

case 'admin':
    $session->requireSuperAdmin();

    switch ($this->owner) {
    case 'userlist':
        echo UserList::render();
        break;

    case 'useredit': //child=user id
        // XXX link to here is hardcoded in admin_UserList.php view
        $useredit = new UserEditor();
        $useredit->setId($this->child);
        echo $useredit->render();
        break;

    case 'usergroup':
        $grouplist = new UserGroupList();
        echo $grouplist->render();
        break;

    case 'usergroup_details': //child=group id
        // XXX link to here is hardcoded in admin_UserGroupList.php
        $details = new UserGroup($this->child);
        echo $details->render();
        break;

    case 'timezones':
        //XXX put this into a view
        echo '<h1>Time zones</h1>';

        echo 'Server time: '.date('r').'<br/>';
        echo 'Server timezone: '.date_default_timezone_get().' ('.date('T').')<br/>';
        echo '<br/>';

        //XXX ability to show some common timezones

        echo 'Browser time: <span id="js_time"></span><br/>';
        echo 'Browser timezone offset: <span id="js_timezone"></span><br/>';

        $header->embedJs(
        'function get_js_time() {'.
            'var d = new Date();'.
            'e = document.getElementById("js_time");'.
            'e.innerHTML = d.toUTCString();'.
            'e = document.getElementById("js_timezone");'.
            'e.innerHTML = d.getTimezoneOffset();'.
        '}');
        $header->embedJsOnload('get_js_time();');
        break;

    case 'phpinfo':
        phpinfo();
        break;

    case 'compatiblity':
        echo '<h1>Compatiblity check</h1>';

        echo 'PHP version: '.PHP_VERSION;
        if (php_min_ver('5.2'))
            echo ' OK';
        else
            echo ' ERROR - php 5.2 or newer required';
        echo '<br/>';
        echo '<br/>';

        echo '<h2>Extensions</h2>';
        echo 'Curl: '.(function_exists('curl_init') ? 'OK' : 'NOT FOUND').'<br/>';
        echo 'GD2: '.(function_exists('imagegd2') ? 'OK' : 'NOT FOUND').'<br/>';
        echo 'APC: '.(function_exists('apc_cache_info') ? 'OK' : 'NOT FOUND').'<br/>';  // useful for cassandra + general speedups
        echo '<br/>';

        echo '<h2>Configuration</h2>';
        echo 'Curl: sftp support '.(curl_check_protocol_support('sftp') ? 'OK' : 'NOT FOUND').'<br/>';

        echo '<br/>';
        break;

    default:
        echo '<h1>core_dev admin</h1>';
        echo ahref('coredev/admin/userlist', 'Manage users').'<br/>';
        echo ahref('coredev/admin/usergroup', 'Manage user groups').'<br/>';
        echo '<br/>';
        echo ahref('coredev/admin/phpinfo', 'phpinfo()').'<br/>';
        echo ahref('coredev/admin/compatiblity', 'Compatibility check').'<br/>';
        echo ahref('coredev/admin/timezones', 'Time zones').'<br/>';
        break;
    }
    break;

case 'view':
    // view built in view. owner = name of view in core/views/

    $file = $page->getCoreDevInclude().'views/'.$this->owner.'.php';
    if (!file_exists($file))
        throw new Exception ('DEBUG: view not found '.$file);

    $view = new ViewModel($file);
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
