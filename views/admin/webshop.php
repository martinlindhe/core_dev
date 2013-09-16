<?php
/**
 * This is the webshop manager
 */

namespace cd;

$session->requireAdmin();

switch ($this->owner) {
case 'category':
    // show sub categories & items
    // TODO show items in current category also
    // child = category id

    $category_id = 0;
    if (is_numeric($this->child))
        $category_id = $this->child;

    if ($category_id) {
        $cat = WebShopCategory::get($category_id);

        echo '<h1>Manage webshop - category '.$cat->name.'</h1>';
        echo ahref('a/webshop/editcategory/'.$category_id, 'Edit').'<br/>';

    } else {
        echo '<h1>Manage webshop</h1>';
    }

    $list = WebShopCategory::getAllByOwner($category_id);

    if ($list) {
        echo '<h2>Sub-categories</h2>';
        foreach ($list as $cat) {
            echo ahref('a/webshop/category/'.$cat->id, $cat->name).'<br/>';
        }
    }

    $items = WebShopItem::getAllByOwner($category_id);
    if ($items) {
        echo '<h2>Items in this category</h2>';
        foreach ($items as $item) {
            echo ahref('a/webshop/item/'.$item->id, $item->name.' ('.$item->inventory.' in stock)').'<br/>';
        }
    }

    echo '<br/>';
    echo ahref('a/webshop/newitem/'.$category_id, 'Add item to this category').'<br/>';
    echo ahref('a/webshop/newcategory/'.$category_id, 'Create sub-category here').'<br/>';

    // TODO: if category has no sub categories & no items: allow DELETE

    break;

case 'editcategory':
    // child = category id

    function editCategoryHandler($p)
    {
        $x = WebShopCategory::get($p['id']);
        if (!$x)
            die('EOEOWP');
        $x->name = $p['name'];
        $x->id = $x->store();

        js_redirect('a/webshop/category/'.$x->id);
    }

    $cat = WebShopCategory::get($this->child);
    if (!$cat)
        die('EWPP');

    echo '<h1>Edit webshop category</h1>';


    $x = new XhtmlForm();
    $x->addHidden('id', $cat->id);
    $x->addInput('name', 'Name', $cat->name);
    $x->setFocus('name');

    $x->addSubmit('Save');
    $x->setHandler('editCategoryHandler');
    echo $x->render();

    break;

case 'newcategory':
    // child = parent id

    function newCategoryHandler($p)
    {
        $x = new WebShopCategory();
        $x->name = $p['name'];
        $x->owner = $p['owner'];
        $x->store();

        js_redirect('a/webshop/category/'.$x->id);
    }

    echo '<h1>Add new webshop category</h1>';

    $parent_id = 0;
    if (is_numeric($this->child))
        $parent_id = $this->child;

    $x = new XhtmlForm();
    $x->addHidden('owner', $parent_id);
    $x->addInput('name', 'Name');
    $x->setFocus('name');

    $x->addSubmit('Add');
    $x->setHandler('newCategoryHandler');
    echo $x->render();
    break;

case 'newitem':
    // child = parent id

    function newCategoryHandler($p)
    {
        // allows decimal point as both "," or "."
        $p['price'] = str_replace(',', '.', $p['price']);

        $x = new WebShopItem();
        $x->name = $p['name'];
        $x->owner = $p['owner'];
        $x->info = $p['info'];
        $x->price = $p['price'];
        $x->inventory = $p['inventory'];
        $x->store();

        js_redirect('a/webshop/category/'.$x->id);
    }

    $cat = WebShopCategory::get($this->child);
    if ($cat) {
        echo '<h1>Add new webshop item to category '.$cat->name.'</h1>';
    } else {
        echo '<h1>Add new webshop item to top level</h1>';
    }

    $parent_id = 0;
    if (is_numeric($this->child))
        $parent_id = $this->child;


    $x = new XhtmlForm();
    $x->addHidden('owner', $parent_id);
    $x->addInput('name', 'Name');
    $x->setFocus('name');

    $x->addTextarea('info', 'Info');
    $x->addInput('price', 'Price');
    $x->addInput('inventory', 'Inventory count');

    $x->addSubmit('Add');
    $x->setHandler('newCategoryHandler');
    echo $x->render();
    break;

case 'item':
    // child = item id

    $item = WebShopItem::get($this->child);
    if (!$item)
        die('WOPWEP');

    echo '<h1>Webshop item details - '.$item->name.'</h1>';

    $cat = WebShopCategory::get($item->owner);
    if ($cat)
        echo 'Category: '.ahref('a/webshop/category/'.$cat->id, $cat->name).'<br/>';

    echo 'Info: '.$item->info.'<br/>';
    echo 'Price: '.$item->price.'<br/>';
    echo 'In stock: '.$item->inventory.' pieces<br/>';
    echo '<br/>';

    $images = File::getByCategory(WEBSHOP_ITEM, $item->id);

    foreach ($images as $img) {
        $a = new XhtmlComponentA();
        $a->href = getThumbUrl($img->id, 0, 0);
        $a->target = "_blank";
        $a->content = showThumb($img->id, $img->name, 150, 150);
        echo $a->render();
        echo '<br/>';
    }
    echo '<br/>';

    echo ahref('a/webshop/edititem/'.$item->id, 'Edit item').'<br/>';
    echo ahref('a/webshop/additemimage/'.$item->id, 'Upload image').'<br/>';

    // TODO: allow *MARK AS DELETED* item, if stock = 0 ( need old data for references, so dont delete it!)

    break;

case 'edititem':
    // child = item id

    function editItemHandler($p)
    {
        // allows decimal point as both "," or "."
        $p['price'] = str_replace(',', '.', $p['price']);

        $x = WebShopItem::get($p['id']);
        $x->name = $p['name'];
        $x->info = $p['info'];
        $x->price = $p['price'];
        $x->inventory = $p['inventory'];
        $x->store();

        js_redirect('a/webshop/item/'.$x->id);
    }


    $item = WebShopItem::get($this->child);
    if (!$item)
        die('WOPWEP');

    echo '<h1>Webshop edit item - '.$item->name.'</h1>';

    $x = new XhtmlForm();
    $x->addHidden('id', $item->id);
    $x->addInput('name', 'Name', $item->name);
    $x->setFocus('name');

    $x->addTextarea('info', 'Info', $item->info);
    $x->addInput('price', 'Price', $item->price);
    $x->addInput('inventory', 'Inventory count', $item->inventory);

    $x->addSubmit('Save');
    $x->setHandler('editItemHandler');
    echo $x->render();

    // TODO: move item to another category
    break;

case 'additemimage':
    // child = item id

    // upload image & attach to item

    function uploadSubmit($p)
    {
        $fileId = File::importImage(WEBSHOP_ITEM, $p['f1'], $p['category'], false, 800, 600);

        if (!$fileId) {
            ErrorHandler::getInstance()->add('No file was uploaded');
            return false;
        }

        redir('a/webshop/item/'.$p['category']);
    }

    $item = WebShopItem::get($this->child);
    if (!$item)
        die('WOPWEP');

    echo '<h1>Upload image to item '.$item->name.'</h1>';

    $form = new XhtmlForm();
    $form->addHidden('category', $item->id);
    $form->addFile('f1', 'Image');
    $form->addSubmit('Upload');
    $form->setHandler('uploadSubmit');
    echo $form->render();
    break;

default:
    echo 'no such view: '.$this->owner;
}
