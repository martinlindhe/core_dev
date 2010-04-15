<?php
/**
 * $Id$
 *
 * http://developer.yahoo.com/yui/datatable/
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: WIP

//TODO: enable ajax loading of data
//TODO: enable pagination
//TODO: enable inline cell editing

class yui_datatable
{
    private $columns         = array();
    private $datalist        = array();
    private $div_holder_name = 'myDataTableHolder';
    private $caption         = ''; ///< caption for the datatable

    function addColumn($key, $label)
    {
        $this->columns[$key]['key']      = $key; ///XXX stupid, but simplifies other code
        $this->columns[$key]['label']    = $label;
        $this->columns[$key]['sortable'] = true;
        //$this->columns[$key]['resizeable'] = true; //is disabled by default
    }

    /**
     * Configures column type
     */
    function setColumnType($key, $type, $extra = '')
    {
        switch ($type) {
        case 'link':
            //$this->columns[$key]['type']       = $type;
            $this->columns[$key]['formatter']  = 'myFormatLink';
            $this->columns[$key]['extra_data'] = $extra;
            break;

        default: throw new Exception('Unknown column type '.$type);
        }
    }

    function setDataList($arr)
    {
        //only include registered array keys

        foreach ($arr as $row)
        {
            $inc_row = array();
            foreach ($row as $key => $val)
                foreach ($this->columns as $inc_col)
                    if (isset($this->columns[$key]))
                        $inc_row[$key] = $val;

            $res[] = $inc_row;
        }

        $this->datalist = $res;
    }

    function render()
    {
        $res = '<div id="'.$this->div_holder_name.'"></div> ';

        $res .= '<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.8.0r4/build/datatable/assets/skins/sam/datatable.css">';
        //$res .= '<script type="text/javascript" src="http://yui.yahooapis.com/combo?2.8.0r4/build/yahoo-dom-event/yahoo-dom-event.js&2.8.0r4/build/element/element-min.js&2.8.0r4/build/datasource/datasource-min.js&2.8.0r4/build/datatable/datatable-min.js"></script>';

        //Debug version:
        $res .= '<script type="text/javascript" src="http://yui.yahooapis.com/combo?2.8.0r4/build/yahoo/yahoo-debug.js&2.8.0r4/build/dom/dom-debug.js&2.8.0r4/build/event/event-debug.js&2.8.0r4/build/element/element-debug.js&2.8.0r4/build/datasource/datasource-debug.js&2.8.0r4/build/datatable/datatable-debug.js&2.8.0r4/build/logger/logger-debug.js"></script>';


/*
        //OPTIONAL: JSON Utility (for DataSource)
        '<script src="http://yui.yahooapis.com/2.8.0r4/build/json/json-min.js"></script>'.

        //OPTIONAL: Connection Manager (enables XHR for DataSource)
        '<script src="http://yui.yahooapis.com/2.8.0r4/build/connection/connection-min.js"></script>'.

        //OPTIONAL: Get Utility (enables dynamic script nodes for DataSource)
        '<script src="http://yui.yahooapis.com/2.8.0r4/build/get/get-min.js"></script>'.

        //OPTIONAL: Drag Drop (enables resizeable or reorderable columns)
        '<script src="http://yui.yahooapis.com/2.8.0r4/build/dragdrop/dragdrop-min.js"></script>'.

        //OPTIONAL: Calendar (enables calendar editors)
        '<script src="http://yui.yahooapis.com/2.8.0r4/build/calendar/calendar-min.js"></script>'.
*/

        $data_var = 'yui_dt'.mt_rand(0,99999);

        $res .=
        '<script type="text/javascript">'."\n".
        'var '.$data_var.' = '.jsArray2D($this->datalist).';'."\n".

        'YAHOO.util.Event.addListener(window, "load", function() {'.
            'YAHOO.example.Basic = function() {'.

                /**
                 * elLiner reference to parent cell
                 * oRecord reference to current row, read other column value with oRecord.getData("key_name")
                 * oColumn reference to current column (pointer to a row in myColumnDefs)
                 * oData is the cell data
                 */
                'this.myFormatLink = function(elLiner, oRecord, oColumn, oData) {'.
                    'var prefix = oColumn["extra_data"];'.
                    'elLiner.innerHTML = "<a href=\"" + prefix + oData + "\">" + oData + "</a>";'.
                '};'.

                // Add the custom formatter to the shortcuts
                'YAHOO.widget.DataTable.Formatter.myFormatLink = this.myFormatLink;'."\n".

                'myColumnDefs = '.jsArray2D($this->columns).';'."\n".
                'myDataSource = new YAHOO.util.DataSource('.$data_var.');'.
                'myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;'.
                'myDataSource.responseSchema = {'.
                    //XXX return 2d array with key:name,parser:datatype    see http://developer.yahoo.com/yui/datatable/#basicsort
                    'fields: '.jsArray1D(array_keys($this->columns), false).
                '};'.

                'this.myDataTable = new YAHOO.widget.DataTable("'.$this->div_holder_name.'",'.
                    'myColumnDefs, myDataSource, {caption:"'.$this->caption.'"});'.

                'return {'.
                    'oDS: myDataSource,'.
                    'oDT: myDataTable'.
                '};'.
            '}();'.
        '});'.
        '</script>';

        return $res;
    }

}

?>
