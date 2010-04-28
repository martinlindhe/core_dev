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

require_once('CsvWriter.php');

class XhrResponse
{
    private $data;
    private $format; //XXX allow other formats

    function __construct($format = 'json')
    {
        if (!in_array($format, array('json', 'csv')))
            throw new Exception('Unsupported XhrResponse format '.$format);

        $this->format = $format;
    }

    function setData($arr) { $this->data = $arr; }
    function setTotalRecords($n) { if (is_numeric($n)) $this->total_records = $n; }

    function render()
    {
        switch ($this->format) {
        case 'json': return $this->renderJson();
        case 'csv':  return $this->renderCsv();
        }
    }

    function renderJson()
    {
        $res = array(
        'totalRecords' => $this->total_records, //total results available: mapped to js var in yui_datatable
        'records'      => $this->data,          //mapped to js var in yui_datatable
        //'pageSize'     => count($this->data), //results returned ... unused???
        //'firstResultPosition'   => 1,    //???
        );
        return json_encode($res);
    }

    function renderCsv()
    {
        $page = XmlDocumentHandler::getInstance();
        $page->setMimeType('text/plain');
        $page->sendAttachment('export.csv');

        $writer = new CsvWriter();
        $writer->setData($this->data);
        return $writer->render();
    }
}

?>
