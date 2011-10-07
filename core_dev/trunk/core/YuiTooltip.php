<?php
/**
 * $Id$
 */

//STATUS: early wip

//TODO: tooltip "window" where content is loaded with XHR

class YuiTooltip
{
    function render()
    {
        $header = XhtmlHeader::getInstance();

        $header->includeCss('http://yui.yahooapis.com/2.9.0/build/fonts/fonts-min.css');
        $header->includeCss('http://yui.yahooapis.com/2.9.0/build/container/assets/skins/sam/container.css');

        // utility Dependencies
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/container/container-min.js');

        $header->embedCss(
        '#ctx{'.
            'background:orange;'.
            'width:200px;'.
            'height:200px;'.
        '}'
        );

        $js =
        'YAHOO.namespace("example.container");'.
        'YAHOO.example.container.tt1 = new YAHOO.widget.Tooltip("tt1", { context:"ctx", text:"My text was set using the text configuration property" });';

        return
        '<p id="ctx">Hover over me to see a Tooltip!</p>'.js_embed($js);
    }

}

?>
