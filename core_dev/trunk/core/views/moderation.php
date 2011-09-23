<?php

//STATUS: wip

require_once('ModerationObject.php');
require_once('UserFinder.php');
require_once('YuiDatatable.php');

if (!$session->isSuperAdmin)
    return;

if (!$this->owner)
    $this->owner = 'default';

switch ($this->owner) {
case 'default':
    echo '<h1>Unhandled items in Moderation queue</h1>';

    $list = ModerationObject::getUnhandled();

    //d( $list );

    $dt = new YuiDatatable();
    $dt->addColumn('id',           'Id',    'link', 'coredev/view/moderation/handle/', 'name');
    $dt->addColumn('owner',        'Owner', 'link', 'coredev/view/manage_user/', 'name');
    $dt->addColumn('type',         'Type',  'array', getModerationTypes() );
    $dt->addColumn('time_created', 'Created');
    $dt->addColumn('data',         'Data');
    $dt->addColumn('reference',    'Reference');
    $dt->setDataList( $list );
    echo $dt->render();

    echo '<br/>';
    echo '&raquo; '.ahref('coredev/view/moderation/approved', 'Show approved objects').'<br/>';
    echo '&raquo; '.ahref('coredev/view/moderation/denied', 'Show denied objects').'<br/>';
    break;

case 'approved':
    echo '<h1>Approved items in Moderation queue</h1>';

    $list = ModerationObject::getApproved();
    //d( $list );

    $dt = new YuiDatatable();
    $dt->addColumn('id',           'Id' ); //,    'link', 'coredev/view/moderation/handle/', 'name');
    $dt->addColumn('owner',        'Owner', 'link', 'coredev/view/manage_user/', 'name');
    $dt->addColumn('type',         'Type',  'array', getModerationTypes() );
    $dt->addColumn('time_created', 'Created');
    $dt->addColumn('time_handled', 'Approved');
    $dt->addColumn('handled_by',   'Approved by', 'link', 'coredev/view/manage_user/', 'name');
    $dt->addColumn('data',         'Data');
    $dt->addColumn('reference',    'Reference');
    $dt->setDataList( $list );
    echo $dt->render();
    break;

case 'denied':
    echo '<h1>Denied items in Moderation queue</h1>';

    $list = ModerationObject::getDenied();
    //d( $list );

    $dt = new YuiDatatable();
    $dt->addColumn('id',           'Id' ); //,    'link', 'coredev/view/moderation/handle/', 'name');
    $dt->addColumn('owner',        'Owner', 'link', 'coredev/view/manage_user/', 'name');
    $dt->addColumn('type',         'Type',  'array', getModerationTypes() );
    $dt->addColumn('time_created', 'Created');
    $dt->addColumn('time_handled', 'Denied');
    $dt->addColumn('handled_by',   'Denied by', 'link', 'coredev/view/manage_user/', 'name');
    $dt->addColumn('data',         'Data');
    $dt->addColumn('reference',    'Reference');
    $dt->setDataList( $list );
    echo $dt->render();
    break;


case 'handle':
    if (!$this->child)
        die('SADFGFG');

    $o = ModerationObject::get($this->child);
//    d($o);

    if (isset($_GET['approve']) || isset($_GET['deny']))
    {
        $o->handled_by = $session->id;
        $o->time_handled = sql_datetime( time() );
        $o->approved = isset($_GET['approve']) ? 1 : 0;
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
        echo '<h2>Moderate file # '.$o->data.'</h2>';

        if ($o->owner) {
            $u = User::get($o->owner);
            echo 'Uploaded by '.$u->name;
        }

        $view = new ViewModel('views/file_details.php');
        $view->registerVar('owner', $o->data);
        echo $view->render();

        echo ' &raquo; '.ahref('?approve', 'Approve').'<br/>';

        echo '<br/>';
        echo ' &raquo; '.ahref('?deny', 'Deny').'<br/>';
        break;
    default:
        throw new Exception ('Unhandled ModerationObject type '.$o->type);
    }
    break;

default:
    echo 'No handler for view '.$this->owner;
}




?>
