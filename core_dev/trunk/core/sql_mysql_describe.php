<?php
/**
 * $Id$
 *
 * Describes a MySQL database as a series of SQL commands
 * Useful for exporting a project's table structure
 */

//STATUS: wip

class MysqlDescribe
{
    private $database;
    function setDatabase($s) { $this->database = $s; }

    /**
     * Returns true if a table with this name already exists
     */
    function getTables()
    {
        $db = SqlHandler::getInstance();

        $q = 'SHOW TABLES FROM '.$db->escape($this->database);
        return $db->getNumArray($q);
    }

    function getTableDetails($tblname)
    {
        $db = SqlHandler::getInstance();

        $list = $db->getArray('DESCRIBE '.$db->escape($this->database).'.'.$db->escape($tblname));
        if (!$list) return false;

        return $list;
    }

    function renderTable($tbl)
    {
        $key_pri = '';
        $res = 'CREATE TABLE `'.$tbl.'` ('."\n";
        foreach ($this->getTableDetails($tbl) as $l) {

            if ($l['Extra'] && $l['Extra'] != 'auto_increment')
                throw new Exception ('TODO handle extra data: '.$l['Extra']);
            $res .= '  `'.$l['Field'].'` '.$l['Type'];
            $res .= ($l['Null'] == 'NO' ? ' NOT NULL' : '');
            if ($l['Extra'] && $l['Extra'] == 'auto_increment')
                $res .= ' AUTO_INCREMENT';

            if ($l['Key'] == 'PRI')
                $key_pri = $l['Field'];

            if ($l['Default'] !== false) $res .= " DEFAULT '".$l['Default']."'";
            $res .= ",\n";
        }
        if ($key_pri)
            $res .= "  PRIMARY KEY (".$key_pri.")\n";
        $res .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
        return $res;
    }

    function renderDatabase()
    {
        $q = 'CREATE DATABASE `'.$this->database."` CHARACTER SET utf8\n";

        foreach ($this->getTables() as $idx => $tbl)
            $q .= $this->renderTable($tbl);

        return $q;
    }


/*
    private function parseLayout($layout)
    {
        return $layout;
        //
        $res = array();
        $idx = 0;
        foreach ($layout as $name=>$col) {
            //XXXX parse layout to a table_layout object
            $res[$idx]['Field'] = $name;
            $res[$idx]['Type'] = '';
            $res[$idx]['Null'] = 'YES';
            $res[$idx]['Key'] = '';
            $res[$idx]['Default'] = '';
            $res[$idx]['Extra'] = '';
            d( $col);die;
            foreach ($col as $prop) {
                $ex = explode(':',$prop);

                switch ($ex[0]) {
                case 'key':
                    $res[$idx]['Key'] = 'PRI';
                    break;

                case 'extra':
                    $res[$idx]['Extra'] = $ex[1];
                    break;

                case 'default':
                    $res[$idx]['Default'] = $ex[1];
                    break;

                case 'null':
                    $res[$idx]['Null'] = $ex[1];
                    break;

                case 'datetime':
                case 'text':
                case 'smallint':
                case 'tinyint':
                case 'bigint':
                    $res[$idx]['Type'] = $ex[0].(!empty($ex[1]) ? '('.$ex[1].')' : '').(!empty($ex[2]) ? ' '.$ex[2] : '');
                    break;

                default:
                    echo "parseLayout: unknown prop ".$ex[0]."\n";
                }
            }
            $idx++;
        }
        return $res;
    }
*/

}

?>
