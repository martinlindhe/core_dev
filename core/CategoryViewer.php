<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2011 <martin@ubique.se>
 */

//STATUS: xxx

//TODO: add some ui category management (edit, remove)

namespace cd;

class CategoryViewer extends CoreBase
{
    var $Cat; //CategoryList object

    function __construct($type, $owner)
    {
        $this->Cat = new CategoryList($type);
        $this->Cat->setOwner($owner);
    }

    function renderList()
    {
        $res = '';
        $this->Cat->init();

        foreach ($this->Cat->getKeyVals() as $id => $name)
            $res .= ', <a href="?cat='.$id.'">'.$name.'</a>';

        return '<a href="?cat=0">'.t('Overview').'</a>, '.substr($res, 2);
    }

}

?>
