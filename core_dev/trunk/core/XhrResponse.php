<?php
/**
 * $Id$
 *
 * XmlHttpRequest (XHR) response generator
 *
 * Used by yui_datatable
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

class XhrResponse
{
    private $data;
    private $format; //XXX allow other formats

    function __construct($format = 'json')
    {
        if ($format != 'json')
            throw new Exception('Unsupported XhrResponse format '.$format);

        $this->format = $format;
    }

    function setData($arr) { $this->data = $arr; }
    function setTotalRecords($n) { if (is_numeric($n)) $this->total_records = $n; }

    function render()
    {
        $res = array(
        'totalRecords' => $this->total_records, //total results available: mapped to js var in yui_datatable
        'records'      => $this->data,          //mapped to js var in yui_datatable
        //'pageSize'     => count($this->data), //results returned ... unused???
        //'firstResultPosition'   => 1,    //???
        );
        return json_encode($res);
    }
}

?>
