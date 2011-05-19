<div id="header">
    <div id="header-logo">
        process site
    </div>
    <div id="header-items">
    </div>
</div>
<div id="leftmenu">
<?php

$menu = new XhtmlMenu();
$menu->setCss('nav_menu', 'nav_menu_current');
$menu->add('Home', '/');

if ($session->id) {
    $menu->add('Uploads',       'uploads/show');
    $menu->add('Work queue',    'queue/show');
    $menu->add('Upload file',   'uploads/new');
    $menu->add('Add to queue',  'queue/add');
}

if ($session->isSuperAdmin) {
    $menu->add('Process queue', 'queue/process');
    $menu->add('Admin',         '/admin');
}

if ($session->id) {
    $menu->add('Logout', '?logout');
}

echo $menu->render();

?>
</div>

<div id="middle">
