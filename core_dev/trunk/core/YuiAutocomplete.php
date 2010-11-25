<?php
/**
 * $Id$
 *
 * Renders a Yahoo UI autocomplete input field
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: draft

require_once('output_js.php');

class YuiAutocomplete
{
    protected $name;
    protected $xhr_url;

    function setName($s) { $this->name = $s; }
    function setXhrUrl($s) { $this->xhr_url = $s; }

    function render()
    {
        if (!$this->name)
            throw new Exception ('must set a name');

        if (!$this->xhr_url)
            throw new Exception ('must set xhr url');

        $header = XhtmlHeader::getInstance();

        $header->includeCss('http://yui.yahooapis.com/2.8.2r1/build/fonts/fonts-min.css');
        $header->includeCss('http://yui.yahooapis.com/2.8.2r1/build/autocomplete/assets/skins/sam/autocomplete.css');

        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/get/get-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/animation/animation-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/datasource/datasource-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/autocomplete/autocomplete-min.js');

        $header->embedCss(
        'label {'.
            'color:#E76300;'.
            'font-weight:bold;'.
        '}'.
        '#myAutoComplete {'.
            'width:30em;'. // set width here or else widget will expand to fit its container
            'padding-bottom:2em;'.
        '}'.
        '.yui-ac .result {position:relative;height:62px;}'.
        '.yui-ac .name {position:absolute;bottom:0;left:64px;}'.
        '.yui-ac .img {position:absolute;top:0;left:0;width:58px;height:58px;border:1px solid black;background-color:black;color:white;}'.
        '.yui-ac .imgtext {position:absolute;width:58px;top:50%;text-align:center;}'.
        '.yui-ac img {width:60px;height:60px;margin-right:4px;}'
        );

        $res =
        'YAHOO.example.CustomFormatting = (function(){'.
            // Instantiate DataSource
            'var oDS = new YAHOO.util.ScriptNodeDataSource("'.$this->xhr_url.'");'.
            'oDS.responseSchema = {'.
                'resultsList: "records",'.
                'fields: ["clickurl", "name", "id", "country", "status"]'.   //XXX configurable
            '};'.

            // Instantiate AutoComplete
            'var oAC = new YAHOO.widget.AutoComplete("'.$this->name.'","myContainer", oDS);'.

            // Bump up the query delay to reduce server load
            'oAC.queryDelay = 1;'.

            // The webservice needs custom parameters
            'oAC.generateRequest = function(sQuery) {'.
                'return sQuery + "&format=json";'.
            '};'.

            // Result data passed as object for easy access from custom formatter.
            'oAC.resultTypeList = false;'.
            // Customize formatter to show thumbnail images
            'oAC.formatResult = function(oResultData, sQuery, sResultMatch) {'.
//XXX make this a custom snippet
                'if(oResultData.thumbnail_url) {'.
                    'img = "<img src=\""+ oResultData.thumbnail_url + "\">";'.
                '} else {'.
                    'img = "<span class=\"img\"><span class=\"imgtext\">N/A</span></span>";'.
                '}'.

                //XXX show country flag instead
                'return "<div class=\"result\">" + img + "&nbsp;<span class=\"name\">" + oResultData.name + " (" + oResultData.country + ") " + oResultData.status + "</span></div>";'.
            '};'.
            'oAC.itemSelectEvent.subscribe(function(sType, aArgs) {'.
                'var oData = aArgs[2];'. // object literal of selected item's result data

                // Redirect to the link
                'window.location.href = "'.relurl('shows/add/').'" + oData.id;'.  //XXX configurable field name
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

        return
        '<div id="myAutoComplete">'.
            '<input type="text" id="'.$this->name.'" name="'.$this->name.'">'.
            '<div id="myContainer"></div>'.
        '</div>'.
        js_embed($res);
    }

}

?>
