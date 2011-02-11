<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: xxx

require_once('class.CoreList.php');

class CategoryList extends CoreList
{
    private $type;         ///< category type
    private $owner;        ///< owner id, the meaning depends on category type
    private $creator;

    function __construct($type)
    {
        global $h;
        if (!is_numeric($type)) return false;

        $this->type    = $type;
        $this->creator = $h->session->id;
    }

    function setOwner($id)
    {
        if (!is_numeric($id)) return false;
        $this->owner = $id;
        $this->init(); /// XXX remove hack
    }

    /**
     * Initializes the list from database
     */
    function init()
    {
        global $db;

        $q  = 'SELECT * FROM tblCategories WHERE categoryType='.$this->type.' ';
        if ($this->owner) $q .= 'AND ownerId='.$this->owner;

        $list = $db->getArray($q);
        foreach ($list as $row) {
            $cat = new CategoryItem($this->type);
            $cat->setId($row['categoryId']);
            $cat->setTitle($row['categoryName']);
            $cat->setOwner($row['ownerId']);
            $cat->setPermissions($row['permissions']);
            $cat->setCreator($row['creatorId']);
            $cat->TimeCreated = new Timestamp($row['timeCreated']);

            $this->addItem($cat);
        }
    }

}

?>
