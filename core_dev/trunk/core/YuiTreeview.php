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
    protected $xhr_url          = '';
    protected $root_nodes       = array();
    protected $ms_timeout       = 7000;    ///< (in ms), duration before giving up XHR request

    protected $allow_expand_all = false;   ///< allow multiple nodes open at once?
    protected $leaf_mode        = true;    ///< shows childless nodes. disable to use Expand/Collapse icons

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

        if ($this->allow_expand_all)
            $node_type = 'TextNode';
        else
            $node_type = 'MenuNode';

        $res =
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

            'var sUrl = "'.$this->xhr_url.'" + nodeLabel;'.

            //prepare our callback object
            'var callback = {'.

                //if our XHR call is successful, we want to make use
                //of the returned data and create child nodes.
                    'success: function(oResponse) {'.
//                  'YAHOO.log("XHR transaction was successful.", "info", "example");'.
                    //YAHOO.log(oResponse.responseText);
                    'var oResults = eval("(" + oResponse.responseText + ")");'.
                    'if((oResults.records) && (oResults.records.length)) {'.
                        //Result is an array if more than one result, string otherwise
                        'if(YAHOO.lang.isArray(oResults.records)) {'.
                            'for (var i=0, j=oResults.records.length; i<j; i++) {'.
                                'var tempNode = new YAHOO.widget.'.$node_type.'(oResults.records[i], node, false);'.
                            '}'.
                        '} else {'.
                            //there is only one result; comes as string:
                            'var tempNode = new YAHOO.widget.'.$node_type.'(oResults.records, node, false);'.
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

                'timeout:'.$this->ms_timeout.
            '};'.

            'YAHOO.util.Connect.asyncRequest("GET", sUrl, callback);'.
        '}'.

        'tree = new YAHOO.widget.TreeView("'.$div_holder.'");'.

        //turn dynamic loading on for entire tree:
        'tree.setDynamicLoad(loadNodeData, '.($this->leaf_mode ? '1' : '0').');'.

        //get root node for tree:
        'var root = tree.getRoot();'.

        //add child nodes for tree:
        'var aChilds = ['.jsArrayFlat($this->root_nodes, false).'];'.

        'for (var i=0, j=aChilds.length; i<j; i++) {'.
            'var tempNode = new YAHOO.widget.'.$node_type.'(aChilds[i], root, false);'.
        '}'.

        //render tree with these toplevel nodes; all descendants of these nodes
        //will be generated as needed by the dynamic loader.
        'tree.draw();'.

        'tree.subscribe("dblClickEvent",function(oArgs) {'.
            'alert("Double click on node: " + oArgs.node.label);'.
        '});';

        return
        '<div id="'.$div_holder.'"></div>'.
        js_embed($res);
    }

}

?>
