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
require_once('XlsWriter.php');

class XhrResponse
{
    private $total_records;
    private $data;
    private $format; //XXX allow other formats

    function __construct($format = 'json')
    {
        if (!in_array($format, array('json', 'csv', 'xls')))
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
        case 'xls':  return $this->renderXls();
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

        //attaches sql debug in the response
        $db = SqlHandler::getInstance();

        if ($db instanceof DatabaseMysqlProfiler) {
            $res['db'] = array(
            'queries' => $db->queries,
            'time_spent' => $db->time_spent,
            'errors' => $db->query_error,
            );
        }

        return json_encode($res);
    }

    function renderCsv()
    {
        $page = XmlDocumentHandler::getInstance();
        $page->setMimeType('text/csv');
        $page->sendAttachment('export.csv');

        $writer = new CsvWriter();
        $writer->setData($this->data);
        return $writer->render();
    }

    function renderXls()
    {
        $page = XmlDocumentHandler::getInstance();
        $page->setMimeType('application/vnd.ms-excel');
        $page->sendAttachment('export.xls');

        $writer = new XlsWriter();
        $writer->setData($this->data);
        return $writer->render();
    }
}

?>
