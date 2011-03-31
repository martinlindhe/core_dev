<?php
/**
 * $Id$
 *
 * XmlHttpRequest (XHR) backend base class
 *
 * Implements the required sorting & selection methods for a data list
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

abstract class XhrBackend
{
    protected $sort_column;
    protected $sort_order;
    protected $result_limit;
    protected $result_idx    = 0;
    protected $period_from, $period_to;

    /**
     * How many results to return
     */
    function setResultRows($n) { if (is_numeric($n)) $this->result_limit = $n; }

    /**
     * Specifies selection of entries over a time period (timestamp column is $timestamp_name)
     */
    function setPeriod($from, $to)
    {
        $from = ts($from);
        $to   = ts($to);
        if (!$from || !$to) return false;

        $this->period_from = $from;
        $this->period_to   = $to;
    }

    function getDateFromAsString() { return date('Y-m-d', $this->period_from); }
    function getDateToAsString() { return date('Y-m-d', $this->period_to); }

    /**
     * Index of results to return
     */
    function setResultIndex($n) { if (is_numeric($n)) $this->result_idx = $n; }

    //XXX require to be a registered column!
    function setSortOrder($s, $order = 'asc')
    {
        if (!is_alphanumeric($s))
            return false;

        $order = strtolower($order);
        if (!in_array($order, array('desc', 'asc')))
            throw new Exception ('Bad sortOrder');

        $this->sort_column = $s;
        $this->sort_order  = $order;
    }

    abstract function getTotalCount();
    abstract function get();
}

?>
