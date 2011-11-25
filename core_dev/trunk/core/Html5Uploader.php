<?php
/**
 * $Id$
 *
 * Wrapper for js/ext/html5uploader.js
 */

class Html5Uploader
{
    /**
     * @param $id album id
     */
    public static function albumUploader($id)
    {
        $header = XhtmlHeader::getInstance();

        $page = XmlDocumentHandler::getInstance();

        $header->includeJs($page->getCoreDevRoot().'js/ext/html5uploader.js');

        $header->embedJsOnload( "new uploader('drop', 'status', '/u/upload/album/".$id."', 'list');");

$header->embedCss(
'   #box {
        background-color: #f60;
        width: 208px;
        border: 1px solid #f60;
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        border-radius: 6px;
        padding-bottom: 10px;
        float: left;
    }
    #box p {
        font-size: 10px;
        padding: 5px;
        margin: 0px;
    }
    #drop {
        width: 208px;
        height: 200px;
        background-color: #f90;
    }
    #status {
        width: 200px;
        height: 25px;
        font-size: 10px;
        color: #fff;
        padding: 5px;
    }
    #list {
        width: 210px;
        font-size: 10px;
        float: left;
        margin-left: 10px;
    }
    .addedIMG {
        width: 100px;
        height: 100px;
    }
    ');
        $res =
        '<div id="box">'.
                '<div id="status">Drag the images from a folder to the area below ...</div>'.
                '<div id="drop"></div>'.
        '</div>'.
        '<div id="list"></div>';

        return $res;
    }

}

?>
