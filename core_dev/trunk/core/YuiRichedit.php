<?php
/**
 * $Id$
 *
 * http://developer.yahoo.com/yui/editor/
 *
 * Custom toolbar example:
 * http://developer.yahoo.com/yui/examples/editor/toolbar_editor.html
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: integrate image upload

require_once('XhtmlComponent.php');

require_once('JSON.php');

class YuiRichedit extends XhtmlComponent
{
    var       $value;                 ///< initial value of textarea
    var       $width        = 500;
    var       $height       = 200;
    protected $titlebar     = '';
    protected $show_dompath = false;  ///< displays the DOM bar at the bottom

    function setValue($s) { $this->value = $s; }
    function setWidth($n) { $this->width = $n; }
    function setHeight($n) { $this->height = $n; }

    function render()
    {
        $header = XhtmlHeader::getInstance();

        $header->includeCss('http://yui.yahooapis.com/2.9.0/build/assets/skins/sam/skin.css');

        // utility Dependencies
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/element/element-min.js');

        // needed for Menus, Buttons and Overlays used in the Toolbar
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/container/container_core-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/menu/menu-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/button/button-min.js');

        // source file for Rich Text Editor
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/editor/editor-min.js');

        $div_id = $this->name ? $this->name : 're_'.mt_rand();

        $res =
        'var myEditor = new YAHOO.widget.Editor("'.$div_id.'",'.
        '{'.
            'width: "'.$this->width.'px",'.
            'height: "'.$this->height.'px",'.
            'dompath: '.($this->show_dompath ? 'true' : 'false').','.
            'animate: true,'.      // animates the opening, closing and moving of Editor windows
            'handleSubmit: true,'. // editor will attach itself to the textareas parent form's submit handler

            'toolbar: {'.
                ($this->titlebar ? 'titlebar: "'.$this->titlebar.'",' : '').
                'buttons: ['.
                    '{ group: "textstyle", label: " ",'. ///XXX if not using a space as label, holder div will shrink and it looks ugly
                        'buttons: ['.
                            '{ type: "push", label: "Bold", value: "bold" },'.
                            '{ type: "push", label: "Italic", value: "italic" },'.
                            '{ type: "push", label: "Underline", value: "underline" },'.
                            '{ type: "separator" },'.
                            '{ type: "select", label: "Arial", value: "fontname", disabled: true,'.
                                'menu: ['.
                                    '{ text: "Arial", checked: true },'.
                                    '{ text: "Arial Black" },'.
                                    '{ text: "Comic Sans MS" },'.
                                    '{ text: "Courier New" },'.
                                    '{ text: "Lucida Console" },'.
                                    '{ text: "Tahoma" },'.
                                    '{ text: "Times New Roman" },'.
                                    '{ text: "Trebuchet MS" },'.
                                    '{ text: "Verdana" }'.
                                ']'.
                            '},'.
                            '{ type: "spin", label: "13", value: "fontsize", range: [ 9, 75 ], disabled: true },'.
                            '{ type: "separator" },'.
                            '{ type: "color", label: "Font Color", value: "forecolor", disabled: true },'.
                            '{ type: "color", label: "Background Color", value: "backcolor", disabled: true },'.
                            '{ type: "separator" },'.
                            '{ type: "push", label: "Create an Unordered List", value: "insertunorderedlist" },'.
                            '{ type: "push", label: "Create an Ordered List", value: "insertorderedlist" },'.
                        ']'.
                    '}'.
                ']'.
            '}'.
        '}'.
        ');'.

        'myEditor.render();';

        $hold = new XhtmlComponentTextarea();
        $hold->name   = $this->name;
        $hold->value  = $this->value;
        $hold->width  = 1;
        $hold->height = 1;

        return $hold->render().js_embed($res);
    }
}

?>
