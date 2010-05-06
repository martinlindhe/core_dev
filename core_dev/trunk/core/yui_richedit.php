<?php
/**
 * $Id$
 *
 * http://developer.yahoo.com/yui/editor/
 *
 * Custom toolbar example:
 * http://developer.yahoo.com/yui/examples/editor/toolbar_editor.html
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: integrate image upload

class yui_richedit
{
    private $width      = 500;
    private $height     = 200;
    private $input_name = 'yui_richedit_input';  ///< name of <textarea> to decorate with rich editor
    private $titlebar   = '';
    private $show_dompath = false; //Displays the DOM bar at the bottom

    function setWidth($n) { $this->width = $n; }
    function setHeight($n) { $this->height = $n; }
    function setInputName($s) { $this->input_name = $s; }

    function render()
    {
        $header = XhtmlHeader::getInstance();

        $header->includeCss('http://yui.yahooapis.com/2.8.1/build/assets/skins/sam/skin.css');

        //Utility Dependencies
        $header->includeJs('http://yui.yahooapis.com/2.8.1/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.1/build/element/element-min.js');

        //Needed for Menus, Buttons and Overlays used in the Toolbar
        $header->includeJs('http://yui.yahooapis.com/2.8.1/build/container/container_core-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.1/build/menu/menu-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.1/build/button/button-min.js');

        //Source file for Rich Text Editor
        $header->includeJs('http://yui.yahooapis.com/2.8.1/build/editor/editor-min.js');

        $res =
        'var myEditor = new YAHOO.widget.Editor("'.$this->input_name.'", {'.
            'width: "'.$this->width.'px",'.
            'height: "'.$this->height.'px",'.
            'dompath: '.($this->show_dompath ? 'true' : 'false').','.
            'animate: true,'.      //Animates the opening, closing and moving of Editor windows
            'handleSubmit: true,'. //editor will attach itself to the textareas parent form's submit handler

            'toolbar: {'.
                'titlebar: "'.$this->titlebar.'",'.
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

        return '<script type="text/javascript">'.$res.'</script>';
    }
}

?>
