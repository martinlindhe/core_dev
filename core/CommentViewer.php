<?php
/**
 * @author Martin Lindhe, 2009-2011 <martin@ubique.se>
 */

namespace cd;

class CommentViewer
{
    public static function render($type, $owner)
    {
        $view = new ViewModel('views/user/comments.php');
        $view->registerVar('type', $type);
        $view->registerVar('owner', $owner);
        return $view->render();
    }
}
