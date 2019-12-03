<?php

namespace patrick115\Sinusbot;

class Config {

    private static $config = NULL;

    private static $configDir = __DIR__ . "/config/config.php";

    private function __construct() {}

    private static function loadConfig()
    {
        if (self::$config === NULL) {
        	if (!file_exists(__DIR__ . "/config/config.php")) {
        		die("<b>Config Errror:</b><br><i>While loading config:</i> Config file not found.");
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
                die("<b>Config Errror:</b><br><i>While reading {$path}: </i> Can't find value in config!<br>");
            }
            $return = $return[$part[$i]];
        }
        return $return;
    }
}