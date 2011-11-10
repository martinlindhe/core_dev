<?php
/**
 * $Id$
 */

//STATUS: early wip

//TODO: extend from XhtmlComponent
//TODO: tooltip "window" where content is loaded with XHR

class YuiTooltip
{
    var $text; ///< tooltip text

    function render()
    {
        if (!$this->text)
            throw new Exception ('no tooltip set');

        $header = XhtmlHeader::getInstance();

        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/container/container-min.js');

        $header->includeCss('http://yui.yahooapis.com/2.9.0/build/fonts/fonts-min.css');
        $header->includeCss('http://yui.yahooapis.com/2.9.0/build/container/assets/skins/sam/container.css');

        $tt_id = 'tt_'.mt_rand();

        $header->embedCss(
        '#'.$tt_id.'{'.
            'background:orange;'.
            'width:200px;'.
            'height:200px;'.
        '}'
        );

        $js =
        'YAHOO.namespace("example.container");'.
        'YAHOO.example.container.tt1 = new YAHOO.widget.Tooltip("tt1",'.
            '{'.
                'context:"'.$tt_id.'",'.
                'text:"'.$this->text.'"'.
            '}'.
        ');';

        return
        '<p id="'.$tt_id.'">Hover over me to see a Tooltip!</p>'.js_embed($js);
    }

}

/*
    $x = new YuiTooltip();
    $x->text = 'this is teh tooltip!!!';
    echo $x->render();
*/

?>
