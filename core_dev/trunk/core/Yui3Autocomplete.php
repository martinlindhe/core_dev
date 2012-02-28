<?php
/**
 * $Id$
 *
 * http://developer.yahoo.com/yui/3/autocomplete/
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: not finished. YuiAutocomplete is currently preferred. this will eventuall replace YuiAutocomplete

//TODO: ability to load list with id=>label pairs of data. show label but submit id

require_once('JSON.php');

class Yui3Autocomplete
{
    protected $data_source;

    function setDataSource($s) { $this->data_source = $s; }

    function render()
    {
        if (!$this->data_source)
            throw new Exception ('need data source');

        $header = XhtmlHeader::getInstance();

        $header->includeCss('http://yui.yahooapis.com/3.3.0/build/cssfonts/fonts-min.css');
        $header->includeJs('http://yui.yahooapis.com/3.3.0/build/yui/yui-min.js');

        $res =
        'YUI().use("autocomplete", "autocomplete-filters", "autocomplete-highlighters", function (Y)'.
        '{'.
            'var inputNode = Y.one("#ac-input"),'.
                'tags = '.JSON::encode($this->data_source, false).';'.

            'inputNode.plug(Y.Plugin.AutoComplete, {'.
//                'activateFirstItem: true,'.
                'allowTrailingDelimiter: true,'.
                'minQueryLength: 0,'.
                'queryDelay: 0,'.
                'queryDelimiter: ",",'.
                'source: tags,'.
                'resultHighlighter: "startsWith",'.

                // Chain together a startsWith filter followed by a custom result filter
                // that only displays tags that havent already been selected.
                'resultFilters: ["startsWith", function (query, results) {'.
                    // Split the current input value into an array based on comma delimiters.
                    'var selected = inputNode.ac.get("value").split(/\s*,\s*/);'.

                    // Pop the last item off the array, since it represents the current query
                    // and we dont want to filter it out.
                    'selected.pop();'.

                    // Convert the array into a hash for faster lookups.
                    'selected = Y.Array.hash(selected);'.

                    // Filter out any results that are already selected, then return the
                    // array of filtered results.
                    'return Y.Array.filter(results, function (result) {'.
                        'return !selected.hasOwnProperty(result.text);'.
                    '});'.
                '}]'.
            '});'.

            // When the input node receives focus, send an empty query to display the full
            // list of tag suggestions.
            'inputNode.on("focus", function () {'.
                'inputNode.ac.sendRequest("");'.
            '});'.

            // After a tag is selected, send an empty query to update the list of tags.
            'inputNode.ac.after("select", function () {'.
                'inputNode.ac.sendRequest("");'.
                'inputNode.ac.show();'.
            '});'.
        '});';

        return
        '<div id="demo">'.
            '<label for="ac-input">Tags:</label><br/>'.
            '<input id="ac-input" type="text"/>'.
        '</div>'.
        js_embed($res);
    }
}

?>
