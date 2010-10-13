<?php
/**
 * $Id$
 *
 * Tool to view, modify and delete a user
 */

//STATUS: wip

class UserEditor
{
    private $id;

    function __construct($n = 0)
    {
        if ($n)
            $this->setId($n);
    }

    function setId($n) { if (is_numeric($n)) $this->id = $n; }

    function getId() { return $this->id; }

    function render()
    {
        $view = new ViewModel('views/admin_usereditor.php', $this);
        return $view->render();
    }

}

?>
