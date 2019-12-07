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

use patrick115\Sinusbot\Error;

class Config extends Error
{

    /**
     * Config values
     * 
     * @var array
     */
    private static $config = NULL;

    /**
     * Config directory
     * 
     * @var string
     */
    private static $configDir = __DIR__ . "/config/config.php";

    /**
     * Prevence to construct this function
     * 
     */
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

    /**
     * Get config value
     * 
     * @var string $path Get path from config
     */
    public static function getConfig($path)
    {
        $config = self::loadConfig();

        $part = explode("/", $path);

        if (empty($part[1])) {
            return $config[$path];
        }

        for ($i=0; $i < count($part); $i++) { 
            if (empty($config[$part[$i]])) {
                parent::catchError("Can't find {$path} in config!", debug_backtrace());
            }
            $config = $config[$part[$i]];
        }
        return $config;
    }
}