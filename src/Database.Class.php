<?php

namespace patrick115\Sinusbot;

use mysqli;

class Database
{

    private static $conn = NULL;

    public static $error;

    protected static function Connect()
    {
        if (self::$conn === NULL) {
            include __DIR__ . "/config/config.php";

            $config = $cfg["database"];

            $conn = new mysqli($config["address"] . ":" . $config["port"], $config["username"], $config["password"]);
            $conn->set_charset("utf8mb4");

            if (isset($conn->connect_error)) {
                die("<b>MYSQLI Error:</b>" . self::errorConvert($conn->connect_error));
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
        $list = "`";
        for ($i=0; $i < count($params) - 1; $i++) { 
            $list .= $params[$i] . ", ";
        }
        $list .= $params[count($params) - 1];
        if ($haystack === NULL || $needle === NULL) {
            $command = "SELECT $list FROM `$table`";
        } else {
            $command = "SELECT $list FROM `$table` WHERE `$haystack` = '$needle';";
        }
        $return = self::$conn->query($command);
        return $return;
    }

    public static function execute($sql)
    {
        self::$conn->query($sql);
        if (!empty(self::$conn->error)) {
            die("<b>MYSQLI Error:</b> <i>Error while executing {$sql}</i>: " . self::$conn->error);
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
