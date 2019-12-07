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
use patrick115\Sinusbot\Config;
use patrick115\Sinusbot\Main;
use mysqli;
use Exception;

class Database extends Error
{

    /**
     * Connection to database
     * 
     * @var object
     */
    private static $conn = NULL;

    /**
     * Status of connection
     * 
     * @var boolean
     */
    public static $connected = NULL;

    /**
     * Contains last database error
     * 
     * @var string
     */
    public static $error;

    /**
     * Table prefix
     * 
     * @var string
     */
    private static $table_prefix;


    /**
     * Connect to database
     * 
     */
    protected static function Connect()
    {
        if (self::$conn === NULL) {

            self::$table_prefix = Config::getConfig("Database/prefix");

            $conn = new mysqli(
                Config::getConfig("Database/address")
                 . ":" . 
                Config::getConfig("Database/port"), 
                Config::getConfig("Database/username"), 
                Config::getConfig("Database/password"), 
                Config::getConfig("Database/database")
            );
            $conn->set_charset("utf8mb4");

            if (isset($conn->connect_error)) {
                parent::catchError(self::errorConvert($conn->connect_error), debug_backtrace());
            }

            self::$conn = $conn;
            self::$connected = true;
        }
    }

    /**
     * Remove MYSQLInsert characters
     * 
     * @param string $string String
     * 
     */
    protected static function removeChars($string)
    {
        self::$conn->real_escape_string($string);
        return $string;
    }

    /**
     * Catch error
     * 
     * @param string $error Error to convert
     * 
     */
    private static function errorConvert($error)
    {
        $uperror = $error . PHP_EOL;
        $error = strtolower($error);
        if ($error == "php_network_getaddresses: getaddrinfo failed: no address associated with hostname") {
            $return = "Undefined host.";
        } else if (strpos($error, "access denied for user ") !== false) {
            $return = "Incorrect Login Data";
        } else if ($error == "php_network_getaddresses: getaddrinfo failed: no address associated with hostname") {
            $return = "Please use valid host";
        } else if ($error == "php_network_getaddresses: getaddrinfo failed: name or service not known") {
            $return = "Please use valid host";
        } else if (strpos($error, "unknown database") !== false) {
            $return = "Database " . explode("'", $uperror)[1] . " not found! Please create it";
        }
        return $return;
    }

    /**
     * Select rows from table
     * 
     * @param array $param Selected rows
     * @param string $table Table
     * @param string $option Option like LIMIT 1..
     * @param string $haystack 
     * @param string $needle
     * 
     */
    public static function select($params, $table, $options = "", $haystack = NULL, $needle = NULL)
    {
        self::Connect();

        $table = self::convertTableName($table);

        if (empty($params) || empty($table)) {
            parent::catchError("Empty parameter(s).", debug_backtrace());
        }
        $list = "";
        for ($i=0; $i < count($params) - 1; $i++) { 
            $list .= "`" . $params[$i] . "`, ";
        }
        $list .= "`" . $params[count($params) - 1] . "`";
        if ($haystack === NULL && $needle === NULL) {
            $command = "SELECT $list FROM `$table` $options";
        } else {
            $command = "SELECT $list FROM `$table` WHERE `$haystack` = '$needle' $options"; 
        }
        try {
            $return = self::$conn->query($command);
        } catch (Exception $e) {
            $error = $e->getMessage();
            parent::catchError($error, debug_backtrace());
        }
        return $return;
    }

    /**
     * Execute sql command
     * 
     * @param string $sql Sql command
     * @param boolean $return return result
     * 
     */
    public static function execute($sql, $return = false)
    {
        self::Connect();
        $return = self::$conn->query(Main::Chars($sql));
        if (!empty(self::$conn->error)) {
            parent::catchError(self::$conn->error, debug_backtrace());
        }
        if ($return === true) {
            return $return;
        }
    }

    /**
     * Check database connection
     * 
     * @param string $address  Address
     * @param int    $port     Port
     * @param string $username Username
     * @param string $password Password
     * @param string $database Database
     * 
     */
    public static function checkConnection($address, $port, $username, $password, $database)
    {

        $conn = new mysqli($address . ":" . $port, $username, $password);
        $conn->query("USE {$database};");
        if (!empty($conn->error)) {
            echo $conn->error;
            self::$error = self::errorConvert($conn->error);
            return false;
        }
        $conn->set_charset("utf8mb4");
        if (isset($conn->connect_error)) {
            self::$error = self::errorConvert($conn->connect_error);
            return false;
        }
        return true;
    }

    /**
     * Add table prefix to table
     * 
     * @param string $table Table
     * 
     */
    public static function convertTableName($table)
    {
        if (strpos($table, Config::getConfig("Database/prefix")) === false) {
            return Config::getConfig("Database/prefix") . $table;
        } else {
            parent::catchError("Converted table, contains table prefix.", debug_backtrace());
        }
    }
}
