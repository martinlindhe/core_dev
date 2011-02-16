<?php
/**
 * $Id: YuiDate.php 5710 2011-02-03 17:39:53Z ml $
 *
 * Renders a Yahoo UI treeview (javascript)
 *
 * Documentation:
 * http://developer.yahoo.com/yui/treeview/
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('output_js.php');

class YuiTreeview
{
    protected $xhr_url = '';
    protected $root_nodes = array();

    function setRootNodes($arr) { $this->root_nodes = $arr; }

    function setXhrUrl($s) { $this->xhr_url = $s; }

    function render()
    {
        if (!$this->root_nodes)
            throw new Exception ('no root nodes set');

        if (!$this->xhr_url)
            throw new Exception ('xhr url must be set');

        $header = XhtmlHeader::getInstance();

        $header->includeCss('http://yui.yahooapis.com/2.8.2r1/build/treeview/assets/skins/sam/treeview.css');
        // Optional CSS for for date editing with Calendar
        $header->includeCss('http://yui.yahooapis.com/2.8.2r1/build/calendar/assets/skins/sam/calendar.css');

        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/animation/animation-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/calendar/calendar-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/json/json-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/connection/connection-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/treeview/treeview-min.js');

        $locale = LocaleHandler::getInstance();

        $div_holder = 'yui_tree_hold'.mt_rand();

        $res =

        'YAHOO.example.treeExample = function() {'.

            'var tree, currentIconMode;'.

            'function changeIconMode() {'.
                'var newVal = parseInt(this.value);'.
                'if (newVal != currentIconMode) {'.
                    'currentIconMode = newVal;'.
                '}'.
                'buildTree();'.
            '}'.

            'function loadNodeData(node, fnLoadComplete)  {'.

                //We'll load node data based on what we get back when we
                //use Connection Manager topass the text label of the
                //expanding node to the Yahoo!
                //Search "related suggestions" API.  Here, we're at the
                //first part of the request -- we'll make the request to the
                //server.  In our success handler, we'll build our new children
                //and then return fnLoadComplete back to the tree.

                //Get the node's label and urlencode it; this is the word/s
                //on which we'll search for related words:
                'var nodeLabel = encodeURI(node.label);'.

                //prepare URL for XHR request:
                'var sUrl = "'.$this->xhr_url.'" + nodeLabel;'.

                //prepare our callback object
                'var callback = {'.

                    //if our XHR call is successful, we want to make use
                    //of the returned data and create child nodes.
                    'success: function(oResponse) {'.
                        'YAHOO.log("XHR transaction was successful.", "info", "example");'.
                        //YAHOO.log(oResponse.responseText);
                        'var oResults = eval("(" + oResponse.responseText + ")");'.
                        'if((oResults.records) && (oResults.records.length)) {'.
                            //Result is an array if more than one result, string otherwise
                            'if(YAHOO.lang.isArray(oResults.records)) {'.
                                'for (var i=0, j=oResults.records.length; i<j; i++) {'.
                                    'var tempNode = new YAHOO.widget.TextNode(oResults.records[i], node, false);'.
                                '}'.
                            '} else {'.
                                //there is only one result; comes as string:
                                'var tempNode = new YAHOO.widget.TextNode(oResults.records, node, false);'.
                            '}'.
                        '}'.

                        //When we're done creating child nodes, we execute the node's
                        //loadComplete callback method which comes in via the argument
                        //in the response object (we could also access it at node.loadComplete,
                        //if necessary):
                        'oResponse.argument.fnLoadComplete();'.
                    '},'.

                    //if our XHR call is not successful, we want to
                    //fire the TreeView callback and let the Tree
                    //proceed with its business.
                    'failure: function(oResponse) {'.
                        'YAHOO.log("Failed to process XHR transaction.", "info", "example");'.
                        'oResponse.argument.fnLoadComplete();'.
                    '},'.

                    //our handlers for the XHR response will need the same
                    //argument information we got to loadNodeData, so
                    //we'll pass those along:
                    'argument: {'.
                        '"node": node,'.
                        '"fnLoadComplete": fnLoadComplete'.
                    '},'.

                    //timeout -- if more than 7 seconds go by, we'll abort
                    //the transaction and assume there are no children:
                    'timeout: 7000'.
                '};'.

                //With our callback object ready, it's now time to
                //make our XHR call using Connection Manager's
                //asyncRequest method:
                'YAHOO.util.Connect.asyncRequest("GET", sUrl, callback);'.
            '}'.

            'function buildTree() {'.
                //create a new tree:
                'tree = new YAHOO.widget.TreeView("'.$div_holder.'");'.

                //turn dynamic loading on for entire tree:
                'tree.setDynamicLoad(loadNodeData, currentIconMode);'.

                //get root node for tree:
                'var root = tree.getRoot();'.

                //add child nodes for tree:
                'var aChilds = ['.jsArrayFlat($this->root_nodes, false).'];'.

                'for (var i=0, j=aChilds.length; i<j; i++) {'.
                    'var tempNode = new YAHOO.widget.TextNode(aChilds[i], root, false);'.
                '}'.

                // Use the isLeaf property to force the leaf node presentation for a given node.
                // This disables dynamic loading for the node.
                'var tempNode = new YAHOO.widget.TextNode("This is a leaf node", root, false);'.
                'tempNode.isLeaf = true;'.

                //render tree with these toplevel nodes; all descendants of these nodes
                //will be generated as needed by the dynamic loader.
                'tree.draw();'.
            '}'.

            'return {'.
                'init: function() {'.
                    'YAHOO.util.Event.on(["mode0", "mode1"], "click", changeIconMode);'.
                    'var el = document.getElementById("mode1");'.
                    'if (el && el.checked) {'.
                        'currentIconMode = parseInt(el.value);'.
                    '} else {'.
                        'currentIconMode = 0;'.
                    '}'.

                    'buildTree();'.
                '}'.
            '}'.
        '} ();'.

        //once the DOM has loaded, we can go ahead and set up our tree:
        'YAHOO.util.Event.onDOMReady(YAHOO.example.treeExample.init, YAHOO.example.treeExample,true);';

        return
        '<div id="'.$div_holder.'"></div>'.
        js_embed($res);
    }

}

?>
