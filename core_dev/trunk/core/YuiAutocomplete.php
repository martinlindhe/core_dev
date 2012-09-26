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

//STATUS: wip, currently only used in savak/bnr

//TODO: clean up css so button is on same line as input field

//XXX TODO: remove items from selection menu which was already selected
// XXX TODO later: encase each selected item with a <span> or somehting, and add a "X" remove button, similar to facebook autocomplete lists

namespace cd;

require_once('XhtmlComponent.php');
require_once('JSON.php');

class YuiAutocomplete extends XhtmlComponent
{
    protected $xhr_url;

    protected $js_format_result = 'return "<div class=\"result\"><span class=\"name\">" + highlight(oResultData.name, sQuery) + "</span></div>";';

    protected $result_fields    = array();

    protected $query_delay      = 0.4;         // Bump up the query delay to reduce server load

    function setXhrUrl($s) { $this->xhr_url = relurl($s); }

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

        if (!$this->js_format_result || !$this->result_fields)
            throw new Exception ('need js code');

        $header = XhtmlHeader::getInstance();

        $header->includeCss('http://yui.yahooapis.com/2.9.0/build/fonts/fonts-min.css');
        $header->includeCss('http://yui.yahooapis.com/2.9.0/build/autocomplete/assets/skins/sam/autocomplete.css');
        $header->includeCss('http://yui.yahooapis.com/2.9.0/build/button/assets/skins/sam/button.css');

        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/get/get-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/animation/animation-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/datasource/datasource-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/autocomplete/autocomplete-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/element/element-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/button/button-min.js');

        $div_holder = 'yui_ac'.mt_rand();

        $container_holder = 'ac_contain_'.mt_rand();

        $button_id = 'ac_toggle_'.mt_rand();

        $input_id = 'ac_input_'.mt_rand();

        $header->embedJs(
        'function highlight(s,h)'.
        '{'.
            'var regex = new RegExp("("+h+")","ig");'.
            'return s.replace(regex, "<span class=\"highlighted\">$1</span>");'.
        '}'
        );

        $header->embedCss(
        'label {'.
            'color:#E76300;'.
            'font-weight:bold;'.
        '}'.
        '#'.$div_holder.' {'.
            'width:20em;'. // set width here or else widget will expand to fit its container
        '}'.

        '.yui-ac .result {position:relative;height:20px;}'.
        '.yui-ac .name {position:absolute;bottom:0;}'.
        '.highlighted {color:#CA485E;font-weight:bold; }'.

        // buttons
        '.yui-ac .yui-button {vertical-align:middle;}'.
        '.yui-ac .yui-button button {background: url(http://developer.yahoo.com/yui/examples/autocomplete/assets/img/ac-arrow-rt.png) center center no-repeat}'.
        '.yui-ac .open .yui-button button {background: url(http://developer.yahoo.com/yui/examples/autocomplete/assets/img/ac-arrow-dn.png) center center no-repeat}'.

        // custom styles for inline instances
        '.yui-skin-sam .yui-ac-input {position:static; vertical-align:middle;}'.
//        '.yui-skin-sam .yui-ac-container {width:20em;left:0px;}'.

        '.yui-skin-sam .yui-ac-content {'. /* set scrolling */
            'max-height:250px;overflow:auto;overflow-x:hidden;'. /* set scrolling */
        '}'

        );

        $res =
        'YAHOO.example.CustomFormatting = (function(){'.
            // Instantiate DataSource
            'var oDS = new YAHOO.util.ScriptNodeDataSource("'.$this->xhr_url.'");'. /// XXX use XHR instead
            'oDS.responseSchema = {'.
                'resultsList:"records",'.
                'fields:'.JSON::encode($this->result_fields, false).
            '};'.

            // Instantiate AutoComplete
            'var oAC = new YAHOO.widget.AutoComplete("'.$input_id.'","'.$container_holder.'", oDS);'.
            'oAC.minQueryLength = 0;'.        // minimum length to start search.    XXXX must be 0 or else Toggle button stuff dont work due to bug in YuiAutocomplete 2.9.0, unreported...
            'oAC.queryDelay = '.$this->query_delay.';'.
//            'oAC.delimChar = [",",";"];'.     // Enable comma and semi-colon delimiters. if enabled the form takes multi select of items
            'oAC.animSpeed = 0.01;'.          // speed of dropdown animation
            'oAC.maxResultsDisplayed = 100;'. // Show more results, scrolling is enabled via CSS
//            'oAC.useShadow = true;'.
//          'prehighlightClassName: "yui-ac-prehighlight",
            'oAC.forceSelection = true;'.

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


            // creates a hidden input field with desired nmae
            'oAC.itemSelectEvent.subscribe(function(sType, aArgs) {'.
                // 'var myAC = aArgs[0];'. // reference back to the AC instance
                // 'var elLI = aArgs[1];'. // reference to the selected <li> element
                'var oData = aArgs[2];'. // object literal of selected item's result data

                // creates a new hidden input field and attach it to the form
                'var input = document.createElement("input");'.
                'input.setAttribute("type", "hidden");'.
                'input.setAttribute("name", "'.$this->name.'");'.
                //'input.setAttribute("name", "'.$this->name.'[]");'. // for multi-select
                'input.setAttribute("value", oData.id);'.
                'document.getElementById("'.$div_holder.'").appendChild(input);'.
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
        $in->name = $input_id;
        $in->width = 200;   // XXXX HACK, should not set width at all.. but we do it now so button dont end up on the next line

        return
        '<div id="'.$div_holder.'">'.
            $in->render().
            '<div id="'.$container_holder.'"></div>'.
        '</div>'.
        js_embed($res);
    }

}

?>
