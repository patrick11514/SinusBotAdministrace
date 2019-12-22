<?php
/**
 * Session class
 * 
 * @author    patrick115 <info@patrick115.eu>
 * @copyright ©2019
 * @link      https://patrick115.eu
 * @link      https://github.com/patrick11514
 * @version   0.1.0
 * 
 */

namespace patrick115\Sinusbot;

use patrick115\Sinusbot\Error;

class Session extends Error
{
    private static $Session = NULL;

    public static function sessionStatus()
    {
        if (self::$Session === NULL) {
            return false;
        }
        return true;
    }

    public static function sessionStart()
    {
        if (self::$Session === NULL) {
            session_start();
            session_regenerate_id(true);
            self::$Session = true;
        } else {
            parent::catchError("Session is already opened!", debug_backtrace());
        }
    }

    public static function closeSession()
    {
        if (self::$Session === NULL) {
            parent::catchError("Can't close session, if session is not started!", debug_backtrace());
            return;
        }
        session_regenerate_id(true);
        session_unset();
        session_destroy();
        session_write_close();
        setcookie(session_name(), '', 0, '/');
    }

    public static function get($param)
    {
        if (isset($_SESSION[$param])) {
            return $_SESSION[$param];
        }
        return false;
    }
}