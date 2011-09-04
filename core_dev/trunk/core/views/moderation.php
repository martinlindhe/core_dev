<?php

//STATUS: early, nonfunctional

//TODO: ability to process a object (detailed view)

require_once('ModerationObject.php');
require_once('YuiDatatable.php');

if (!$session->isSuperAdmin)
    return;

if ($this->child)
{
    echo '<h1>Moderate object # '.$this->child.'</h1>';

    $o = ModerationObject::get($this->child);

    d($o);

    return;
}

echo '<h1>Moderation queue</h1>';


$list = ModerationObject::getUnhandled();

//d( $list );

$dt = new YuiDatatable();
$dt->addColumn('owner',    'Owner', 'link', relurl('coredev/view/manage_user/'), 'name');

$dt->addColumn('id',           'Id', 'link', relurl('coredev/view/moderation/handle/'), 'name');
$dt->addColumn('type',         'Type', 'array', getModerationTypes() );
$dt->addColumn('time_created', 'Created');
$dt->addColumn('data',         'Data');
$dt->addColumn('data2',        'Data2');

$dt->setDataList( $list );

echo $dt->render();

?>
