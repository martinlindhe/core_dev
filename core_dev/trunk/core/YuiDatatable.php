<?php
/**
 * $Id$
 *
 * http://developer.yahoo.com/yui/datatable/
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: see if yui has money rounding code & use that instead of my formatMoney()

//TODO: Conditional row coloring: http://developer.yahoo.com/yui/examples/datatable/dt_row_coloring.html
//TODO: enable inline cell editing: http://developer.yahoo.com/yui/examples/datatable/dt_cellediting.html

require_once('output_js.php');
require_once('JSON.php');

class YuiDatatable
{
    private $columns         = array();
    private $response_fields = array();
    private $datalist        = array();
    private $caption         = ''; ///< caption for the datatable
    private $xhr_source      = ''; ///< url to retrieve data from XMLHttpRequest
    private $rows_per_page   = 20; ///< for the paginator
    private $sort_column;
    private $sort_order;
    private $embed_arrays    = array(); ///< array with strings for substitution of numeric values in some columns

    private $pixel_width;                 ///< if set, forces horizontal scrollbar on the datatable
    private $pixel_height;                ///< if set, forces vertical scrollbar on the datatable

    function setCaption($s) { $this->caption = $s; }
    function setRowsPerPage($n) { $this->rows_per_page = $n; }
    function setWidth($n) { $this->pixel_width = $n; }
    function setHeight($n) { $this->pixel_height = $n; }

    /**
     * Adds a hidden column to the dataset, needed to embed data in the table linked to other cells, see $col_label param for addColumn()
     */
    private function addHiddenColumn($key)
    {
        if (!$key) return;
        $this->columns[] = array('key' => $key, 'hidden' => true);
        $this->response_fields[] = $key;
    }

    /**
     * Configure initial sort order. If unset, defaults to first column, ascending
     *
     * @param $col sort by column name
     * @param $order asc,desc
     */
    function setSortOrder($col, $order)
    {
        if (!in_array($order, array('asc', 'desc')))
            throw new Exception ('bad sort order: '.$order);

        foreach ($this->columns as $idx => $c)
            if ($c['key'] == $col)
                $this->sort_column = $idx;

        $this->sort_order = $order;
    }

    /**
     * @param $key column key
     * @param $label column label
     * @param $type render column as this type of data
     * @param $extra extra-data for column type (for "link" its url prefix)
     * @param $col_label use a different cell content for the label of this cell
     */
    function addColumn($key, $label, $type = '', $extra = '', $col_label = '')
    {
        $arr = array('key' => $key, 'label' => $label, 'sortable' => true);
        $response = array('key' => $key);
        //$arr['resizable'] = true;   //disabled by default

        if (!$type && substr($key, 0, 4) == 'time')
            $type = 'time';

        if (!$type && substr($key, 0, 4) == 'date')
            $type = 'date';

        if (!$type)
            $type = 'text';

        switch ($type) {
        case 'text':
            $arr['maxAutoWidth'] = 600;
            break;

        case 'link':
            $arr['formatter']  = 'formatLink';
            $arr['extra_data'] = $extra;
            $arr['col_label']  = $col_label;
            $this->addHiddenColumn($col_label);
            break;

        case 'date':
            $arr['formatter']  = 'formatDate';
            //$response['parser'] = 'date';  //XXX js-date dont like mysql date format???
            break;

        case 'time':
            $arr['formatter']  = 'formatTime';
            //$response['parser'] = 'date';  //XXX js-date dont like mysql date format???
            break;

        case 'money':
            $arr['formatter']  = 'formatMoney';
            break;

        case 'bool':
            $arr['formatter']  = 'formatBool';
            break;

        case 'array':
            //"extra" contains an array of string representations of this column's values
            $arr['formatter'] = 'formatArray'.count($this->embed_arrays);
            $this->embed_arrays[] = $extra;
            break;

        default: throw new Exception('Unknown column type '.$type);
        }

        $this->response_fields[] = $key;
        $this->columns[] = $arr;
    }

    /**
     * Loads the datatable with a static array of data
     * Only includes registered array keys
     * Cannot be used with setDataSource()
     */
    function setDataList($arr)
    {
        if (!is_array($arr))
            throw new Exception ('YuiDatatable->setDataList() needs an array');

        $res = array();

        foreach ($arr as $row)
        {
            $inc_row = array();
            foreach ($row as $key => $val)
                foreach ($this->columns as $inc_col)
                    if ($inc_col['key'] == $key)
                        $inc_row[$key] = $val;

            $res[] = $inc_row;
        }

        $this->datalist = $res;
    }

    /**
     * Configures the datatable to load data from a callback url
     * Cannot be used with setDataList()
     */
    function setDataSource($url) { $this->xhr_source = $url; }

    function render()
    {
        if (!$this->columns)
            throw new Exception ('no columns');

        $header = XhtmlHeader::getInstance();

        $header->includeCss('http://yui.yahooapis.com/2.8.2r1/build/paginator/assets/skins/sam/paginator.css');
        $header->includeCss('http://yui.yahooapis.com/2.8.2r1/build/datatable/assets/skins/sam/datatable.css');

        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/connection/connection-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/datasource/datasource-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/element/element-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/paginator/paginator-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/datatable/datatable-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/json/json-min.js');

        $div_holder = 'yui_dt'.mt_rand();
        $data_var   = 'yui_dt_data'.mt_rand();
        $pag_holder = 'yui_pag'.mt_rand();

        $res =
        'YAHOO.util.Event.addListener(window, "load", function() {'.
            'YAHOO.example.Basic = function() {'.

                /**
                 * elLiner reference to parent cell
                 * oRecord reference to current row, read other column value with oRecord.getData("key_name")
                 * oColumn reference to current column (pointer to a row in myColumnDefs)
                 * oData is the cell data
                 */
                'this.formatLink = function(elLiner, oRecord, oColumn, oData) {'.
                    'if (!oData) return;'.
                    'var prefix = oColumn["extra_data"];'.
                    'var col_label_name = oColumn["col_label"];'.
                    'var col_label = oRecord._oData[col_label_name];'.
                    'elLiner.innerHTML = "<a href=\"" + prefix + oData + "\">" + (col_label ? col_label : oData) + "</a>";'.
                '};'.

                //oData cell data "YYYY-MM-DD HH:MM:SS"
                'this.formatDate = function(elLiner, oRecord, oColumn, oData) {'.
                    'if (!oData) return;'.
                    'var a1 = oData.substr(0,10).split("-");'.
                    'elLiner.innerHTML = a1[0]+"-"+a1[1]+"-"+a1[2];'.
                '};'.

                'this.formatTime = function(elLiner, oRecord, oColumn, oData) {'.
                    'if (!oData) return;'.
                    'var a1 = oData.substr(0,10).split("-");'.
                    'var a2 = oData.substr(11,8).split(":");'.
                    'elLiner.innerHTML = a1[0]+"-"+a1[1]+"-"+a1[2]+" "+a2[0]+":"+a2[1]+":"+a2[2];'.
                '};'.

                //oData cell data is value in lowest representation (cents, öre)
                'this.formatMoney = function(elLiner, oRecord, oColumn, oData) {'.
                    'var val = Number(oData) / 100;'.
                    'elLiner.innerHTML = val.toFixed(2);'.
                '};'.

                // draws a gray (false) or green (true) checkmark
                'this.formatBool = function(elLiner, oRecord, oColumn, oData) {'.
                    'elLiner.innerHTML = \'<img src="'.relurl('core_dev/gfx/icon_ok').'\' + ( oData ? "" : "_gray") + \'.png"/>\';'.
                '};';

                for ($i=0; $i<count($this->embed_arrays); $i++) {
                    $res .=
                    'this.formatArray'.$i.' = function(elLiner, oRecord, oColumn, oData) {'.
                        'var a='.jsArray1D($this->embed_arrays[$i]).';'."\n".
                        'elLiner.innerHTML = a[oData];'.
                    '};'.
                    'YAHOO.widget.DataTable.Formatter.formatArray'.$i.' = this.formatArray'.$i.';';
                }

                $res .=
                // Add the custom formatter to the shortcuts
                'YAHOO.widget.DataTable.Formatter.formatLink = this.formatLink;'.
                'YAHOO.widget.DataTable.Formatter.formatDate = this.formatDate;'.
                'YAHOO.widget.DataTable.Formatter.formatTime = this.formatTime;'.
                'YAHOO.widget.DataTable.Formatter.formatMoney = this.formatMoney;'.
                'YAHOO.widget.DataTable.Formatter.formatBool = this.formatBool;'.

                'myColumnDefs = '.jsArray2D($this->columns).';'."\n".
                ($this->xhr_source ?
                    //rpc
                    'var myDataSource = new YAHOO.util.DataSource("'.$this->xhr_source.'");'.
                    'myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;'.
                    //'myDataSource.connXhrMode = "queueRequests";'. //XXX ???
                    'myDataSource.responseSchema = {'.
                        'resultsList: "records",'.
                        'fields: '.JSON::encode($this->response_fields, false).','.
                        'metaFields: { totalRecords:"totalRecords" }'. // mapped to XhrResponse "totalRecords" field, needed for paginator
                    '};'
                :
                    //embedded js-array
                    'var '.$data_var.' = '.jsArray2D($this->datalist).';'."\n".
                    'var myDataSource = new YAHOO.util.DataSource('.$data_var.');'.
                    'myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;'.
                    'myDataSource.responseSchema = { fields:'.JSON::encode($this->response_fields, false).'};'
                ).

                'var myConfigs = {'.
                    'caption:"'.$this->caption.'",'.
                    ($this->pixel_width  ? 'width:"'.$this->pixel_width.'px",' : '').
                    ($this->pixel_height ? 'height:"'.$this->pixel_height.'px",' : '').
                    ($this->sort_order ?
                        'sortedBy: {'.
                            'key:"'.$this->columns[ $this->sort_column ]['key'].'",'.
                            'dir:YAHOO.widget.DataTable.'.($this->sort_order == 'asc' ? 'CLASS_ASC' : 'CLASS_DESC').
                        '},'
                    :
                        ''
                    ).
                    'paginator: new YAHOO.widget.Paginator({'.
                        (!$this->xhr_source ? 'totalRecords:'.count($this->datalist).',' : '').
                        'rowsPerPage:'.$this->rows_per_page.','.
                        'rowsPerPageOptions:['.implode(',', array(10, 15, 20, 25, 50, 75, 100, 250, 500, 1000) ).'],'.
                        'containers:["'.$pag_holder.'"],'.
                        'template:"{FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink} &nbsp; {CurrentPageReport} {RowsPerPageDropdown} per page",'.
                        'pageReportTemplate:"Showing items {startRecord} - {endRecord} of {totalRecords}",'.
                    '}),'.
                    ($this->xhr_source ?
                        'dynamicData:true,'.
                        'initialRequest:"sort='.$this->columns[ $this->sort_column ]['key'].($this->sort_order ? '&dir='.$this->sort_order : '').'&startIndex=0&results='.$this->rows_per_page.'"' // Initial request for first page of data
                    :
                        ''
                    ).
                '};';

                $tbl_type = ($this->pixel_width || $this->pixel_height) ? 'ScrollingDataTable' : 'DataTable';

                $res .=
                'myDataTable = new YAHOO.widget.'.$tbl_type.'("'.$div_holder.'",myColumnDefs, myDataSource, myConfigs);'.

                ($this->xhr_source ?
                    // Update totalRecords on the fly with value from the XHR request, needed for paginator
                    'myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {'.
                        'oPayload.totalRecords = oResponse.meta.totalRecords;'.
                        'return oPayload;'.
                    '};'
                :
                    ''
                ).

                // Enable row highlighting, more examples: http://developer.yahoo.com/yui/examples/datatable/dt_highlighting.html
                'myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);'.
                'myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);'.

                'return {'.
                    'oDS: myDataSource,'.
                    'oDT: myDataTable'.
                '};'.
            '}();'.
        '});';

        return
        '<div id="'.$pag_holder.'"></div>'.
        '<div id="'.$div_holder.'"></div>'.js_embed($res);
    }

}

?>
