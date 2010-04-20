<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

require_once('class.CoreBase.php');

class XhtmlMenu extends CoreBase
{
    private $items = array();
    private $class = 'ulli_menu';
    private $class_current = 'ulli_menu_current';

    public function add($title, $link)
    {
        $this->items[] = array('title'=>$title, 'link'=>$link);
    }

    function setCss($class, $current)
    {
        $this->class = $class;
        $this->class_current = $current;
    }

    public function render()
    {
        $cur = $_SERVER['REQUEST_URI'];
        $res = '<ul class="'.$this->class.'">';

        foreach ($this->items as $item)
        {
            $l = strlen($item['link']);
            if ($item['link'] == $cur || ($l > 1 && substr($cur, 0, $l) == $item['link']))
                $res .= '<li class="'.$this->class_current.'">';
            else
                $res .= '<li>';

            if ($item['link']) $res .= '<a href="'.$item['link'].'">'.$item['title'].'</a>';
            else $res .= $item['title'];
            $res .= '</li>';
        }
        $res .= '</ul>';
        return $res;
    }
}

?>
