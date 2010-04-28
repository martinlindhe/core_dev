<?php
/**
 * $Id$
 *
 * http://developer.yahoo.com/yui/datatable/
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: WIP

//TODO: enable inline cell editing
//HMM: remove addHiddenColumn() ? it is currently unused

class yui_datatable
{
    private $columns         = array();
    private $response_fields = array();
    private $datalist        = array();
    private $div_holder      = ''; ///< name of div tag to hold the datatable
    private $caption         = ''; ///< caption for the datatable
    private $xhr_source      = ''; ///< url to retrieve data from XMLHttpRequest
    private $rows_per_page   = 20; ///< for the paginator

    function setCaption($s) { $this->caption = $s; }
    function setRowsPerPage($n) { $this->rows_per_page = $n; }

    /**
     * Adds a hidden column to the dataset, needed to embed data in the table linked to other cells, see $col_label param for addColumn()
     */
    function addHiddenColumn($key)
    {
        $this->columns[] = array('key' => $key, 'hidden' => true);
        $this->response_fields[] = array('key' => $key);
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

        if ($type)
            switch ($type) {
            case 'link':
                $arr['formatter']  = 'formatLink';
                $arr['extra_data'] = $extra;
                $arr['col_label']  = $col_label;
                break;

            case 'date':
                $arr['formatter']  = 'formatDate';
                //$response['parser'] = 'date';  //XXX js-date dont like mysql date format???
                break;

            case 'time':
                $arr['formatter']  = 'formatTime';
                //$response['parser'] = 'date';  //XXX js-date dont like mysql date format???
                break;

            default: throw new Exception('Unknown column type '.$type);
            }

        $this->response_fields[] = $response;

        $this->columns[] = $arr;
    }

    /**
     * Loads the datatable with a static array of data
     * Cannot be used with setDataRetreiver()
     */
    function setDataList($arr)
    {
        //only include registered array keys
die('XXX BROKEN');
        foreach ($arr as $row)
        {
            $inc_row = array();
            foreach ($row as $key => $val)
                foreach ($this->columns as $inc_col)
                    if (isset($this->columns[$key])) //XXX broken check
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
        $header = XhtmlHeader::getInstance();

        $header->includeCss('http://yui.yahooapis.com/2.8.0r4/build/paginator/assets/skins/sam/paginator.css');
        $header->includeCss('http://yui.yahooapis.com/2.8.0r4/build/datatable/assets/skins/sam/datatable.css');

        $header->includeJs('http://yui.yahooapis.com/2.8.0r4/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.0r4/build/connection/connection-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.0r4/build/datasource/datasource-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.0r4/build/element/element-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.0r4/build/paginator/paginator-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.0r4/build/datatable/datatable-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.0r4/build/json/json-min.js');

        if (!$this->div_holder)
            $this->div_holder = 'YuiDtHold'.mt_rand(0,9999);

        $data_var = 'YuiDt'.mt_rand(0,9999);

        $col1 = $this->columns[0];

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
                    'var prefix = oColumn["extra_data"];'.
                    'var col_label_name = oColumn["col_label"];'.
                    'var col_label = oRecord._oData[col_label_name];'.
                    'elLiner.innerHTML = "<a href=\"" + prefix + oData + "\">" + (col_label ? col_label : oData) + "</a>";'.
                '};'.

                //oData cell data "YYYY-MM-DD HH:MM:SS"
                'this.formatDate = function(sData) {'.
                    'if (!sData) return;'.
                    'var a1 = oData.substr(0,10).split("-");'.
                    'elLiner.innerHTML = a1[0]+"-"+a1[1]+"-"+a1[2];'.
                '};'.

                'this.formatTime = function(elLiner, oRecord, oColumn, oData) {'.
                    'if (!oData) return;'.
                    //'return new Date(sData);'.  //dont work because js date dont parse mysql datestamps (???)
                    'var a1 = oData.substr(0,10).split("-");'.
                    'var a2 = oData.substr(11,8).split(":");'.
                    'elLiner.innerHTML = a1[0]+"-"+a1[1]+"-"+a1[2]+" "+a2[0]+":"+a2[1]+":"+a2[2];'.
                '};'.

                // Add the custom formatter to the shortcuts
                'YAHOO.widget.DataTable.Formatter.formatLink = this.formatLink;'.
                'YAHOO.widget.DataTable.Formatter.formatDate = this.formatDate;'.
                'YAHOO.widget.DataTable.Formatter.formatTime = this.formatTime;'.

                'myColumnDefs = '.jsArray2D($this->columns).';'."\n".
                ($this->xhr_source ?
                    //rpc
                    'var myDataSource = new YAHOO.util.DataSource("'.$this->xhr_source.'");'.
                    'myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;'.
                    //'myDataSource.connXhrMode = "queueRequests";'. //XXX ???
                    'myDataSource.responseSchema = {'.
                        'resultsList: "records",'.
                        'fields: '.jsArray2D($this->response_fields).','.
                        'metaFields: { totalRecords:"totalRecords" }'. // XXX ???
                    '};'
                    :
                    //embedded js-array
                    'var '.$data_var.' = '.jsArray2D($this->datalist).';'."\n".
                    'var myDataSource = new YAHOO.util.DataSource('.$data_var.');'.
                    'myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;'.
                    'myDataSource.responseSchema = { fields:'.jsArray2D($this->response_fields).'};'
                ).

                'var myConfigs = {'.
                    'caption:"'.$this->caption.'",'.
                    ($this->xhr_source ?
                        'dynamicData:true,'.
                        'initialRequest: "sort='.$col1['key'].'&dir=asc&startIndex=0&results='.$this->rows_per_page.'",'. // Initial request for first page of data
                        'sortedBy : {key:"'.$col1['key'].'", dir:YAHOO.widget.DataTable.CLASS_ASC},'.        //XXX test with static data
                        'paginator: new YAHOO.widget.Paginator({ rowsPerPage:'.$this->rows_per_page.' })'  //XXX test with static data
                        :
                        ''
                    ).
                '};'.

                'myDataTable = new YAHOO.widget.DataTable("'.$this->div_holder.'",myColumnDefs, myDataSource, myConfigs);'.

                // Update totalRecords on the fly with value from server
                'myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {'.
                    'oPayload.totalRecords = oResponse.meta.totalRecords;'. //XXXX???
                    'return oPayload;'.
                '};'.

                'return {'.
                    'oDS: myDataSource,'.
                    'oDT: myDataTable'.
                '};'.
            '}();'.
        '});';

        return
        '<div id="'.$this->div_holder.'"></div>'.
        '<script type="text/javascript">'.$res.'</script>';
    }

}

?>
