<?php

//STATUS: wip

require_once('ModerationObject.php');
require_once('UserFinder.php');
require_once('YuiDatatable.php');

if (!$session->isSuperAdmin)
    return;

if ($this->child)
{
    $o = ModerationObject::get($this->child);
//    d($o);

    if (isset($_GET['approve']) || isset($_GET['deny']))
    {
        $o->handled_by = $session->id;
        $o->time_handled = sql_datetime( time() );
        ModerationObject::store($o);

        if (!isset($_GET['approve']))
            redir('coredev/view/moderation');

        switch ($o->type) {
        case MODERATE_CHANGE_USERNAME:
            if (UserFinder::byUsername($o->data))
                return;

            // perform the username switch
            UserHandler::setUsername($o->owner, $o->data);
            break;

        // marking item approved is all needed
        case MODERATE_UPLOAD:
            break;

        default:
            throw new Exception ('Unhandled ModerationObject type '.$o->type);
        }

        redir('coredev/view/moderation');
    }

    echo '<h1>Moderate object # '.$this->child.'</h1>';

    switch ($o->type) {
    case MODERATE_CHANGE_USERNAME:
        $u = User::get($o->owner);
        echo '<h2>'.$u->name.' wants to change username to '.$o->data.'</h2>';

        if (UserFinder::byUsername($o->data))
            echo 'Username is taken!<br/><br/>';
        else
            echo ' &raquo; '.ahref('?approve', 'Approve').'<br/>';

        echo '<br/>';
        echo ' &raquo; '.ahref('?deny', 'Deny').'<br/>';
        break;

    case MODERATE_UPLOAD:
        $u = User::get($o->owner);
        echo '<h2>'.$u->name.' needs file # '.$o->data.' approved.</h2>';

        echo ' &raquo; '.ahref('?approve', 'Approve').'<br/>';

        echo '<br/>';
        echo ' &raquo; '.ahref('?deny', 'Deny').'<br/>';
        break;
    default:
        throw new Exception ('Unhandled ModerationObject type '.$o->type);
    }

    return;
}

echo '<h1>Moderation queue</h1>';


$list = ModerationObject::getUnhandled();

//d( $list );

$dt = new YuiDatatable();
$dt->addColumn('id',           'Id',    'link', 'coredev/view/moderation/handle/', 'name');
$dt->addColumn('owner',        'Owner', 'link', 'coredev/view/manage_user/', 'name');
$dt->addColumn('type',         'Type',  'array', getModerationTypes() );
$dt->addColumn('time_created', 'Created');
$dt->addColumn('data',         'Data');
$dt->addColumn('data2',        'Data2');
$dt->setDataList( $list );

echo $dt->render();

?>
