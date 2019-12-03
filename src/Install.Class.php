<?php

/**
 * Installation class for sinusbot
 * 
 * @author    patrick115 <info@patrick115.eu>
 * @copyright ©2019
 * @link      https://patrick115.eu
 * @link      https://github.com/patrick11514
 * @version   0.1.0
 * 
 */

namespace patrick115\Sinusbot;

use patrick115\Sinusbot\Database;
use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Main;

class Install extends Database
{
	/**
     * Contains last error
     * 
     * @var string
     */
    public static $lasterror;



    /**
     * Validate form inputs from 1st part
     * 
     * @param object $arr Contains $_POST informations
     * 
     */
    public static function validate_1($arr)
    {
        if (empty($arr["address"]) || empty($arr["port"]) || empty($arr["username"]) || empty($arr["password"]) || empty($arr["database"]) || empty($arr["prefix"])) {
            self::$lasterror = "Please fill form";
            return false;
        } else if (!is_numeric($arr["port"])) {
            self::$lasterror = "Port must be numeric";
            return false;
        }
        return true;
    }



    /**
     * Validate form inputs from 2nd part
     * 
     * @param object $arr Contains $_POST informations
     * 
     */
    public static function validate_2($arr)
    {
        if (empty($arr["D_Port"]) || empty($arr["Folder"]) || empty($arr["UseDP"])) {
            self::$lasterror = "Please fill form";
            return false;
        } else if (!is_numeric($arr["D_Port"])) {
            self::$lasterror = "Port must be numeric";
            return false;
        } else if ($arr["UseDP"] != "true" && $arr["UseDP"] != "false") {
            self::$lasterror = "Use default port value must be True/False";
            return false;
        } else if ($arr["UseDP"] == "true" && empty($arr["DPassword"])) {
            self::$lasterror = "If you can use default password, you must define it";
            return false;
        }
        return true;
    }


    /**
     * Validate form inputs from 3rd part
     * 
     * @param object $arr Contains $_POST informations
     * 
     */
    public static function validate_3($arr)
    {
        if (empty($arr["address"]) || empty($arr["username"]) || empty($arr["password"])) {
            self::$lasterror = "Please fill form";
            return false;
        }
        return true;
    }


    /**
     * Generate random string
     * 
     * @param int $length Length of random string
     * 
     */
    public static function randomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    /**
     * Check compatibility
     * 
     */
    public static function checkVersion()
    {
        $errors = array();
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $errors[] = "Please use PHP 5.4.0+";
        }
        if (!extension_loaded("mysqli")) {
            $errors[] = "Please install mysqli extension";
        }
        if (!extension_loaded("curl")) {
            $errors[] = "Please install curl extension";
        }
        if (!extension_loaded("gd")) {
            $errors[] = "Please install gd extension";
        }

        if (!empty($errors)) {
            $return = "<pre>There was an error(s) checking the extensions and php version<ul>";
            foreach ($errors as $error) {
                $return .= "<li>{$error}</li>";
            }
            $return .= "</ul></pre>";
            die($return);
        }
    }


    /**
     * Install and prepare administration for
     * use.
     * 
     * @param string $config Contains temp config location
     * 
     */
    public static function Install_bot($config)
    {
        $config = file_get_contents($config);
        #unlink($config);
        $file = fopen(__DIR__ . "/config/config.php", "w");
        fwrite($file, $config);
        fclose($file);

        $content = file_get_contents(__DIR__ . "/installer/sql.txt");
        $content = str_replace(
                    "<%DATABASE%>",
                    Config::getConfig("Database/database"),
        			str_replace(
        				"<%PREFIX%>",
        				Config::getConfig("Database/prefix"),
        				$content
        			)
        		);

        $sql = explode("\n", $content);

        unset($sql[(count($sql) - 1)]);

        foreach ($sql as $command){
        	Database::execute($command);
        }
        
        $content = file_get_contents(__DIR__ . "/installer/commands.txt");

        $content = str_replace(
                    "{%sinusbot_dir%}",
                    Config::getConfig("Bot/folder"),
                    $content
                );
        
        $exec = explode(";", $content);

        unset($exec[(count($exec) - 1)]);

        foreach ($exec as $command) {
            Main::SSHExecute($command);
        }
        
    }
}