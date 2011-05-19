<?php
/**
 * This is the application handler
 * all webpage requests is sent here from a RewriteRule in .htaccess
 */

require_once('config.php');
require_once('RequestHandler.php');

try {
    $front = RequestHandler::getInstance();
    $front->excludeSession( array('api') ); //exclude session handling for these controllers
    $front->route();

    $page = XmlDocumentHandler::getInstance();
    echo $page->render();
} catch (Exception $e) {
    echo '<pre>';
    echo $e->__toString();
    dp( $e->__toString() ); //because the exception is caught it is not written to error log
    echo '</pre>';
}

?>
