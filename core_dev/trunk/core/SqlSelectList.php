<?php
/**
 * $Id$
 *
 * Select wrapper for use with e.g. paginator back-end of yui_datatable
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

abstract class SqlSelectList
{
    private $owner;
    private $child;
    private $sort_column;
    private $sort_order;
    private $result_limit;
    private $result_idx    = 0;
    private $columns = array();

    protected $table_name;
    protected $owner_name;
    protected $child_name;

    function __construct($owner, $child)
    {
        $this->setOwner($owner);
        $this->setChild($child);
    }

    function setOwner($n) { if (is_numeric($n)) $this->owner = $n; }
    function setChild($s) { $this->child = $s; }

    /**
     * Specify which columns to include in the result-set
     */
    function setColumns($arr) { $this->columns = $arr; }

    /**
     * How many results to return
     */
    function setResultRows($n) { if (is_numeric($n)) $this->result_limit = $n; }

    /**
     * Index of results to return
     */
    function setResultIndex($n) { if (is_numeric($n)) $this->result_idx = $n; }

    //XXX require to be a registered column!
    function sortBy($s) { $this->sort_column = $s; }

    function sortOrder($s)
    {
        $s = strtoupper($s);
        if (!in_array($s, array('DESC', 'ASC')))
            throw new Exception ('Bad sortOrder');

        $this->sort_order = $s;
    }

    function getTotalCount()
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT COUNT(*) FROM '.$this->table_name.' WHERE '.$this->owner_name.'='.$this->owner;
        //XXX FIXME: auto-escape depending on value of $this->child
        $q .= ' AND '.$this->child_name.'="'.$db->escape($this->child).'"';

        return $db->getOneItem($q);
    }

    function get()
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT '.implode(',', $this->columns).' FROM '.$this->table_name.' WHERE '.$this->owner_name.'='.$this->owner;
        $q .= ' AND '.$this->child_name.'="'.$db->escape($this->child).'"';

        if ($this->sort_column)
            $q .= ' ORDER BY '.$this->sort_column.' '.$this->sort_order;

        if ($this->result_limit)
            $q .= ' LIMIT '.$this->result_idx.','.$this->result_limit;

        return $db->getArray($q);
    }
}

?>
