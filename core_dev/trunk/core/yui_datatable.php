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

//FIXME: include all scripts in one request

class yui_datatable
{
    private $columns         = array();
    private $datalist        = array();
    private $div_holder_name = 'myDataTableHolder';
    private $caption         = ''; //caption of the datatable

    function addColumn($key, $label)
    {
        $this->columns[] = array('key' => $key, 'label' => $label, 'sortable' => true, 'resizeable' => true); //XXX i jsArray2D om val === bool, skriv ut numeriskt
    }

    function setDataList($arr)
    {
        //only include registered array keys

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

    function render()
    {
        $res = '<div id="'.$this->div_holder_name.'"></div> ';

        //CSS file (default YUI Sam Skin)
        $res .=
        '<link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.8.0r4/build/datatable/assets/skins/sam/datatable.css">'.
        //Dependencies
        '<script src="http://yui.yahooapis.com/2.8.0r4/build/yahoo-dom-event/yahoo-dom-event.js"></script>'.
        '<script src="http://yui.yahooapis.com/2.8.0r4/build/element/element-min.js"></script>'.
        '<script src="http://yui.yahooapis.com/2.8.0r4/build/datasource/datasource-min.js"></script>'.

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

        //Source files
        '<script src="http://yui.yahooapis.com/2.8.0r4/build/datatable/datatable-min.js"></script>';

        //XXX remove this hack
        $xxx = array();
        foreach ($this->columns as $row) {
            $xxx[] = $row['key'];
        }

        $res .=
        '<script type="text/javascript">'."\n".

        'YAHOO.example.Data = {'."\n".
            'bookorders: '.jsArray2D($this->datalist).
        '};'."\n".

        'YAHOO.util.Event.addListener(window, "load", function() {'.
            'YAHOO.example.Basic = function() {'.
                'var myColumnDefs = '.jsArray2D($this->columns).';'."\n".

                'var myDataSource = new YAHOO.util.DataSource(YAHOO.example.Data.bookorders);'.
                'myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;'.
                'myDataSource.responseSchema = {'.
                    'fields: '.jsArray1D($xxx, false).
                '};'.

                'var myDataTable = new YAHOO.widget.DataTable("'.$this->div_holder_name.'",'.
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
