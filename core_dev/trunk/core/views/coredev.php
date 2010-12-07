<?php

//STATUS: wip

//TODO: ability to attach project-specific admin pages here

require_once('UserEditor.php');
require_once('UserGroupList.php');
require_once('FtpClient.php'); // for curl_check_protocol_support()

switch ($this->view) {
case 'admin':
    $session->requireSuperAdmin();

    switch ($this->owner)
    {
    case 'userlist':
        $userlist = new UserList();
        echo $userlist->render();
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

default:
    throw new Exception ('DEBUG: no such view '.$this->view);
}

?>
