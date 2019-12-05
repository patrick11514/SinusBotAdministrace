<?php

namespace patrick115\Sinusbot;

use patrick115\Sinusbot\Error;

class Config extends Error
{

    private static $config = NULL;

    private static $configDir = __DIR__ . "/config/config.php";

    private function __construct() {}

    private static function loadConfig()
    {
        if (self::$config === NULL) {
        	if (!file_exists(__DIR__ . "/config/config.php")) {
                parent::catchError("Config file not found.", debug_backtrace());
        	}
            include self::$configDir;
            self::$config = $config;
        }
        return self::$config;
    }

    public static function getConfig($path)
    {
        $config = self::loadConfig();

        $part = explode("/", $path);

        if (empty($part[1])) {
            return $config[$path];
        }

        $return = $config;

        for ($i=0; $i < count($part); $i++) { 
            if (empty($return[$part[$i]])) {
                parent::catchError("Can't find {$path} in config!", debug_backtrace());
            }
            $return = $return[$part[$i]];
        }
        return $return;
    }
}