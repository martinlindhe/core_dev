<?php
/**
 * $Id$
 *
 * Select wrapper for use with e.g. paginator back-end of yui_datatable
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

require_once('XhrBackend.php');

abstract class SqlSelectList extends XhrBackend
{
    private $owner;
    private $child;

    protected $table_name;
    protected $owner_name;
    protected $child_name;
    protected $timestamp_name;

    function __construct($owner, $child)
    {
        $this->setOwner($owner);
        $this->setChild($child);
    }

    function setOwner($n) { if (is_numeric($n)) $this->owner = $n; }
    function setChild($s) { $this->child = $s; }

    function getTotalCount()
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT COUNT('.$this->child_name.') FROM '.$this->table_name.' WHERE '.$this->owner_name.'='.$this->owner;

        if ($this->period_from)
            $q .= ' AND DATE('.$this->timestamp_name.') BETWEEN "'.sql_date($this->period_from).'" AND "'.sql_date($this->period_to).'"';

        //XXX FIXME: auto-escape depending on value of $this->child
        if ($this->child)
            $q .= ' AND '.$this->child_name.'="'.$db->escape($this->child).'"';

        return $db->getOneItem($q);
    }

    function get()
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT '.implode(',', $this->columns).' FROM '.$this->table_name.' WHERE '.$this->owner_name.'='.$this->owner;

        if ($this->period_from)
            $q .= ' AND DATE('.$this->timestamp_name.') BETWEEN "'.sql_date($this->period_from).'" AND "'.sql_date($this->period_to).'"';

        if ($this->child)
            $q .= ' AND '.$this->child_name.'="'.$db->escape($this->child).'"';

        if ($this->sort_column)
            $q .= ' ORDER BY '.$this->sort_column.' '.$this->sort_order;

        if ($this->result_limit)
            $q .= ' LIMIT '.$this->result_idx.','.$this->result_limit;

        return $db->getArray($q);
    }

}

?>
