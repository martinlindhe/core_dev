<?php
/**
 * $Id$
 *
 * Session storage handler for keeping session data in database
 *
 * Orginally based on code from
 * http://www.mt-soft.com.ar/2007/12/21/using-a-mysql-database-to-store-session-data/
 */

//STATUS: wip

namespace cd;

class SessionStorageHandler
{
    private $expire_time;

    public function __construct()
    {
        $session = SessionHandler::getInstance();

        $this->expire_time = $session->getTimeout();

        session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "gc")
        );

        register_shutdown_function('session_write_close');
    }

    public function open($save_path, $session_name)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        $q = 'SELECT session_data FROM tblSessionData WHERE session_id = ? AND expires > ?';
        return Sql::pSelectItem($q, 'ss', $id, sql_datetime( time() ) );
    }

    public function write($id, $data)
    {
        $time = time() + $this->expire_time;

        $q = 'REPLACE tblSessionData (session_id,session_data,expires) VALUES(?, ?, ?)';
        Sql::pUpdate($q, 'sss', $id, $data, sql_datetime($time) );
        return true;
    }

    public function destroy($id)
    {
        $q = 'DELETE FROM tblSessionData WHERE session_id = ?';
        Sql::pDelete($q, 's', $id);
        return true;
    }

    /** Garbage Collection */
    public function gc($maxlifetime)
    {
        $q = 'DELETE FROM tblSessionData WHERE expires < NOW()';
        Sql::pDelete($q);
        return true;
    }

}

?>
