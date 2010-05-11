<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: integrate captcha from Comments_borked.php
//TODO: pagination? or auto-hide some comments

//TODO LATER: delete atom_comments.php, Comments_borked.php

require_once('User.php');

class CommentList
{
    private $type;
    private $owner;

    function __construct($type = 0)
    {
        $this->setType($type);
    }

    function setOwner($n) { if (is_numeric($n)) $this->owner = $n; }
    function setType($n) { if (is_numeric($n)) $this->type = $n; }

    function get()
    {
        $db = SqlHandler::getInstance();

        $q  = 'SELECT * FROM tblComments';

        $cond = array();
        if ($this->owner)
            $cond[] = 'ownerId='.$this->owner;

        if ($this->type)
            $cond[] = 'commentType='.$this->type;

        if ($cond)
            $q .= ' WHERE '.implode(' AND ', $cond);

        return $db->getArray($q);
    }

    /**
     * Handles form POST
     */
    function handleSubmit($p, $caller)
    {
        $session = SessionHandler::getInstance();

        if (empty($p['comment_'.$this->type]))
            return false;

        if (!$session->id) {
            $caller->setError('Unauthorized submit');
            return false;
        }

        $db = SqlHandler::getInstance();

        $ip_num = IPv4_to_GeoIP(client_ip());

        $q = 'INSERT INTO tblComments SET ownerId='.$this->owner;
        $q .= ',commentType='.$this->type.',commentText="'.$db->escape($p['comment_'.$this->type]).'"';
        $q .= ',timeCreated=NOW(),userId='.$session->id;
        $q .= ',userIP='.$ip_num;

        unset($p['comment_'.$this->type]);

        return $db->insert($q);
    }

    function render()
    {
        $session = SessionHandler::getInstance();

        $res = '';
        foreach ($this->get() as $c)
        {
            $user = new User($c['userId']);
            $res .= $user->render().' wrote: ';

            $res .= nl2br($c['commentText']).'<br/>';

            $res .= sql_date($c['timeCreated']); //XXX snygga till
            $res .= '<hr/>';
        }

        if ($session->id) {

            //XXX ajax submit + js update form. den kräver nu en page reload för att visa senaste inlägg
            $form = new XhtmlForm('addcomment');
            $form->addRichedit('comment_'.$this->type, t('Write a comment') );

            $form->addSubmit('Save');
            $form->setHandler('handleSubmit', $this);

            $res .= $form->render();
        }

        return $res;
    }
}

?>
