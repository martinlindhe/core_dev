<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2011 <martin@ubique.se>
 */

//STATUS: wip ... used by PollWidget where CategoryList is actually list of poll options

//XXX: rename to OptionList and OptionItem ??

//XXXX2: why do a list have a type? each item have types.. maybe useful to be able to mix types in a list?

namespace cd;

require_once('CategoryItem.php');

class CategoryList
{
    private $type;         ///< category type
    private $owner;        ///< owner id, the meaning depends on category type
    private $creator;

    protected $items = array(); ///< list of objects

    function __construct($type = 0)
    {
        if (!is_numeric($type))
            throw new \Exception ('non-numeric type');

        $this->type = $type;
    }

    function getItems() { return $this->items; }

    function getKeyVals()
    {
        $res = array();
        foreach ($this->items as $item) {
            if ($item instanceof CategoryItem)
                $res[ $item->getId() ] = $item->getTitle();
            else
                throw new \Exception ('CoreList->getKeyVals: cant handle object type '.get_class($item) );
        }
        return $res;
    }

    function addItem($i)
    {
        $this->items[] = $i;
    }

    function addItems($list)
    {
        foreach ($list as $e)
            $this->addItem($e);
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
        $db = SqlHandler::getInstance();

//XXX use SqlObject loading
        $q  = 'SELECT * FROM tblCategories WHERE categoryType='.$this->type.' ';
        if ($this->owner)
            $q .= 'AND ownerId='.$this->owner;

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
