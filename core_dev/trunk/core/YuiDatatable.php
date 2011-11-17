<?php
/**
 * $Id$
 *
 * http://developer.yahoo.com/yui/datatable/
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO WIP: finish conditional row coloring: http://developer.yahoo.com/yui/examples/datatable/dt_row_coloring.html
//          current code is half-working... multiple rules dont work well together, only integer comparisions???

//TODO: see if yui has money rounding code & use that instead of my formatMoney()
//TODO: enable inline cell editing: http://developer.yahoo.com/yui/examples/datatable/dt_cellediting.html
//TODO: right-align money column types

require_once('JSON.php');

class YuiColumnDef
{
    var $key;
    var $label;
    var $sortable;
    var $formatter;
    var $extra_data;
    var $col_label;
    var $maxAutoWidth;
    var $resizable;
}

class YuiColorRow
{
    var $c1, $c2;      // items to compare
    var $comparison;   ///   "=", "<", ">" etc
    var $css;
}


class YuiDatatable
{
    private $columns         = array();
    private $response_fields = array();

    private $data_source;                 ///< array or url to retrieve data from XMLHttpRequest

    private $caption         = '';        ///< caption for the datatable
    private $sort_column     = false;     ///< default to false, because first column idx is 0
    private $sort_order;
    private $embed_arrays    = array();   ///< array with strings for substitution of numeric values in some columns
    private $color_rows      = array();
    private $show_paginator = true;

    private $pixel_width;                 ///< if set, forces horizontal scrollbar on the datatable
    private $pixel_height;                ///< if set, forces vertical scrollbar on the datatable

    private $rows_per_page   = 20;        ///< default for the paginator
    private $rpp_opts = array(10, 15, 20, 25, 35, 50, 75, 100, 250, 500, 1000);  ///< available "show rows per page" options

    function setCaption($s) { $this->caption = $s; }

    function setRowsPerPage($n)
    {
        $this->rows_per_page = $n;

        $this->rpp_opts[] = $n;
        $this->rpp_opts = array_unique($this->rpp_opts);
        sort($this->rpp_opts);
    }

    function setWidth($n) { $this->pixel_width = $n; }
    function setHeight($n) { $this->pixel_height = $n; }

    function disablePaginator() { $this->show_paginator = false; }

    /**
     * Adds a hidden column to the dataset, needed to embed data in the table linked to other cells, see $col_label param for addColumn()
     */
    private function addHiddenColumn($key)
    {
        if (!$key) return;
        $col = new YuiColumnDef();
        $col->key = $key;
        $col->hidden = true;
        $this->columns[] = $col;
        $this->response_fields[] = $key;
    }

    /**
     * Configure initial sort order. If unset, defaults to first column, ascending
     *
     * @param $col sort by column name
     * @param $order asc,desc
     */
    function setSortOrder($col, $order = 'asc')
    {
        if (!in_array($order, array('asc', 'desc')))
            throw new Exception ('bad sort order: '.$order);

        $this->sort_column = false;

        foreach ($this->columns as $idx => $c)
            if ($c->key == $col)
                $this->sort_column = $idx;

        $this->sort_order = $order;

        if ($this->sort_column === false) {
//            d( $this->columns );
            throw new Exception ('column '.$col.' not found');
        }
    }

    function colorRow( $c1, $comp, $c2, $css)
    {
        if (!in_array($comp, array('==', '!=', '>=', '<=', '>', '<')))
            throw new Exception ('unhandled comparison method: '.$comp);

        $col = new YuiColorRow();
        $col->c1 = $c1;
        $col->comparison = $comp;
        $col->c2 = $c2;
        $col->css = $css;

        $this->color_rows[] = $col;
    }

    /**
     * @param $key column key
     * @param $label column label
     * @param $type render column as this type of data
     * @param $extra extra-data for column type (for "link" its url prefix)
     * @param $col_label use a different cell content for the label of this cell
     */
    function addColumn($key, $label, $type = '', $extra = '', $col_label = '', $max_width = '')
    {
        $response = array('key' => $key);

        $col = new YuiColumnDef();
        $col->key = $key;
        $col->label = $label;
        $col->sortable = true;
        //$col->resizable = true;   //disabled by default

        if (!$type && substr($key, 0, 4) == 'time')
            $type = 'time';

        if (!$type && substr($key, 0, 4) == 'date')
            $type = 'date';

        if (!$type)
            $type = 'text';

        switch ($type) {
        case 'text':
            $col->maxAutoWidth = 600;
            break;

        case 'link':
            $col->formatter  = 'formatLink';
            $col->extra_data = $extra ? relurl($extra) : '';
            $col->col_label  = $col_label;
            $this->addHiddenColumn($col_label);
            break;

        case 'date':
            $col->formatter  = 'formatDate';
            //$response['parser'] = 'date';  //XXX js-date dont like mysql date format???
            break;

        case 'time':
            $col->formatter  = 'formatTime';
            //$response['parser'] = 'date';  //XXX js-date dont like mysql date format???
            break;

        case 'money':
            $col->formatter  = 'formatMoney';
            break;

        case 'bool':
            $col->formatter  = 'formatBool';
            break;

        case 'array':
            //"extra" contains an array of string representations of this column's values
            $col->formatter = 'formatArray'.count($this->embed_arrays);
            $this->embed_arrays[] = $extra;
            break;

        default: throw new Exception('Unknown column type '.$type);
        }

        if ($max_width)
            $col->maxAutoWidth = $max_width;

        $this->response_fields[] = $key;
        $this->columns[] = $col;
    }

    /**
     * Configures the datatable to load data from a callback url (XHR),
     * or a static array of data (only includes registered array keys)
     */
    function setDataSource($url)
    {
        if (is_array($url))
        {
            $res = array();

            foreach ($arr as $row)
            {
                $inc_row = array();
                foreach ($row as $key => $val)
                    foreach ($this->columns as $inc_col)
                        if ($inc_col->key == $key)
                            $inc_row[$key] = $val;

                $res[] = $inc_row;
            }

            $this->data_source = $res;

            return;
        }
/*
        if (!is_url($url))
            throw new Exception ('not an url: '.$url);
*/
        if (!is_string($url))
            throw new Exception ('really bad input: '.$url);

        $this->data_source = $url;
    }

    function render()
    {
        if (!$this->columns)
            throw new Exception ('no columns');

        $header = XhtmlHeader::getInstance();

        if ($this->show_paginator)
            $header->includeCss('http://yui.yahooapis.com/2.9.0/build/paginator/assets/skins/sam/paginator.css');

        $header->includeCss('http://yui.yahooapis.com/2.9.0/build/datatable/assets/skins/sam/datatable.css');

        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/connection/connection-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/datasource/datasource-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/element/element-min.js');

        if ($this->show_paginator)
            $header->includeJs('http://yui.yahooapis.com/2.9.0/build/paginator/paginator-min.js');

        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/datatable/datatable-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/json/json-min.js');

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
                    'elLiner.innerHTML = \'<img src="'.relurl('core_dev/gfx/icon_ok').'\' + ( oData == 1 ? "" : "_gray") + \'.png"/>\';'.
                '};';

                for ($i=0; $i<count($this->embed_arrays); $i++) {
                    $res .=
                    'this.formatArray'.$i.' = function(elLiner, oRecord, oColumn, oData) {'.
                        'var a='.JSON::encodeObject($this->embed_arrays[$i],true).';'.
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

                'myColumnDefs = '.JSON::encode($this->columns).';'."\n".
                (is_array($this->data_source) ?
                    //embedded js-array
                    'var '.$data_var.' = '.JSON::encode($this->data_source).';'."\n".
                    'var myDataSource = new YAHOO.util.DataSource('.$data_var.');'.
                    'myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;'. // XXX whats difference of JSARRAY and JSON types?
                    'myDataSource.responseSchema = { fields:'.JSON::encode($this->response_fields, false).'};'
                :
                    //rpc
                    'var myDataSource = new YAHOO.util.DataSource("'.$this->data_source.'");'.
                    'myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;'.
                    //'myDataSource.connXhrMode = "queueRequests";'. //XXX ???
                    'myDataSource.responseSchema = {'.
                        'resultsList: "records",'.
                        'fields: '.JSON::encode($this->response_fields, false).','.
                        'metaFields: { totalRecords:"totalRecords" }'. // mapped to XhrResponse "totalRecords" field, needed for paginator
                    '};'
                );

                if ($this->color_rows) {
                    // Define a custom row formatter function
                    $res .=
                    'var myRowFormatter = function(elTr, oRecord) {';

                    foreach ($this->color_rows as $col)
                    {
                        $c1 = is_numeric($col->c1) ? $col->c1 : 'parseInt( oRecord.getData("'.$col->c1.'") )';
                        $c2 = is_numeric($col->c2) ? $col->c2 : 'parseInt( oRecord.getData("'.$col->c2.'") )';
                        $res .=
                        'if ('.$c1.' '.$col->comparison.' '.$c2.') YAHOO.util.Dom.addClass(elTr, "'.$col->css.'");';
//                        'alert( '.$c1.' + " '.$col->comparison.' " + '.$c2.' + " = " + ('.$c1.' '.$col->comparison.' '.$c2.')   );';
                    }

                    $res .=
                        'return true;'.
                    '};';
                }

                $res .=
                'var myConfigs = {'.
                    'caption:"'.$this->caption.'",'.
                    ($this->pixel_width  ? 'width:"'.$this->pixel_width.'px",' : '').
                    ($this->pixel_height ? 'height:"'.$this->pixel_height.'px",' : '').
                    ($this->sort_order ?
                        'sortedBy: {'.
                            'key:"'.$this->columns[ $this->sort_column ]->key.'",'.
                            'dir:YAHOO.widget.DataTable.'.($this->sort_order == 'asc' ? 'CLASS_ASC' : 'CLASS_DESC').
                        '},'
                    :
                        ''
                    ).
                    ( $this->color_rows ? 'formatRow: myRowFormatter,' : '').
                    ($this->show_paginator ?
                        'paginator: new YAHOO.widget.Paginator({'.
                            (is_array($this->data_source) ? 'totalRecords:'.count($this->data_source).',' : '').
                            'rowsPerPage:'.$this->rows_per_page.','.
                            'rowsPerPageOptions:['.implode(',', $this->rpp_opts).'],'.
                            'containers:["'.$pag_holder.'"],'.
                            'template:"{FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink} &nbsp; {CurrentPageReport} {RowsPerPageDropdown} per page",'.
                            'pageReportTemplate:"Showing items {startRecord} - {endRecord} of {totalRecords}",'.
                        '}),'
                    :
                        ''
                    )
                    .
                    (!is_array($this->data_source) ?
                        'dynamicData:true,'.
                        'initialRequest:"startIndex=0'.
                            ($this->sort_column !== false ? '&sort='.$this->columns[ $this->sort_column ]->key : '').
                            ($this->sort_order ? '&dir='.$this->sort_order : '').
                            '&results='.$this->rows_per_page.'"' // Initial request for first page of data
                    :
                        ''
                    ).
                '};';

                $tbl_type = ($this->pixel_width || $this->pixel_height) ? 'ScrollingDataTable' : 'DataTable';

                $res .=
                'tbl = new YAHOO.widget.'.$tbl_type.'("'.$div_holder.'",myColumnDefs, myDataSource, myConfigs);'.

                (!is_array($this->data_source) ?
                    // Update totalRecords on the fly with value from the XHR request, needed for paginator
                    'tbl.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {'.
                        'oPayload.totalRecords = oResponse.meta.totalRecords;'.
                        'return oPayload;'.
                    '};'
                :
                    ''
                ).

                // Enable row highlighting, more examples: http://developer.yahoo.com/yui/examples/datatable/dt_highlighting.html
                'tbl.subscribe("rowMouseoverEvent", tbl.onEventHighlightRow);'.
                'tbl.subscribe("rowMouseoutEvent", tbl.onEventUnhighlightRow);'.

                'return {'.
                    'oDS: myDataSource,'.
                    'oDT: tbl'.
                '};'.
            '}();'.
        '});';

        return
        '<div id="'.$pag_holder.'"></div>'.
        '<div id="'.$div_holder.'"></div>'.js_embed($res);
    }

}

?>
