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

class Session
{
    private static $Session = NULL;

    public static function sessionStatus()
    {
        if (self::$Session === NULL) {
            return false;
        }
        return true;
    }
}