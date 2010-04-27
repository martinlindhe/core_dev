<?php
/**
 * $Id$
 *
 * XmlHttpRequest (XHR) backend base class
 *
 * Implements the required sorting & selection methods for a data list
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

abstract class XhrBackend
{
    protected $sort_column;
    protected $sort_order;
    protected $result_limit;
    protected $result_idx    = 0;
    protected $columns       = array();
    protected $period_from, $period_to;

    /**
     * Specify which columns to include in the result-set
     */
    function addColumn($m)
    {
        if (is_alphanumeric($m))
            $this->columns[] = $m;
        else if (is_array($m))
            foreach ($m as $s)
                if (is_alphanumeric($s))
                    $this->columns[] = $s;
    }

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

    /**
     * Index of results to return
     */
    function setResultIndex($n) { if (is_numeric($n)) $this->result_idx = $n; }

    //XXX require to be a registered column!
    function sortBy($s)
    {
        if (!is_alphanumeric($s))
            return false;

        $this->sort_column = $s;
    }

    function sortOrder($s)
    {
        $s = strtoupper($s);
        if (!in_array($s, array('DESC', 'ASC')))
            throw new Exception ('Bad sortOrder');

        $this->sort_order = $s;
    }

}

?>
