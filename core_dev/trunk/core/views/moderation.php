<?php

//STATUS: early, nonfunctional

//TODO: ability to process a object (detailed view)

require_once('ModerationObject.php');
require_once('YuiDatatable.php');

if (!$session->isSuperAdmin)
    return;

echo '<h1>Moderation queue</h1>';


$list = ModerationObject::getUnhandled();

//d( $list );

$dt = new YuiDatatable();
$dt->addColumn('owner',    'Owner', 'link', relurl('coredev/view/manage_user/'), 'name');

$dt->addColumn('id',           'Id');
$dt->addColumn('type',         'Type', 'array', getModerationTypes() );
$dt->addColumn('time_created', 'Created');
$dt->addColumn('data',         'Data');
$dt->addColumn('data2',        'Data2');

$dt->setDataList( $list );

echo $dt->render();

?>
