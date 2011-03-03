<?php
/**
 * $Id$
 *
 * Renders a Yahoo UI autocomplete input field
 *
 * http://developer.yahoo.com/yui/autocomplete/
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('JSON.php');
require_once('XhtmlComponent.php');

class YuiAutocomplete extends XhtmlComponent
{
    protected $xhr_url;

    protected $js_onclick       = '';
    protected $js_format_result = '';
    protected $result_fields    = array();

    protected $query_delay      = 0.5;         // Bump up the query delay to reduce server load

    function setXhrUrl($s) { $this->xhr_url = $s; }

    function setJsOnclick($s) { $this->js_onclick = $s; }
    function setJsFormatResult($s) { $this->js_format_result = $s; }

    function setResultFields($o)
    {
        if (is_array($o))
            foreach ($o as $s)
                $this->result_fields[] = $s;
        else
            $this->result_fields[] = $o;
    }

    function render()
    {
        if (!$this->name)
            throw new Exception ('must set a name');

        if (!$this->xhr_url)
            throw new Exception ('must set xhr url');

        if (!$this->js_onclick || !$this->js_format_result || !$this->result_fields)
            throw new Exception ('need js code');

        $header = XhtmlHeader::getInstance();

        $header->includeCss('http://yui.yahooapis.com/2.8.2r1/build/fonts/fonts-min.css');
        $header->includeCss('http://yui.yahooapis.com/2.8.2r1/build/autocomplete/assets/skins/sam/autocomplete.css');

        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/get/get-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/animation/animation-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/datasource/datasource-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/autocomplete/autocomplete-min.js');

        $div_holder = 'yui_ac'.mt_rand();

        $header->embedCss(
        'label {'.
            'color:#E76300;'.
            'font-weight:bold;'.
        '}'.
        '#'.$div_holder.' {'.
            'width:20em;'. // set width here or else widget will expand to fit its container
            'padding-bottom:2em;'.
        '}'
        );

        $res =
        'YAHOO.example.CustomFormatting = (function(){'.
            // Instantiate DataSource
            'var oDS = new YAHOO.util.ScriptNodeDataSource("'.$this->xhr_url.'");'.
            'oDS.responseSchema = {'.
                'resultsList: "records",'.
                'fields: '.JSON::encode($this->result_fields, false).
            '};'.

            // Instantiate AutoComplete
            'var oAC = new YAHOO.widget.AutoComplete("'.$this->name.'","myContainer", oDS);'.
            'oAC.queryDelay = '.$this->query_delay.';'.

            // The webservice needs custom parameters
            'oAC.generateRequest = function(sQuery) {'.
                'return sQuery + "&format=json";'.
            '};'.

            // Result data passed as object for easy access from custom formatter.
            'oAC.resultTypeList = false;'.
            // Customize formatter to show thumbnail images
            'oAC.formatResult = function(oResultData, sQuery, sResultMatch) {'.
                $this->js_format_result.
            '};'.
            'oAC.itemSelectEvent.subscribe(function(sType, aArgs) {'.
                'var oData = aArgs[2];'. // object literal of selected item's result data
                $this->js_onclick.
            '});'.

            // Stub for form validation
            'var validateForm = function() {'.
                // Validation code goes here
                'return true;'.
            '};'.

            'return {'.
                'oDS: oDS,'.
                'oAC: oAC,'.
                'validateForm: validateForm'.
            '}'.
        '})();';

        $in = new XhtmlComponentInput();
        $in->name = $this->name;

        return
        '<div id="'.$div_holder.'">'.
            $in->render().
            '<div id="myContainer"></div>'.
        '</div>'.
        js_embed($res);
    }

}

?>
