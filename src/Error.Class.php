<?php

/**
 * Main class for sinusbot
 * 
 * @author    patrick115 <info@patrick115.eu>
 * @copyright ©2019
 * @link      https://patrick115.eu
 * @link      https://github.com/patrick11514
 * @version   0.1.0
 * 
 */

namespace patrick115\Sinusbot;

class Error
{

    /**
     * Contains all errors
     * 
     * @var array
     */
    private static $catcherror = NULL;

    /**
     * Contains error time
     * 
     * @var array
     */
    private static $catchtime = NULL;

    /**
     * Catch error
     * 
     * @param string $e Error message
     * @param object $dump Informations about error
     * 
     */
    protected function catchError($e, $dump)
    {   
        $file = $dump[0]["file"];
        $class = $dump[0]["class"];
        $function = $dump[0]["function"];
        $line = $dump[0]["line"];
        self::$catcherror[] = "<b>{$file}({$line}):</b> <i>{$class}::{$function}:</i> " . $e;
        self::$catchtime[] = date("H:i:s");
    }

    /**
     * Return all errors
     * 
     */
    public static function returnError()
    {
        if(self::$catcherror !== NULL) {
            $return = "<pre>";
            $return .= "<b>Errors (" . count(self::$catcherror) . "):</b>" . PHP_EOL;
            foreach (self::$catcherror as $id => $error) {
                $return .= "[" . self::$catchtime[$id] . "] " . $error . PHP_EOL;
            }
            $return .= "</pre>";
            echo $return;
            die();
        }
    }
}