<?php

namespace patrick115\Sinusbot;

use patrick115\Sinusbot\Config;
use mysqli;
use Exception;

class Database
{

    private static $conn = NULL;

    public static $error;

    private static $table_prefix;

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
                die("<b>MYSQLI Error: </b><br>" . self::errorConvert($conn->connect_error));
            }

            self::$conn = $conn;
        }
    }

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

    public static function select($params, $table, $haystack = NULL, $needle = NULL)
    {
        self::Connect();

        $table = self::$table_prefix . $table;

        if (empty($params) && empty($table)) {
            die("<b>MYSQLI Error:</b><br><i>Function patrick115\Sinusbot\Database\select:</i> empty parameter.<br>");
        }
        $list = "";
        for ($i=0; $i < count($params) - 1; $i++) { 
            $list .= "`" . $params[$i] . "`, ";
        }
        $list .= "`" . $params[count($params) - 1] . "`";
        if ($haystack === NULL && $needle === NULL) {
            $command = "SELECT $list FROM `$table`";
        } else {
            $command = "SELECT $list FROM `$table` WHERE `$haystack` = '$needle';";
        }
        try {
            $return = self::$conn->query($command);
        } catch (Exception $e) {
            $error = $e->getMessage();
            die("<b>MYSQLI Error:</b><br><i>Function patrick115\Sinusbot\Database\select:</i> " . $error . "<br>");
        }
        return $return;
    }

    public static function execute($sql)
    {
        self::Connect();
        self::$conn->query($sql);
        if (!empty(self::$conn->error)) {
            die("<b>MYSQLI Error:</b><br><i>Error while executing {$sql}</i>: " . self::$conn->error . "<br>");
        }
    }

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
}
