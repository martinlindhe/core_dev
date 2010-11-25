<?php
/**
 * $Id$
 *
 * XmlHttpRequest (XHR) response generator
 *
 * Used by YuiDatatable
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
    private $format;

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
        'totalRecords' => $this->total_records, //total results available, mapped in YuiDatatable, YuiAutocomplete
        'records'      => $this->data,          //mapped in YuiDatatable
        );

        //attaches sql debug in the response
        $db = SqlHandler::getInstance();

        if ($db instanceof DatabaseMysqlProfiler) {
            $res['db'] = array(
            'queries'    => $db->queries,
            'time_spent' => $db->time_spent,
            'errors'     => $db->query_error,
            );
        }

        // creates a js snippet which adds the json code as a parameter to named callback function, used for YuiAutocomplete
        if (!empty($_GET['callback']))  // example: YAHOO.util.ScriptNodeDataSource.callbacks[0]        XXX regexp validate string
            return $_GET['callback'].'('.json_encode($res).');';

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
