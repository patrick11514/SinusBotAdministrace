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

    private static function sessionStatus()
    {
        if (session_status() === PHP_SESSION_NONE) {
            return false;
        }
        return true;
    }

    public static function destroy()
    {
        if (!self::sessionStatus()) {
            self::init()->catchError("Session is not started!", debug_backtrace());
            return;
        }
        @session_unset();
        @session_destroy();
        @session_write_close();
        @setcookie(session_name(), '', 0, '/');
        @session_regenerate_id(true);
    }

    public static function get($param)
    {
        if (!self::sessionStatus()) {
            self::init()->catchError("Session is not started!", debug_backtrace());
            return;
        }

        if (isset($_SESSION[$param])) {
            return $_SESSION[$param];
        }
        return false;
    }

    public static function put($param, $value)
    {
        if (!self::sessionStatus()) {
            self::init()->catchError("Session is not started!", debug_backtrace());
            return;
        }

        if (is_array($param)) {
            if (!is_array($value)) {
                self::init()->catchError("If param is array, value must be too.", debug_backtrace());
            }
        }
        if (is_array($value)) {
            if (!is_array($param)) {
                self::init()->catchError("If param is array, value must be too.", debug_backtrace());
            }
        }
        if (count($param) !== count($value)) {
            self::init()->catchError("Param and Value must have same count", debug_backtrace());
        }

        if (is_array($param)) {
            for ($i = 0; $i <= (count($param) - 1); $i++) {
                $_SESSION[$param[$i]] = $value[$i];
            }
        } else {
            $_SESSION[$param] = $value;
        }

    }
}