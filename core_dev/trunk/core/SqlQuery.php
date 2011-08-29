<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

class SqlQuery
{
    var $query;
    var $error;
    var $time;
    var $prepared = false;
    var $format;            ///< f or prepared statements
    var $params;            ///< for prepared statements

    function render()
    {
        $query = htmlentities(nl2br($this->query), ENT_COMPAT, 'UTF-8');

        $keywords = array(
        'SELECT ', 'UPDATE ', 'INSERT ', 'DELETE ',
        ' FROM ', ' SET ', ' WHERE ',
        ' LEFT JOIN ', ' LEFT OUTER JOIN ', ' INNER JOIN ',
        ' GROUP BY ', ' ORDER BY ',
        ' ON ', ' AS ', ' AND ', ' OR ', ' LIMIT ', ' BETWEEN ',
        ' IS NULL', ' IS NOT NULL', ' DESC', ' ASC',
        ' != ',
        'NOW()', ' DATE(',
        ' COUNT(', ' SUM(',
        );

        $decorated = array(
        '<b>SELECT</b> ', '<b>UPDATE</b> ', '<b>INSERT</b> ', '<b>DELETE</b> ',
        ' <b>FROM</b> ', '<br/><b>SET</b> ', '<br/><b>WHERE</b> ',
        '<br/><b>LEFT JOIN</b> ', '<br/><b>LEFT OUTER JOIN</b> ', '<br/><b>INNER JOIN</b> ',
        '<br/><b>GROUP BY</b> ', '<br/><b>ORDER BY</b> ',
        ' <b>ON</b> ', ' <b>AS</b> ', ' <b>AND</b> ', ' <b>OR</b> ', ' <b>LIMIT</b> ', ' <b>BETWEEN</b> ',
        ' <b>IS NULL</b>', ' <b>IS NOT NULL</b>', ' <b>DESC</b>', ' <b>ASC</b>',
        ' <b>!=</b> ',
        '<b>NOW()</b>', ' <b>DATE</b>(',
        ' <b>COUNT</b>(', ' <b>SUM</b>(',
        );

        $query = str_replace($keywords, $decorated, $query);

        $css =
        'overflow:auto;'.
        'max-width:400px;'.
        'max-height:100px;';

        $res =
        '<div style="'.$css.'">'.
        '<table summary="" class="hover" width="100%" cellpadding="0">'.
        '<tr><td width="30"'.($this->prepared ? ' style="background-color:#B1F9AA"': '').'>';

        if ($this->error)
            $res .= coreButton('Error', '', 'SQL Error');
        else
            $res .= round($this->time, 2).'s';

        $res .=  '</td><td>';

        if ($this->error) {
            $error = true;
            $res .=  'Error: <b>'.$this->error.'</b><br/><br/>';
        }

        $res .= $query;

        if ($this->format) $res .= ' ('.$this->format.')';
        if ($this->params) $res .= ': '.implode(', ', $this->params);

        $res .=
        '</td></tr></table>'.
        '</div>';
        return $res;
    }

}

?>
