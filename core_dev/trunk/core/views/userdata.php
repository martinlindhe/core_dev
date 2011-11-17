<?php

//TODO: ability to delete userdata field

//TODO: abiltiy to mark a userdata field as required at registration (such as email)

require_once('UserDataField.php');
require_once('YuiDatatable.php');

$session->requireSuperAdmin();

switch ($this->owner) {
case 'list':
    echo '<h1>Existing userdata fields</h1>';

    $list = UserDataField::getAll();

    $dt = new YuiDatatable();
    $dt->addColumn('id',    'Name', 'link', 'iview/userdata/edit/', 'name');
    $dt->addColumn('label', 'Label');
    $dt->addColumn('type',  'Type', 'array', UserDataField::getTypes() );

    $dt->setDataSource( $list );
    echo $dt->render();

    echo '<br/>';
    echo '&raquo; '.ahref('iview/userdata/new', 'Create new field');
    break;

case 'new':

    function newSubmit($p)
    {
        $f = new UserDataField();
        $f->name = $p['name'];
        $f->type = $p['type'];
        $f->label = $p['label'];
        $id = UserDataField::store($f);

        if ($f->type == UserDataField::RADIO)
            js_redirect('iview/userdata/edit/'.$id);
        else
            js_redirect('iview/userdata/list');
    }

    echo '<h1>New userdata field</h1>';

    $form = new XhtmlForm();
    $form->addInput('name', 'Name');
    $form->addInput('label', 'Label');
    $form->addDropdown('type', 'Type', UserDataField::getTypes() );

    $form->addSubmit('Create');
    $form->setHandler('newSubmit');
    echo $form->render();
    break;

case 'edit':
    // child = field id

    function editSubmit($p)
    {
        $f = UserDataField::get($p['id']);
        $f->name  = $p['name'];
        $f->label = $p['label'];
        $f->type  = $p['type'];
        $id = UserDataField::store($f);

        if ($f->type == UserDataField::RADIO)
            for ($i=1; $i<6; $i++)
                if (!empty($p['opt'.$i]))
                    UserDataFieldOption::set($id, 'opt'.$i, $p['opt'.$i]);

        js_redirect('iview/userdata/list');
    }

    echo '<h1>Edit userdata field</h1>';

    $field = UserDataField::get($this->child);

    $form = new XhtmlForm();
    $form->addHidden('id', $field->id);  /// XXX  hack!
    $form->addInput('name', 'Name', $field->name);
    $form->addInput('label', 'Label', $field->label);
    $form->addDropdown('type', 'Type', UserDataField::getTypes(), $field->type );

    if ($field->type == UserDataField::RADIO) {

        for ($i=1; $i<6; $i++) {
            $opt = 'opt'.$i;
            $val = UserDataFieldOption::get($field->id, $opt);

            $form->addInput($opt, 'Option '.$i, $val);
        }
    }

    $form->addSubmit('Save');
    $form->setHandler('editSubmit');
    echo $form->render();
    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
