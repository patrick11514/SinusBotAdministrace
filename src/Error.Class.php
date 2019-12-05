<?php

namespace patrick115\Sinusbot;

class Error
{
    private static $catcherror = NULL;

    private static $catchtime = NULL;

    protected function catchError($e, $dump)
    {   
        $file = $dump[0]["file"];
        $class = $dump[0]["class"];
        $function = $dump[0]["function"];
        $line = $dump[0]["line"];
        self::$catcherror[] = "<b>{$file}({$line}):</b> <i>{$class}::{$function}:</i> " . $e;
        self::$catchtime[] = date("H:i:s");
    }

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