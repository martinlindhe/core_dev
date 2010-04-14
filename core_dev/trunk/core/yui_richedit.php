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

//FIXME: textarea is initially shown as huge before the javascript fixes its size, it looks ugly while the page is loading
//TODO: integrate with image upload

class yui_richedit
{
    private $width      = 500;
    private $height     = 200;
    private $input_name = 'yui_richedit_input';
    private $titlebar   = '';
    private $show_dompath = false; //Displays the DOM bar at the bottom

    function setWidth($n) { $this->width = $n; }
    function setHeight($n) { $this->height = $n; }
    function setInputName($s) { $this->input_name = $s; }

    function render()
    {
        $res =
        '<script type="text/javascript">'.
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
        'myEditor.render();'.
        '</script>';

        return $res;
    }
}

?>
