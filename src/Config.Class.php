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
use patrick115\Sinusbot\Singleton;

class Config extends Error
{

    use Singleton;

    /**
     * Config values
     * 
     * @var array
     */
    private $mem_config = NULL;

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
    private function __construct() {
        if (!file_exists(__DIR__ . "/config/config.php")) {
            $this->catchError("Config file not found.", debug_backtrace());
        } else {
            include self::$configDir;
            $this->mem_config = $config;
            return $config;
        }
    }

    public static function existConfig()
    {
        if (file_exists(self::$configDir)) {
            return true;
        }
        return false;
    }

    /**
     * Get config value
     * 
     * @param string $path Get path from config
     */
    public function getConfig($path)
    {
        $config = $this->mem_config;

        $part = explode("/", $path);

        if (empty($part[1])) {
            return $config[$path];
        }

        for ($i=0; $i < count($part); $i++) { 
            if (empty($config[$part[$i]])) {
                $this->catchError("Can't find {$path} in config!", debug_backtrace());
            }
            $config = $config[$part[$i]];
        }
        return $config;
    }
}