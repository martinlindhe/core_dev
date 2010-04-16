<?php
/**
 * $Id$
 *
 * Creates and verifies tables against their objects
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: unused mockup, see #32

class DatabaseMySQLCreator
{
    /**
     * Returns true if a database with this name already exists
     */
    function findDatabase($dbname)
    {
        $list = $this->getArray('SHOW DATABASES');

        foreach ($list as $row)
            if ($row['Database'] == $dbname) return true;

        return false;
    }

    function selectDatabase($dbname)
    {
        $this->database = $dbname;
        return $this->db_handle->select_db($this->database);
    }

    function createDatabase($dbname, $charset = 'utf8')
    {
        if ($this->findDatabase($dbname)) return false;

        $q = 'CREATE DATABASE '.$dbname.' CHARACTER SET utf8';
        return $this->query($q);
    }

    /**
     * Returns true if a table with this name already exists
     */
    function findTable($tblname)
    {
        $list = $this->getNumArray('SHOW TABLES FROM '.$this->database);

        foreach ($list as $row)
            if ($row[0] == $tblname) return true;

        return false;
    }

    function createTable($tblname, $layout, $charset = 'utf8')
    {
        $parsed = $this->parseLayout($layout);

        $q = "CREATE TABLE ".$tblname." (\n";
        $key_pri = '';
        foreach ($parsed as $col) {
            $q .= $col['Field'].' '.$col['Type'];
            switch ($col['Null']) {
            case 'NO': $q .= ' NOT NULL'; break;
            case 'YES': $q .= ' NULL'; break;
            }
            $q .= ($col['Default'] ? " default '".$col['Default']."'" : "");
            $q .= ($col['Extra'] ? ' '.$col['Extra'] : '').",\n";

            if ($col['Key'] == 'PRI') $key_pri = $col['Field'];
        }
        if ($key_pri) {
            $q .= "PRIMARY KEY (".$key_pri.")\n";
        }
        $q .= ") ENGINE=MyISAM DEFAULT CHARSET=".$charset."\n";
        return $this->query($q);
    }

    function verifyTable($tblname, $layout, $charset = 'utf8')
    {
        $list = $this->getArray('DESCRIBE '.$this->database.'.'.$tblname);
        if (!$list) return false;

        $parsed = $this->parseLayout($layout);

        if ($list == $parsed) return true;
        return false;
    }

    function parseLayout($layout)
    {
        $res = array();
        $idx = 0;
        foreach ($layout as $name=>$col) {
            $res[$idx]['Field'] = $name;
            $res[$idx]['Type'] = '';
            $res[$idx]['Null'] = 'YES';
            $res[$idx]['Key'] = '';
            $res[$idx]['Default'] = '';
            $res[$idx]['Extra'] = '';
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
                    echo "createTable: unknown prop ".$ex[0]."\n";
                }
            }
            $idx++;
        }
        return $res;
    }

}

?>
